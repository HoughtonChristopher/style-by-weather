<?php
/*
Plugin Name: Style by weather
Description: Change your page based on local weather.
Version: 1
Author: Christopher Houghton
Author URI: http://ghoulk.in
License: MIT
*/

class WP_Style_By_Weather {

	public function __construct() {

		// Errors
		add_action( 'admin_notices', array( $this, 'do_error' ));

		// Options
	 	add_action( 'admin_menu', array( $this, 'add_options_page' ));

	 	// API set?
		if( get_option( 'sbw_api_key' )) {

			// Shortcode
			add_shortcode( 'style_by_weather', array( $this, 'shortcode' ));
		} else {

			// Error out
			$this->do_error( 'Please fill out your forecast.io API info in SBW options.' );
		}
	}

	public function shortcode( $attributes ) {

		extract( shortcode_atts( array( 
			'element' => '.entry_content'
		), $attributes ));

		return $this->change_style( $element );
	}

	public function change_style( $element ) {

		$weather = $this->get_weather();
		$property = get_option( 'sbw_property' );
		$value = get_option( $weather );

		echo '<script>jQuery( "'.$element.'" ).css( "'.$property.'", "'.$value.'")</script>';
	}

	public function get_weather() {

		$location = $this->decode_api_info( 'http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR'] );
 		$weather = $this->decode_api_info( 'http://api.forecast.io/forecast/'.get_option( 'sbw_api_key' ).'/'.strval( $location['latitude'] ).','.strval( $location['longitude'] ));

 		return $weather = $weather['currently']['icon'];
	}

	private function decode_api_info( $url ) {

		$url = file_get_contents($url);

		return json_decode($url,true);
	}

	public function do_error( $message, $errormsg = false ) {

	  	if ( !empty( $message )) {
		    if ( $errormsg ) echo '<div id="message" class="error">';
		    else echo '<div id="message" class="updated fade">';
		    echo "<p><strong>$message</strong></p></div>";
		}
	}

	public function add_options_page() {
		add_menu_page( 'Style by Weather', 'Style by Weather', 'manage_options', 'sbw_options_page', array( $this,'create_options_page' ));
	}

	public function create_options_page() {

		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Style by Weather</h2>
			<form method="post" action="options.php">	
				<?php wp_nonce_field( 'update-options' ); ?>
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
				<th scope="row"><a href="https://developer.darkskyapp.com/">forecast.io API key</a></th>
				<td><input type="text" name="sbw_api_key" value="<?php echo get_option('sbw_api_key'); ?>" /></td></tr>
				</tr></table><input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="clear-day,clear-night,rain,snow,sleet,wind,fog,cloudy,partly-cloudy-day,partly-cloudy-night,sbw_property,sbw_api_key" />
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		</div>
		<?php
	}
}

new WP_Style_By_Weather();

