<?php
function userank_filter_author($display_name) {
	// TODO: find user and fetch color from meta. Remember about fallback
	$user = get_user_by('login', $display_name);
	$color = get_user_meta($user->ID, 'nickname_color', true);
	return "<span style='color: $color;'>$display_name</span>";
}

add_filter('the_author', 'userank_filter_author', 10, 1);
add_filter('get_comment_author', 'userank_filter_author', 10, 1);
?>
