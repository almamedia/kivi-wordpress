<?php

/**
 * The plugin bootstrap file
 *
 * @since             1.0.0
 * @package           Kivi
 *
 * @wordpress-plugin
 * Plugin Name:       Kivi
 * Plugin URI:        https://github.com/almamedia/kivi-wordpress
 * Description:       A plugin for displaying KIVI real estate system items in WordPress
 * Version:           1.0.1
 * Author:            Alma Medapartners Oy
 * Author URI:        https://kivi.etuovi.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kivi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/* Schedule for kivi background jobs */
function kivi_add_schedule( $schedules ) {
	$schedules['every15minutes'] = array(
		'interval' => 900,
		'display' => __('Every 15 minutes')
	);
	return $schedules;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kivi-activator.php
 */
function activate_kivi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kivi-activator.php';
	Kivi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kivi-deactivator.php
 */
function deactivate_kivi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kivi-deactivator.php';
	Kivi_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kivi' );
register_deactivation_hook( __FILE__, 'deactivate_kivi' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kivi.php';

require_once plugin_dir_path( __FILE__ ) . 'public/kivi-functions.php';

require_once plugin_dir_path( __FILE__ ) . 'public/class-kivi-fact-box.php';
require_once plugin_dir_path( __FILE__ ) . 'public/class-kivi-viewable.php';
require_once plugin_dir_path( __FILE__ ) . 'public/class-kivi-property.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kivi() {

	$plugin = new Kivi();
	$plugin->run();

}
run_kivi();
