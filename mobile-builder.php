<?php

/**
 *
 * @link              https://rnlab.io
 * @since             1.0.0
 * @package           Mobile_Builder
 *
 * @wordpress-plugin
 * Plugin Name:       Mobile Builder
 * Plugin URI:        https://doc-oreo.rnlab.io
 * Description:       The most advanced drag & drop app builder. Create multi templates and app controls.
 * Version:           1.0.0
 * Author:            Rnlab.io
 * Author URI:        https://rnlab.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mobile-builder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MOBILE_BUILDER_VERSION', '1.0.0' );
define( 'MOBILE_BUILDER_APP_VERSION', '1.2.0' );
define( 'MOBILE_BUILDER_JS_VERSION', '1.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mobile-builder-activator.php
 */
function activate_mobile_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobile-builder-activator.php';
	Mobile_Builder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mobile-builder-deactivator.php
 */
function deactivate_mobile_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobile-builder-deactivator.php';
	Mobile_Builder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mobile_builder' );
register_deactivation_hook( __FILE__, 'deactivate_mobile_builder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mobile-builder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mobile_builder() {

	$plugin = new Mobile_Builder();
	$plugin->run();

}
run_mobile_builder();
