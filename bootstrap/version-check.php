<?php
/**
 * Check if version requirements are met before allowing the plugin to run.
 *
 * @since 1.0.0
 *
 * @package BigBox\WC_Combined_Shipping
 * @category Bootstrap
 * @author Spencer Finnell
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Minimum PHP version.
define( 'WC_COMBINED_SHIPPING_PHP_VERSION', '5.4.0' );

// Do not allow the theme to be active if the PHP version is not met.
if ( version_compare( PHP_VERSION, WC_COMBINED_SHIPPING_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'wc_combined_shipping_php_admin_notices' );

	if ( is_admin() ) {
		return;
	}

	wp_die( wc_combined_shipping_get_php_notice_text() ); // WPCS: XSS okay.
}

/**
 * Output a notice that the minimum PHP version is not met.
 *
 * @since 1.10.0
 */
function wc_combined_shipping_php_admin_notices() {
	echo '<div class="notice notice-error"><p>' . wc_combined_shipping_get_php_notice_text() . '</p></div>'; // WPCS: XSS okay.
}

/**
 * PHP upgrade notice text.
 *
 * @since 1.0.0
 *
 * @return string
 */
function wc_combined_shipping_get_php_notice_text() {
	/**
	 * Filter text shown when current PHP version does not meet requirements.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Text to display.
	 */
	return apply_filters(
		'wc_combined_shipping_php_notice_text',
		/* translators: %s Minimum PHP version required for theme to run. */
		wp_kses_post( sprintf( __( 'Combined Shipping for WooCommerce requires PHP version <code>%s</code> or above to be active. Please contact your web host to upgrade.', 'wc-combined-shipping' ), esc_attr( WC_COMBINED_SHIPPING_PHP_VERSION ) ) )
	);
}
