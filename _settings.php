<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//We'll key on the slug for the settings page so set it here so it can be used in various places
const SIMPLE_LINEUP__SLUG = 'simple-lineup';
const SIMPLE_LINEUP__CONTENT_TYPES_FIELD = 'simple_lineup__content_types';

const SIMPLE_LINEUP__PLANNING_METABOX_ID = 'simple_lineup__planning_metabox';
const SIMPLE_LINEUP__PLANNING_NONCE_FIELD_ID = 'simple_lineup__planning_nonce_field';
const SIMPLE_LINEUP__PLANNING_NONCE_FIELD_PROCESS = 'simple_lineup__planning_nonce_field_process';

const SIMPLE_LINEUP__PLANNING_FIELD_ID = 'simple_lineup__planning_field';
const SIMPLE_LINEUP__PLANNING_CHOICES_DEFAULT = [];

const SIMPLE_LINEUP__DIR_NAME = 'simple-lineup';
const SIMPLE_LINEUP__PLUGIN_PATH = ABSPATH . 'wp-content/plugins/' . SIMPLE_LINEUP__DIR_NAME;
const SIMPLE_LINEUP__PLUGIN_INDEX_PATH = SIMPLE_LINEUP__PLUGIN_PATH . '/simple-lineup.php';

const SIMPLE_LINEUP__MINUTE_GRANULARITY = 5; // If set to 2, user will have possibility only to choose 0, 2, 4, 6 ... etc.
const SIMPLE_LINEUP__DEFAULT_MINUTE_LENGTH = 30;

const SIMPLE_LINEUP__DB_TABLE_NAME = 'simple_lineup__sheduling';  // Unprefixed! Do not use!

function simple_lineup__get_db_table_name($wpdb=null){
	if($wpdb === null){
		global $wpdb;
	}

	return $wpdb->prefix . SIMPLE_LINEUP__DB_TABLE_NAME;
}
