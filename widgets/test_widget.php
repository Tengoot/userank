<?php
/*
Plugin Name: Userank user ranking widget
Plugin URI: https://github.com/Tengoot/userank
Description: This plugin adds a custom widget for user ranking.
Version: 1.0
Author: Tengoot
Author URI: https://github.com/Tengoot
*/

class User_Ranking_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'user_ranking_widget',
			__( 'User Ranking Widget', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	public function form( $instance ) {
		$defaults = array(
			'title' => '',
			'number_of_users' => '10',
			'apply_date_filter' => '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php // Numer of users Field ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_users' ) ); ?>"><?php _e( 'Number of users:', 'text_domain' ); ?></label>
		    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_of_users' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_users' ) ); ?>" type="text" value="<?php echo esc_attr( $number_of_users ); ?>" />
		</p>

		<?php // Apply date filter Checkbox ?>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'apply_date_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'apply_date_filter' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $apply_date_filter); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'apply_date_filter' ) ); ?>"><?php _e( 'Apply date filter', 'text_domain' ); ?></label>
		</p>
	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['number_of_users'] = isset( $new_instance['number_of_users'] ) ? wp_strip_all_tags( $new_instance['number_of_users'] ) : '';
		$instance['apply_date_filter'] = isset( $new_instance['apply_date_filter'] ) ? 1 : false;
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		echo $before_widget;

		global $wpdb;
		$table_name = $wpdb->prefix . "userank_points";
		$user_table_name = $wpdb->prefix . "users";
		$points_rows = $wpdb->get_results( "SELECT DISTINCT($user_table_name.ID) AS user_id, $user_table_name.display_name AS user_name FROM $table_name INNER JOIN $user_table_name ON $table_name.rankable_id = $user_table_name.ID WHERE rankable_type = 'user' GROUP BY $user_table_name.ID ORDER BY SUM($table_name.points) DESC" );

		echo '<div class="widget-text wp_widget_plugin_box">';
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo "<ul classname='userank-ranking_list'>";
		foreach ( $points_rows as $points_row ) 
		{
			echo "<li classname='userank-ranking_item'>" . $points_row->user_name . '</td>';
		}
		echo '</ul>';
		echo '</div>';
		echo $after_widget;
	}
}


function userank_register_widgets() {
	register_widget( 'User_Ranking_Widget' );
}
add_action( 'widgets_init', 'userank_register_widgets' );
