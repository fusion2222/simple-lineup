<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function simple_lineup__get_sheduled_post_ids($post_type, $from_date, $to_date){
	global $wpdb;

	$db_table = simple_lineup__get_db_table_name($wpdb);

	$from_datetime = DateTime::createFromFormat('Y-m-d', $from_date);
	$to_datetime = DateTime::createFromFormat('Y-m-d', $to_date);
	// This should prevent SQL injection. In case of wierd format supplied, exception is thrown.

	// We have always date + 4 hours - so when there is a night program, it is not displayed on next day column.
	$from_datetime->setTime(4, 0, 0, 0);
	$to_datetime->add(new DateInterval('P1D'));
	$to_datetime->setTime(3, 59, 59, 999999);

	// TODO: By this point, This should be already sanitized. Sanitize ASAP.
	$sql_from_datetime = $from_datetime->format(DateTime::ATOM);
	$sql_to_datetime = $to_datetime->format(DateTime::ATOM);

	$node_ids = $wpdb->get_col(
		// We do not use `get_posts()` - it server for withdrawal of the latest posts. Moreover it is kind a limited.
		// for example 'post_type' => 'post' returns even bands, or events. However 'post_type' => 'event' returns
		// events only. This is inconsistent unreliable behaviour, therefore pure SQL is used.

		// In order to make modules separated, this function returns only IDs of
		// posts, which have sheduled at least one show in provided date range.
		$wpdb->prepare(
			"SELECT DISTINCT `node_id` FROM `{$db_table}` WHERE `node_type` = %s AND `start` >= %s AND `start` < %s ORDER BY `start` DESC;", $post_type, $sql_from_datetime, $sql_to_datetime
		)
	);
    return $node_ids;
}


function simple_lineup__get_sheduled_shows(
	$post_id, $from_date=null, $to_date=null
){
	global $wpdb;

	$db_table = simple_lineup__get_db_table_name($wpdb);

	$query_where_clause = '`node_id` = %d';
	$query_params = [$post_id];

	if(is_string($from_date)){
		$from_datetime = DateTime::createFromFormat('Y-m-d', $from_date);
		// We have always date + 4 hours - so when there is a night program, it is not displayed on next day column.
		$from_datetime->setTime(4, 0, 0, 0);
		$sql_from_datetime = $from_datetime->format(DateTime::ATOM);
		$query_where_clause .= ' AND `start` >= %s';
		$query_params[] = $sql_from_datetime;
	}

	if(is_string($to_date)){
		$to_datetime = DateTime::createFromFormat('Y-m-d', $to_date);
		$to_datetime->add(new DateInterval('P1D'));
		$to_datetime->setTime(3, 59, 59, 999999);
		$sql_to_datetime = $to_datetime->format(DateTime::ATOM);
		$query_where_clause .= ' AND `start` < %s';
		$query_params[] = $sql_to_datetime;
	}

	$output = $wpdb->get_results(
		// We do not use `get_posts()` - it server for withdrawal of the latest posts. Moreover it is kind a limited.
		// for example 'post_type' => 'post' returns even bands, or events. However 'post_type' => 'event' returns
		// events only. This is inconsistent unreliable behaviour, therefore pure SQL is used.

		// In order to make modules separated, this function returns only IDs of
		// posts, which have sheduled at least one show in provided date range.
		$wpdb->prepare(
			"SELECT * FROM `{$db_table}` WHERE {$query_where_clause} ORDER BY `start` DESC;",
			$query_params
		)
	);
    return $output;
}

// TODO: Make common validation and sanitization of dates in both functions above.
