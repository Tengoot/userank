<?php
/**
 * @package userank
 * @version 0.0.1
 */
/*
Plugin name: Userank
Plugin URI: https://github.com/Tengoot/userank
Description: This Plugin lets you add ranking elements and badges to users.
Version: 0.0.1
Author: Tengoot
Author URI: https://github.com/Tengoot
*/

// Global variables land here

if(!defined('USERANK_PLUGIN_DIR'))
    define('USERANK_PLUGIN_DIR', dirname(__FILE__));
// Includes
include(USERANK_PLUGIN_DIR . '/includes/options.php');
include(USERANK_PLUGIN_DIR . '/includes/points_table.php');
include(USERANK_PLUGIN_DIR . '/includes/meta.php');
include(USERANK_PLUGIN_DIR . '/includes/user_point_callbacks.php');
include(USERANK_PLUGIN_DIR . '/includes/nickname_colors.php');
include(USERANK_PLUGIN_DIR . '/widgets/user_ranking_widget.php');

//hooks
register_activation_hook(__FILE__, 'userank_database_install');

wp_enqueue_script('userank_js', plugins_url('/js/scripts.js', __FILE__), array( 'jquery' ), time(), true);

