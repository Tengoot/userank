<?php
function userank_database_install () {
    global $wpdb;
    global $userank_db_version;

    $table_name = $wpdb->prefix . "userank_points";
    $charset_collate = $wpdb->get_charset_collate();

	$sql = "
CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
		date date DEFAULT '0000-00-00' NOT NULL,
		points mediumint(9) DEFAULT 0 NOT NULL,
		rankable_id mediumint(9) NOT NULL,
        rankable_type varchar(100) NOT NULL,
		PRIMARY KEY  (id),
        KEY  (rankable_id, rankable_type)
) $charset_collate;
";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'userank_db_version', $userank_db_version );
}
?>
