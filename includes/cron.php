<?php
// TODO: Remove that and change default schedule to hourl
function cron_add_minute( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['everyminute'] = array(
	    'interval' => 60,
	    'display' => __( 'Once Every Minute' )
    );
    return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_minute' );

function cronstarter_activation() {
	if( !wp_next_scheduled( 'userank_schedule' ) ) {  
		wp_schedule_event( time(), 'everyminute', 'userank_schedule' );  
	}
}

function cronstarter_deactivate() {	
	$timestamp = wp_next_scheduled ('userank_schedule');
	wp_unschedule_event ($timestamp, 'userank_schedule');
}

function apply_ranks_and_colors() {
	// TODO: Define stuff
}

add_action('wp', 'cronstarter_activation');
register_deactivation_hook (__FILE__, 'cronstarter_deactivate');

add_action ('userank_schedule', 'apply_ranks_and_colors');
?>
