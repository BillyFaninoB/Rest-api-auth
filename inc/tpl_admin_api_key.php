<?php 

global $wpdb;
if (!is_multisite()) {
    $site_id = 0;
} else {
    $site_id = get_current_blog_id();
}
$key = '';
if(isset($_POST['api_key'])){
	$query  = $wpdb->get_results("SELECT * FROM api_key_table WHERE blog_id = " . $site_id );
	if( count($query) == 0 ){
		$wpdb->insert('api_key_table',
			array(
				'api_key' => $_POST['api_key'],
				'blog_id' => $site_id,
			)
		);
	}else{
		$id = $query[0]->id;
		$wpdb->update('api_key_table',
			array(
				'api_key' => $_POST['api_key'],
			),
			array('id' => $id )
		);
	}
	$key = $_POST['api_key'];
}else{
	$query  = $wpdb->get_results("SELECT * FROM api_key_table WHERE blog_id = " . $site_id );
	if( count($query) > 0 ){
		$key = $query[0]->api_key;
	}
}
?>

<?php if( isset($_POST['api_key']) ) : ?>
	<div class="success-msg">
		<p>API Key Changed!!</p>
	</div>
<?php endif; ?>

<div class="container">
	<h2>REST API - API KEY</h2>
	<form method="POST" action="">
		<input type="text" name="api_key" value="<?php echo $key; ?>">
		<input type="submit" name="submit" value="Submit">
	</form>
</div>