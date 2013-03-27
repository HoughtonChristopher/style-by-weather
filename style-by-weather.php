<?php
/*
Plugin Name: Style by weather
Description: Change your page based on local weather.
Version: 0.0001
Author: Christopher Houghton
Author URI: http://ghoulk.in	
License: MIT
*/

function style_by_weather($attributes) {

	extract(shortcode_atts(array(
		'element' => '.entry-content',
	), $attributes));

	// freegeoip
	$_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? $user_ip = 'google.com' : $user_ip = $_SERVER['REMOTE_ADDR'];
	$user_location = api_grab('http://freegeoip.net/json/'.$user_ip);

	$user_lat = strval($user_location['latitude']);
	$user_long = strval($user_location['longitude']);

	// forecast io
	$user_weather = api_grab('http://api.forecast.io/forecast/fa22bafdebe78a1b4ab13a178fc5677b/'.$user_lat.','.$user_long);

	current_weather($user_weather['currently']['icon'],$element);
}

function api_grab($input) {
	$input = file_get_contents($input);
	return json_decode($input,true);
}

function current_weather($weather,$element) {
	$property = get_option('sbw_property');
	$value = get_option($weather);
	if($property && $value) {
		change_css($element,$property,$value);
	}
}

function change_css($ele, $propname, $propvalue) {
	echo '<script>jQuery("'.$ele.'").css("'.$propname.'","'.$propvalue.'");</script>';
}
add_shortcode('style-by-weather','style_by_weather');

// options page
function style_by_weather_options() {
	?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Style by Weather</h2>
	<form method="post" action="options.php">	
	<?php wp_nonce_field('update-options'); ?>
	<table class="form-table">
	<tr valign="top">
	<th scope="row">Clear Day value</th>
	<td><input type="text" name="clear-day" value="<?php echo get_option('clear-day'); ?>" /></td></tr>
	<th scope="row">Clear Night value</th>
	<td><input type="text" name="clear-night" value="<?php echo get_option('clear-night'); ?>" /></td></tr>
	<th scope="row">Rain value</th>
	<td><input type="text" name="rain" value="<?php echo get_option('rain'); ?>" /></td></tr>
	<th scope="row">Snow value</th>
	<td><input type="text" name="snow" value="<?php echo get_option('snow'); ?>" /></td></tr>
	<th scope="row">Sleet value</th>
	<td><input type="text" name="sleet" value="<?php echo get_option('sleet'); ?>" /></td></tr>
	<th scope="row">Wind value</th>
	<td><input type="text" name="wind" value="<?php echo get_option('wind'); ?>" /></td></tr>
	<th scope="row">Fog value</th>
	<td><input type="text" name="fog" value="<?php echo get_option('fog'); ?>" /></td></tr>
	<th scope="row">Cloudy value</th>
	<td><input type="text" name="cloudy" value="<?php echo get_option('cloudy'); ?>" /></td></tr>
	<th scope="row">Partly Cloudy Day value</th>
	<td><input type="text" name="partly-cloudy-day" value="<?php echo get_option('partly-cloudy-day'); ?>" /></td></tr>
	<th scope="row">Partly Cloudy Night value</th>
	<td><input type="text" name="partly-cloudy-night" value="<?php echo get_option('partly-cloudy-night'); ?>" /></td></tr>
	<th scope="row">Property to alter</th>
	<td><input type="text" name="sbw_property" value="<?php echo get_option('sbw_property'); ?>" /></td></tr>
	</tr></table><input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="clear-day,clear-night,rain,snow,sleet,wind,fog,cloudy,partly-cloudy-day,partly-cloudy-night,sbw_property" />
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	</div>
	<?php
}

function sbw_menu() { add_menu_page('Style by Weather','Style by Weather','manage_options','sbw_options','style_by_weather_options'); }

add_action('admin_menu','sbw_menu');

?>
