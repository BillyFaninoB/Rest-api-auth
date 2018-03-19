<?php
/**
 * Plugin Name: REST API auth
 * Description: -
 * Author: -
 * Author URI: -
 * Version: 0.1
 * Plugin URI: -
 */

class Rest_Api_Key{

	public function construct(){
		add_action('admin_menu', [$this, 'add_menu']);
	}

	public static function add_menu(){
		add_menu_page('API Key', 'API Key', 'manage_options', 'api_key', ['Rest_Api_Key', 'adminApiKey'], 'dashicons-book', 15);
	}

	public static function adminApiKey()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        include 'inc/tpl_admin_api_key.php';
    }

	public function install_db(){
		global $wpdb, $rvbook_db_version;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql = "CREATE TABLE IF NOT EXISTS `api_key_table` (
	          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	          `api_key` varchar(100) DEFAULT NULL,
	          `blog_id` int(10) DEFAULT NULL,
	          PRIMARY KEY (`id`)
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		dbDelta($sql);
	}

	public static function check_api_key( $result ){
		
		global $wpdb;

		if (!is_multisite()) {
		    $site_id = 0;
		} else {
		    $site_id = get_current_blog_id();
		}

	    if ( ! empty( $result ) ) {
	        return $result;
	    }

	    $headers = getallheaders();
	    $query  = $wpdb->get_results("SELECT * FROM api_key_table WHERE blog_id = " . $site_id );
	    $api_key = 'no key defined';
	    if( count($query) > 0 ){
	    	$api_key = $query[0]->api_key;
	    }

	    // api key check
	    if( $headers['api_key'] != $api_key )  
	        return new WP_Error( 'rest_wrong_api_key', 'Wrong API Key.', array( 'status' => 401 ) );
	    
    	return $result;
    }
}

add_action('admin_menu', array('Rest_Api_key', 'add_menu'));
register_activation_hook(__FILE__, array('Rest_Api_key','install_db'));
add_filter('rest_dispatch_request', array('Rest_Api_key','check_api_key') , 10, 4 );