<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


function simple_lineup__js($hook) {
	// Adds plugin's custom JS to admin panel.
	wp_enqueue_script('simple_lineup__scripts', plugin_dir_url(__FILE__) . 'dist/js/simple_lineup.min.js');	
}

add_action('admin_enqueue_scripts', 'simple_lineup__js');


function simple_lineup__css($hook){
	// TODO: Display this only on edit.
	// TODO: We are overriding buttons - But WP-admin CSS files are loaded as the last.
	//       This means our CSS will be overriden. Find a better way how to do this.

	$css_url = plugin_dir_url(__FILE__) . 'dist/css/simple_lineup.min.css';
	// echo '<link rel="stylesheet" type="text/css" href="' . $css_url . '">';

	wp_register_style('simple_lineup__css', $css_url, [], '1.0.0', 'all');
	wp_enqueue_style('simple_lineup__css');
}

add_action('admin_enqueue_scripts', 'simple_lineup__css');

