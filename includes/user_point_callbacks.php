<?php

function add_points($user_id, $points_to_add) {
	global $wpdb;
    $table_name = $wpdb->prefix . "userank_user_points";

	$points_row = $wpdb->get_row( "SELECT * FROM $table_name WHERE user_id = $user_id AND date = CURDATE()" );
	if (!!($points_row)) {
		// update record
		$new_points = $points_row->$points + $points_to_add;
		$wpdb->update($table_name, array('points' => $new_points), array('ID' =>$points_row->ID));
	} else {
		// new record
		$attributes = array(
			'date' => date("Y-m-d"),
			'points' => $points_to_add,
			'user_id' => $user_id,
		);
		$wpdb->insert($table_name, $attributes);
	}
}

function points_for_post_creation($post_id, $post, $update) {
	$user_id = $post->post_author;
	if ($update || !!(wp_is_post_revision($post_id)) || $user_id == 0 || is_super_admin($user_id))
		return null;

	add_points($user_id, 10);
}

add_action('save_post', 'points_for_post_creation', 11, 3);
?>
