<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function simple_lineup__activation_handler($somearg){
    global $wpdb;

    $table_name = simple_lineup__get_db_table_name($wpdb);
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,  # Primary key.
        start datetime DEFAULT NOW() NOT NULL,  # When show starts.
        node_id mediumint(9) NOT NULL,  # To which event ID this show belongs.
        node_type varchar(35) NOT NULL,  # To which event type this show belongs.
        duration mediumint(9) NOT NULL,  # Duration of shows in minutes.
        show_on_program_page tinyint(1) DEFAULT 0 NOT NULL,  # Should be displayed on a program page?
        created datetime DEFAULT NOW() NOT NULL,  # When this record was created.
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $output = dbDelta($sql);
}

register_activation_hook(
    SIMPLE_LINEUP__PLUGIN_INDEX_PATH, 'simple_lineup__activation_handler'
);


function simple_lineup__deactivation_handler($somearg){
    global $wpdb;  // This is temporarily on because of dev purposes.

    /*
     * DEBUG: Uncomment this in case, you need to drop DB table after deactivating plugin.
     *        Beware, Leaving this uncommented, can lead to severe data loss!!!
     *
     * $table_name = simple_lineup__get_db_table_name($wpdb);
     * $sql = "DROP TABLE IF EXISTS $table_name;";
     * $wpdb->query($sql);  // dbDelta() does not support DROP!!!
     *
     */
}

register_deactivation_hook(
    SIMPLE_LINEUP__PLUGIN_INDEX_PATH, 'simple_lineup__deactivation_handler'
);