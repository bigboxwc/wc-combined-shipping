<?php
/**
 * Invalid service.
 *
 * @since 1.0.0
 *
 * @package BigBox\WC_Combined_Shipping
 * @category Exception
 * @author Spencer Finnell
 */

namespace BigBox\WC_Combined_Shipping\Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class InvalidService.
 *
 * @since 1.0.0
 */
class InvalidService extends \InvalidArgumentException implements PluginException {

	/**
	 * Create a new instance of the exception for a service class name that is
	 * not recognized.
	 *
	 * @since 1.0.0
	 *
	 * @param string $service Class name of the service that was not recognized.
	 * @return static
	 */
	public static function from_service( $service ) {
		$message = sprintf(
			/* translators: %s service name. */
			__( 'The service "%s" is not recognized and cannot be registered.', 'plugin' ),
			is_object( $service )
				? get_class( $service )
				: (string) $service
		);

		return new static( $message );
	}
}
