<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function simple_lineup__settings_link($links){
    $links[] = '<a href="'. menu_page_url(SIMPLE_LINEUP__SLUG, false ) .'">Settings</a>';
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'simple_lineup__settings_link' );
