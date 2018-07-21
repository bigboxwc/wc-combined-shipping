<?php
/**
 * Template modifications.
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

/**
 * Update shipping package rates.
 *
 * - Do nothing if the current session is a guest.
 * - Remove the free_shipping method if no unprocessed order exists.
 * - Update the shipping rate label to explain combined shipping.
 *
 * @since 1.0.0
 *
 * @param array $rates Shipping package rates.
 * @return array
 */
function wc_combined_shipping_package_rates( $rates, $package ) {
	$unshipped = null;

	// Only query if user is logged in.
	if ( is_user_logged_in() ) {
		$customer  = new BigBox\WC_Combined_Shipping\Customer( get_current_user_id() );
		$unshipped = $customer->get_latest_unshipped_order();
	}

	// Modify label with a link to latest order.
	foreach ( $rates as $method_id => $rate ) {
		if ( 'free_shipping' !== $rate->get_method_id() ) {
			continue;
		}

		/**
		 * Filter if free shipping should be offered at all.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $sould True.
		 */
		$has_free = apply_filters( 'wc_combined_shipping_maybe_has_free_shipping', true, $rates, $package );

		// Remove free shipping if no open order or explicitely set.
		if ( ! $unshipped || ! $has_free || ! is_user_logged_in() ) {
			unset( $rates[ $method_id ] );

			return $rates;
		}

		/**
		 * Filters the free shipping label that is displayed.
		 *
		 * @since 1.0.0
		 *
		 * @param string $label Free shipping label.
		 */
		$label = apply_filters(
			'wc_combined_shipping_free_shipping_label',
			sprintf(
				/* translators: %1$s order ID, %2$s order date. */
				__( 'Ship with order #%1$s (%2$s) for free', 'wc-combined-shipping' ),
				$unshipped->get_id(),
				$unshipped->get_date_created()->date_i18n( get_option( 'date_format' ) )
			)
		);

		// Update label.
		$rate->set_label( $label );
	}

	return $rates;
}
add_filter( 'woocommerce_package_rates', 'wc_combined_shipping_package_rates', 10, 2 );
