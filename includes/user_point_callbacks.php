<?php

function add_points($id, $type, $points_to_add) {
	global $wpdb;
    $table_name = $wpdb->prefix . "userank_points";
	
	if (is_null($id) || is_null($type) || is_null($points_to_add))
		return false;

	$points_row = $wpdb->get_row( "SELECT * FROM $table_name WHERE rankable_id = $id AND rankable_type = '$type' AND date = CURDATE()" );
	if (is_null($points_row)) {
		// new record
		$attributes = array(
			'date' => date("Y-m-d"),
			'points' => $points_to_add,
			'rankable_id' => $id,
			'rankable_type' => $type
		);
		$wpdb->insert($table_name, $attributes);
	} else {
		// update record
		$new_points = $points_row->points + $points_to_add;
		$wpdb->update($table_name, array('points' => $new_points), array('id' =>$points_row->id));
	}

	return true;
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

// For now, this hook depends on post-ratings plugin, check of rating spam depends on other plugin.
// FOR NOW! Future implementation may include custom post rating system.
function points_for_post_rating($meta_id, $post_id, $meta_key, $meta_value) {
	if ($meta_key != 'rating' && !is_user_logged_in()) {
		return null;
	}
	
	$voted = (int) $_REQUEST['rate'];
	$user = wp_get_current_user();
	$points = null;
	$post = get_post($post_id);
	$post_author_id = $post->post_author;

	switch($voted) {
		case 1:
			$points = -5;
		case 2:
			$points = -3;
		case 3:
			$points = 1;
		case 4:
			$points = 5;
		case 5:
			$points = 10;
	}

	if (!is_null($points)) {
		add_points($post_id, 'post', $points);
		add_points($user->id, 'user', 1);
		if ($post_author_id != 0 && !is_super_admin($post_author_id)) {
			add_points($post_author_id, 'user', $points);
		}
	}
}

add_action('save_post', 'points_for_post_creation', 11, 3);
add_action('comment_post', 'points_for_comment_creation', 11, 3);
add_action('updated_post_meta', 'points_for_post_rating', 10, 4);
add_action('added_post_meta', 'points_for_post_rating', 10, 4);
?>
