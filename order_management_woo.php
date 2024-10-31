<?php
/**
 *
 * @link              https://ahsandev.com/
 * @since             1.0.0
 * @package           order_management_woo
 *
 * @wordpress-plugin
 * Plugin Name:       Order Management for WooCommerce 
 * Plugin URI:        https://wordpress.org/plugins/order-management-woo/
 * Description:       This Plugin is used to provide the features to do management of woocommerce orders, it provide detail reports and analytics for woocommerce store
 * Version:           1.0.3
 * Author:            Ahsan Khan
 * Author URI:        https://ahsandev.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       order-management-woo
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'order_management_woo_VERSION', '1.0.3' );

function order_management_woo_activate() {
	// Check if WooCommerce is active
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        // WooCommerce is active, proceed with activating
    } else {
        // WooCommerce is not active, show a message to the user
        wp_die('WooCommerce is not active. Please install and activate WooCommerce to use this plugin.');
    }

}
register_activation_hook( __FILE__, 'order_management_woo_activate' );

require plugin_dir_path( __FILE__ ) . 'includes/class-order_management_woo.php';
function order_management_woo_run() {

	$plugin = new order_management_woo();
	$plugin->run();

}
order_management_woo_run();