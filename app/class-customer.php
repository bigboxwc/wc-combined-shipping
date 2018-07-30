<?php
/**
 * Manage a customer to determine shipping options.
 *
 * @since 1.0.0
 *
 * @package BigBox\WC_Combined_Shipping
 * @category Bootstrap
 * @author Spencer Finnell
 */

namespace BigBox\WC_Combined_Shipping;

use WC_Customer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Customer
 *
 * @since 1.0.0
 */
class Customer extends WC_Customer {

	/**
	 * Return a list of the customer's unshipped orders.
	 *
	 * @todo Move to data store?
	 *
	 * @since 1.0.0
	 *
	 * @param array $args wc_get_orders() Arguments.
	 * @return array
	 */
	public function get_unshipped_orders( $args = [] ) {
		/**
		 * Filter the order status that determines if an order is unshipped.
		 *
		 * @since 1.0.0
		 *
		 * @param string $status Order status.
		 */
		$unshipped_status = apply_filters( 'wc_combined_shipping_unshipped_order_status', 'processing' );

		$defaults = [
			'customer_id' => $this->get_id(),
			'return'      => 'ids',
			'status'      => $unshipped_status,
			'orderby'     => 'ID',
			'order'       => 'DESC',
		];

		$args = wp_parse_args( $args, $defaults );

		return wc_get_orders( $args );
	}

	/**
	 * Return the most second most recent unshipped order.
	 *
	 * Used to get the previous order (since the current order is now technically unshipped).
	 *
	 * @return null|WC_Order Order object if found.
	 */
	public function get_previously_unshipped_order() {
		$orders = $this->get_unshipped_orders( [
			'number' => 1,
			'offset' => 1,
		] );

		if ( empty( $orders ) ) {
			return null;
		}

		return wc_get_order( current( $orders ) );
	}

	/**
	 * Return the most recent unshipped order.
	 *
	 * @return null|WC_Order Order object if found.
	 */
	public function get_latest_unshipped_order() {
		$orders = $this->get_unshipped_orders( [
			'number' => 1,
		] );

		if ( empty( $orders ) ) {
			return null;
		}

		return wc_get_order( current( $orders ) );
	}

}
