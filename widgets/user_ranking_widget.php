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
	public function __construct() {
		parent::__construct(
			'user_ranking_widget',
			__( 'User Ranking Widget', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
		add_action('wp_ajax_ajaxFilterUserRanking', array($this, 'ajaxFilterUserRanking'));
		add_action('wp_ajax_nopriv_ajaxFilterUserRanking', array($this, 'ajaxFilterUserRanking'));
	}

	public function form( $instance ) {
		$defaults = array(
			'title' => '',
			'number_of_users' => '10',
			'apply_date_filter' => '',
		);
		
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

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['number_of_users'] = isset( $new_instance['number_of_users'] ) ? wp_strip_all_tags( $new_instance['number_of_users'] ) : '';
		$instance['apply_date_filter'] = isset( $new_instance['apply_date_filter'] ) ? 1 : false;
		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$number_of_users = isset( $instance['number_of_users'] ) ? $instance['number_of_users'] : '10';
		$apply_date_filter = !empty($instance['apply_date_filter'] ) ? $instance['apply_date_filter'] : false;
		echo $before_widget;

		global $wpdb;
		$table_name = $wpdb->prefix . "userank_points";
		$user_table_name = $wpdb->prefix . "users";
		$points_rows = $wpdb->get_results( "SELECT DISTINCT($user_table_name.ID) AS user_id, $user_table_name.display_name AS user_name FROM $table_name INNER JOIN $user_table_name ON $table_name.rankable_id = $user_table_name.ID WHERE rankable_type = 'user' AND $table_name.date BETWEEN DATE_SUB(NOW(), INTERVAL 1 YEAR) AND NOW() GROUP BY $user_table_name.ID ORDER BY SUM($table_name.points) DESC LIMIT $number_of_users" );

		echo '<div class="widget-text wp_widget_plugin_box">';
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo "<input id='userank_query_limit' type='hidden' value=$number_of_users />";

		if ( $apply_date_filter == true ) {
			$selected_html = '<p><label for="date_filter_select"></label><br /><select id="userank_user_date_filter" name="userank_user_date_filter[]" />';
			$selected_html .= '<option value="day">Today</option>';
			$selected_html .= '<option value="week">This Week</option>';
			$selected_html .= '<option value="month">This Month</option>';
			$selected_html .= '<option value="year" selected="selected">This Year</option>';
			$selected_html .= '</select></p>';

			echo $selected_html;
		}

		echo "<ol id='userank-user-ranking' classname='userank-ranking_list'>";
		foreach ( $points_rows as $points_row ) 
		{
			$color = get_user_meta($points_row->user_id, 'nickname_color', true);
			$link = get_author_posts_url($points_row->user_id);
			$display_name = "<a href='$link' style='text-decoration: none;'><span style='color: $color;'>$points_row->user_name</a></span>";

			echo "<li classname='userank-ranking_item'>" . $display_name . '</td>';
		}
		echo '</ol>';
		echo '</div>';
		echo $after_widget;
	}

	public function ajaxFilterUserRanking() {
		global $wpdb;
		$table_name = $wpdb->prefix . "userank_points";
		$user_table_name = $wpdb->prefix . "users";

		$time = $_POST['t'];
		$number_of_users = $_POST['n'];
		switch($time) {
			case 'day':
			    $interval = 'INTERVAL 1 DAY';
				break;
			case 'week':
			    $interval = 'INTERVAL 7 DAY';
				break;
			case 'month':
			    $interval = 'INTERVAL 1 MONTH';
				break;
			case 'year':
			    $interval = 'INTERVAL 1 YEAR';
				break;
			default:
				$interval = null;
		}

		$interval_where_clause = null;
		$limit_clause = null;

		if ($interval) {
			$interval_where_clause = "AND $table_name.date BETWEEN DATE_SUB(NOW(), $interval) AND NOW()";
		}

		if ($number_of_users) {
			$limit_clause = "LIMIT $number_of_users";
		}

		$points_rows = $wpdb->get_results( "SELECT DISTINCT($user_table_name.ID) AS user_id, $user_table_name.display_name AS user_name FROM $table_name INNER JOIN $user_table_name ON $table_name.rankable_id = $user_table_name.ID WHERE rankable_type = 'user' $interval_where_clause GROUP BY $user_table_name.ID ORDER BY SUM($table_name.points) DESC $limit_clause" );

		foreach ( $points_rows as $points_row ) 
		{
			$color = get_user_meta($points_row->user_id, 'nickname_color', true);
			$link = get_author_posts_url($points_row->user_id);
			$display_name = "<a href='$link' style='text-decoration: none;'><span style='color: $color;'>$points_row->user_name</a></span>";
			
			echo "<li classname='userank-ranking_item'>" . $display_name . '</td>';
		}
		
		die();
	}
}


function userank_register_widgets() {
	register_widget('User_Ranking_Widget');
}

add_action( 'widgets_init', 'userank_register_widgets' );
