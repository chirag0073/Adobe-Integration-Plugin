<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              crestinfosystems.com
 * @since             1.0.0
 * @package           Adobeintegration
 *
 * @wordpress-plugin
 * Plugin Name:       Adobe Integration
 * Plugin URI:        https://www.crestinfosystems.com/contact-us/
 * Description:       Adobe Integration to access Adobe sevices.
 * Version:           1.0.0
 * Author:            Crest Infosystems Pvt Ltd
 * Author URI:        crestinfosystems.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       adobeintegration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define basic functionalities.* 
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/UtilsAdobeintegration.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ADOBE_PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-adobeintegration-activator.php
 */
function activate_adobeintegration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-adobeintegration-activator.php';
	Adobeintegration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-adobeintegration-deactivator.php
 */
function deactivate_adobeintegration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-adobeintegration-deactivator.php';
	Adobeintegration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_adobeintegration' );
register_deactivation_hook( __FILE__, 'deactivate_adobeintegration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-adobeintegration.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_adobeintegration() {

	$plugin = new Adobeintegration();
	$plugin->run();

}
run_adobeintegration();
