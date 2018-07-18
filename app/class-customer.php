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
	 * @return array
	 */
	public function get_unshipped_orders() {
		return wc_get_orders(
			[
				'customer_id' => $this->get_id(),
				'return'      => 'ids',
				/**
				 * Filter the order status that determines if an order is unshipped.
				 *
				 * @since 1.0.0
				 *
				 * @param string $status Order status.
				 */
				'status'      => apply_filters( 'wc_combined_shipping_unshipped_order_status', 'processing' ),
			]
		);
	}

	/**
	 * Return the most recent unshipped order.
	 *
	 * @return null|WC_Order Order object if found.
	 */
	public function get_latest_unshipped_order() {
		$orders = $this->get_unshipped_orders();

		if ( empty( $orders ) ) {
			return null;
		}

		return wc_get_order( array_pop( $orders ) );
	}

}
