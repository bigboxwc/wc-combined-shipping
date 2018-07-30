<?php
/**
 * Frontend functionality.
 *
 * @since 1.0.0
 *
 * @package BigBox\WC_Combined_Shipping
 * @category Template
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
 * @param array            $rates Shipping package rates.
 * @param WC_Shipping_Rate $package WooCommerce shipping rate.
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
		$has_free = apply_filters( 'wc_combined_shipping_maybe_has_free_shipping', $unshipped, $rates, $package );

		// Remove free shipping if no open order or explicitely set.
		if ( ! $has_free || ! is_user_logged_in() ) {
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
		$label = sprintf(
			/* translators: %1$s order ID, %2$s order date. */
			__( 'Combine with order #%1$s (%2$s) and ship for free', 'wc-combined-shipping' ),
			$unshipped->get_id(),
			$unshipped->get_date_created()->date_i18n( get_option( 'date_format' ) )
		);

		// Update label.
		$rate->set_label( apply_filters( 'wc_combined_shipping_free_shipping_label', $label ) );
	}

	return $rates;
}
add_filter( 'woocommerce_package_rates', 'wc_combined_shipping_package_rates', 10, 2 );

/**
 * Add an order note to orders that utilize combined shipping.
 *
 * @since 1.1.0
 *
 * @param int $order_id Order ID.
 */
function wc_combined_shipping_add_order_note( $order_id ) {
	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$shipping  = $order->get_items( 'shipping' );
	$free      = false;
	$unshipped = null;

	// Only query if user is logged in.
	if ( is_user_logged_in() ) {
		$customer  = new BigBox\WC_Combined_Shipping\Customer( get_current_user_id() );
		$unshipped = $customer->get_previously_unshipped_order();
	}

	if ( ! $unshipped ) {
		return;
	}

	// Check if free shipping was used.
	foreach ( $shipping as $li ) {
		if ( 'free_shipping' === $li->get_method_id() ) {
			$free = true;
			break;
		}
	}

	if ( ! $free ) {
		return;
	}

	/**
	 * Filters the free shipping note added to an order.
	 *
	 * @since 1.0.0
	 *
	 * @param string $note Free shipping order note.
	 */
	$note = sprintf(
		/* translators: %1$s do not translate, %2$s order ID , %3$s do not translate. */
		__( 'Combine with order %1$s#%2$s%3$s and ship for free.', 'wc-combined-shipping' ),
		'<a href="' . esc_url(
			add_query_arg(
				[
					'action' => 'edit',
					'post'   => $unshipped->get_id(),
				],
				admin_url( 'post.php' )
			)
		) . '">',
		$unshipped->get_id(),
		'</a>'
	);

	$order->add_order_note( apply_filters( 'wc_combined_shipping_free_shipping_order_note', $note ) );
}

/** This filter is documented in app/class-customer.php */
$unshipped_status = apply_filters( 'wc_combined_shipping_unshipped_order_status', 'processing' );

add_action( 'woocommerce_order_status_' . $unshipped_status, 'wc_combined_shipping_add_order_note' );
