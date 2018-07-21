<?php
/**
 * Plugin Name: Combined Shipping for WooCommerce
 * Plugin URI: https://bigboxwc.com/
 * Description: Allow customers to combine shipping for a new order with an existing unshipped order.
 * Version: WC_COMBINED_SHIPPING_VERSION
 * Author: Spencer Finnell
 *
 * @package BigBox\Plugin
 * @category Bootstrap
 * @author Spencer Finnell
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Current version -- automatically updated on release.
define( 'WC_COMBINED_SHIPPING_VERSION', '%WC_COMBINED_SHIPPING_VERSION%' );

// Plugin path and URL.
define( 'WC_COMBINED_SHIPPING_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_COMBINED_SHIPPING_URL', plugin_dir_url( __FILE__ ) );

// Composer autoloader.
require_once __DIR__ . '/bootstrap/version-check.php';

// Composer autoloader.
require_once __DIR__ . '/bootstrap/autoload.php';

// Start things.
require_once __DIR__ . '/bootstrap/app.php';
