<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           shopadvisor-search
 *
 * @wordpress-plugin
 * Plugin Name:       ShopAdvisor Product Search
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This plugin searches and imports products from ShopAdvisor Product Search API.
 * Version:           1.0.0
 * Author:            Your Name or Your Company
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shopadvisor-search
 * Domain Path:       /languages
 */
/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) || ! function_exists( 'is_woocommerce_active' ) ) {
    require_once( 'woo-includes/woo-functions.php' );
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-shopadvisor-search-activator.php
 */
function activate_shopadvisor_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-shopadvisor-search-activator.php';
    ShopAdvisor_Search_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-shopadvisor-search-deactivator.php
 */
function deactivate_shopadvisor_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-shopadvisor-search-deactivator.php';
    ShopAdvisor_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_shopadvisor_search' );
register_deactivation_hook( __FILE__, 'deactivate_shopadvisor_search' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-shopadvisor-search.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_shopadvisor_search() {

	$plugin = new ShopAdvisor_Search();
	$plugin->run();

}
run_shopadvisor_search();

