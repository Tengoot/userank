<?php

function get_user_id_by_display_name( $display_name ) {
    global $wpdb;

    if ( ! $user = $wpdb->get_row( $wpdb->prepare(
        "SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $display_name
    ) ) )
        return false;

    return $user->ID;
}

function userank_filter_author($display_name) {
	$color = get_option('guest_nickname_color');
	$rank = get_option('guest_rank');
	$user_id = get_user_id_by_display_name($display_name);
	if ($user_id) {
		if (user_can($user_id, 'manage_options')) {
			$color = get_option('admin_nickname_color');
			$rank = get_option('admin_rank');				
		} else {
			$color = get_user_meta($user_id, 'nickname_color', true);
			$rank = get_user_meta($user_id, 'rank', true);
		}
	}
	return "<div class='Userank-user-block'><span style='color: $color;'>$display_name</span><span class='Userank-user-rank-smol'>$rank</span></div>";
}

add_filter('the_author', 'userank_filter_author', 10, 1);
add_filter('get_comment_author', 'userank_filter_author', 10, 1);
?>
