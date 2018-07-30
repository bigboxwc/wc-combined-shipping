<?php
/**
 * Plugin.
 *
 * @since 1.0.0
 *
 * @package BigBox\WC_Combined_Shipping
 * @category Bootstrap
 * @author Spencer Finnell
 */

namespace BigBox\WC_Combined_Shipping;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Theme class.
 *
 * @since 1.0.0
 */
final class Plugin implements Registerable {

	/**
	 * Register the theme with the WordPress system.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception\InvalidService If a service is not valid.
	 */
	public function register() {
		$this->load_helpers();

		// Load translations.
		load_plugin_textdomain( 'wc-combined-shipping', false, trailingslashit( WC_COMBINED_SHIPPING_PATH ) . 'resources/languages' );
	}

	/**
	 * Load functional helpers.
	 *
	 * @since 1.0.0
	 */
	public function load_helpers() {
		$helpers = [
			'functions.php',
		];

		foreach ( $helpers as $file ) {
			require_once trailingslashit( WC_COMBINED_SHIPPING_PATH ) . trailingslashit( 'app' ) . $file;
		}
	}

}
