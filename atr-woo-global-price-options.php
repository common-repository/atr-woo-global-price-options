<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.atarimtr.com/
 * @since             1.0.0
 * @package           Atr_Woo_Gpo
 *  
 * @wordpress-plugin
 * Plugin Name:       ATR Woocommerce Global Price Options
 * Plugin URI:        http://atarimtr.com
 * Description:       Add global price options by category to Woocommerce products. 
 * Version:           1.0.5
 * Author:            Yehuda Tiram
 * Author URI:        http://www.atarimtr.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       atr-woo-global-price-options
 * Domain Path:       /languages
 * WC requires at least: 2.6.0
 * WC tested up to: 6.0.0  
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-atr-woo-global-price-options-activator.php
 */
function activate_atr_woo_gpo() {
  if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'WooCommerce' ) ) {
    // Deactivate the plugin.
    deactivate_plugins( plugin_basename( __FILE__ ) );
    // Throw an error in the WordPress admin console.
    $error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' . esc_html__( 'This plugin requires ', 'atr-woo-global-price-options' ) . '<a href="' . esc_url( 'https://wordpress.org/plugins/woocommerce/' ) . '">WooCommerce</a>' . esc_html__( ' plugin to be active.', 'atr-woo-global-price-options' ) . '</p>';
    die( $error_message ); // WPCS: XSS ok.
  }	
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-atr-woo-global-price-options-activator.php';
	Atr_Woo_Gpo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-atr-woo-global-price-options-deactivator.php
 */
function deactivate_atr_woo_gpo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-atr-woo-global-price-options-deactivator.php';
	Atr_Woo_Gpo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_atr_woo_gpo' );
register_deactivation_hook( __FILE__, 'deactivate_atr_woo_gpo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-atr-woo-global-price-options.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_atr_woo_gpo() {

	$plugin = new Atr_Woo_Gpo();
	$plugin->run();

}
run_atr_woo_gpo();
