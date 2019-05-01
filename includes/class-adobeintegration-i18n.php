<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       crestinfosystems.com
 * @since      1.0.0
 *
 * @package    Adobeintegration
 * @subpackage Adobeintegration/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Adobeintegration
 * @subpackage Adobeintegration/includes
 * @author     Crest Infosystems Pvt Ltd <admin@crestinfosystems.com>
 */
class Adobeintegration_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'adobeintegration',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
