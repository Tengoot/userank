<?php
function cronstarter_activation() {
	if( !wp_next_scheduled( 'userank_schedule' ) ) {  
		wp_schedule_event( time(), 'hourly', 'userank_schedule' );  
	}
}

function cronstarter_deactivate() {	
	$timestamp = wp_next_scheduled ('userank_schedule');
	wp_unschedule_event ($timestamp, 'userank_schedule');
}

function find_value_in_userank_option_array($array, $int_value) {
	$point_threshold = -1;
	$result = null;

	foreach($array as $single_option) {
		$option_array = explode('-', $single_option);
		$min_points = intval($option_array[0]);
		$possible_result = $option_array[1];

		if($min_points <= $int_value && $min_points > $point_threshold) {
			$result = $possible_result;
			$point_threshold = $min_points;
		}
	}

	return $result;
}

function apply_ranks_and_colors() {
	global $wpdb;
	$table_name = $wpdb->prefix . "userank_points";
	$user_table_name = $wpdb->prefix . "users";
	$points_rows = $wpdb->get_results( "SELECT DISTINCT($user_table_name.ID) AS user_id, SUM($table_name.points) AS points FROM $table_name INNER JOIN $user_table_name ON $table_name.rankable_id = $user_table_name.ID WHERE rankable_type = 'user' AND $table_name.date BETWEEN DATE_SUB(NOW(), INTERVAL 1 YEAR) AND NOW() GROUP BY $user_table_name.ID ORDER BY SUM($table_name.points)");
	
	$colors_options = get_option('colors');
	$ranks_options = get_option('ranks');

	if ($colors_options && $ranks_options) {
		$parsed_colors = explode(', ', $colors_options);
		$parsed_ranks = explode(', ', $ranks_options);
		
		foreach ( $points_rows as $points_row ) {
			$points_value = intval($points_row->points);
			$color = find_value_in_userank_option_array($parsed_colors, $points_value);
			$rank = find_value_in_userank_option_array($parsed_ranks, $points_value);
			
			if (!is_null($color) && !is_null($rank)) {
				update_user_meta($points_row->user_id, 'nickname_color', $color);
				update_user_meta($points_row->user_id, 'rank', $rank);
			}
		}
	}
}

add_action ('userank_schedule', 'apply_ranks_and_colors');

add_action('wp', 'cronstarter_activation');
register_deactivation_hook (__FILE__, 'cronstarter_deactivate');
?>
