<?php

function add_points($id, $type, $points_to_add) {
	global $wpdb;
    $table_name = $wpdb->prefix . "userank_points";

	$points_row = $wpdb->get_row( "SELECT * FROM $table_name WHERE rankable_id = $id AND rankable_type = '$type' AND date = CURDATE()" );
	if (!!($points_row)) {
		// update record
		$new_points = $points_row->points + $points_to_add;
		$wpdb->update($table_name, array('points' => $new_points), array('id' =>$points_row->id));
	} else {
		// new record
		$attributes = array(
			'date' => date("Y-m-d"),
			'points' => $points_to_add,
			'rankable_id' => $id,
			'rankable_type' => $type
		);
		$wpdb->insert($table_name, $attributes);
	}
}

function points_for_post_creation($post_id, $post, $update) {
	$user_id = $post->post_author;
	if ($update || !!(wp_is_post_revision($post_id)) || $user_id == 0 || is_super_admin($user_id))
		return null;

	add_points($user_id, 'user', 10);
}

function points_for_comment_creation($comment_id, $comment_approved, $comment_data) {
	$user_id = $comment_data['user_id'];
	$comment_points = strlen($comment_data['comment_content']) > 150 ? 6 : 3;
	$post_id = $comment_data['comment_post_ID'];
	$post = get_post($post_id);
	$post_author_id = $post->post_author;

	if ($user_id == 0 || is_super_admin($user_id) || $user_id == $post_author_id)
		return null;

	add_points($user_id, 'user', $comment_points);

	if ($post_author_id != 0 && !is_super_admin($post_author_id)) {
		add_points($post_id, 'post', $comment_points);
		add_points($author_id, 'user', $comment_points);
	}

}

add_action('save_post', 'points_for_post_creation', 11, 3);
add_action('comment_post', 'points_for_comment_creation', 11, 3)
?>
