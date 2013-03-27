<?php
/*
Plugin Name: Style by weather
Description: Change your page based on local weather.
Version: 0.0001
Author: Christopher Houghton
Author URI: http://ghoulk.in	
License: MIT
*/

function style_by_weather() {

	// freegeoip
	$_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? $user_ip = 'google.com' : $user_ip = $_SERVER['REMOTE_ADDR'];
	$user_location = api_grab('http://freegeoip.net/json/'.$user_ip);

	$user_lat = strval($user_location['latitude']);
	$user_long = strval($user_location['longitude']);

	// forecast io
	$user_weather = api_grab('http://api.forecast.io/forecast/fa22bafdebe78a1b4ab13a178fc5677b/'.$user_lat.','.$user_long);

	current_weather($user_weather['currently']['icon']);
}

function api_grab($input) {
	$input = file_get_contents($input);
	return json_decode($input,true);
}

function current_weather($weather) {

	extract(shortcode_atts(array(
		'element' => '.entry-content',
		'property' => 'background-color'
	), $attributes));

	switch($weather) {
		case 'clear-day':
		change_css($element,$property,'white');
		return;

		case 'clear-night':
		change_css($element,$property,'#1F1F7A');
		return;

		case 'rain':
		change_css($element,$property,'#6A6A71');
		return;

		case 'snow':
		change_css($element,$property,'#EDEDEE');
		return;

		case 'sleet':
		change_css($element,$property,'#686882');
		return;

		case 'wind':
		change_css($element,$property,'#B2ECB2');
		return;

		case 'fog':
		change_css($element,$property,'#FFFF9D');
		return;

		case 'cloudy':
		change_css($element,$property,'#A0A0AE');
		return;

		case 'partly-cloudy-day':
		change_css($element,$property,'#BCBCC6');
		return;

		case 'partly-cloudy-night':
		change_css($element,$property,'#545458');
		return;

		default:
		return;
	}
}

function change_css($ele, $propname, $propvalue) {
	echo '<script>jQuery("'.$ele.'").css("'.$propname.'","'.$propvalue.'");</script>';
}

add_shortcode('style-by-weather','style_by_weather');
?>
