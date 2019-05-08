<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// This file should contain util functions.

function simple_lineup__showbox_start_format($strDate){
	// This format is what user visually percieve. - hh:mm d.m.Y
	return DateTime::createFromFormat('Y-m-d H:i:s', $strDate)->format('G:i d.m.Y');
}

function simple_lineup__showbox_duration_format($minutes){
	// This format is what user visually percieve. - DDd HHh MMm

	$minutesToDisplay = $minutes % 60;
	$hoursToDisplay = floor($minutes / 60);
	$daysToDisplay = floor($minutes / 60 / 24);

	$output = (string)$minutesToDisplay . 'm';

	if($hoursToDisplay){
		$output = (string)$hoursToDisplay . 'h ' . $output;
	}

	if($daysToDisplay){
		$output = (string)$daysToDisplay . 'd ' . $output;
	}

	return $output;
}
