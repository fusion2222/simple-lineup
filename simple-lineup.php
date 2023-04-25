<?php

/*
 * Plugin Name: Simple Lineup
 * Description: Allows sheduling of selected content as event program.
 * Author: Matej Šrubař
 * Author URI:  https://www.exile.sk 
 */

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

include '_settings.php';
include '_db.php';
include '_utils.php';
include '_plugin_settings_link.php';
include '_options.php';
include 'templates/sheduling-widget-showbox.php';
include '_content_meta_box.php';
include '_forms.php';
include '_queries.php';
include '_assets.php';

// TODO: Upon post deletion, remove also its sheduled shows!!!
