<?php
/**
 * Include Composer's autoloader.
 *
 * @since 1.0.0
 *
 * @package BigBox\WC_Combined_Shipping
 * @category Bootstrap
 * @author Spencer Finnell
 */

$file = __DIR__ . '/../vendor/autoload.php';

if ( file_exists( $file ) ) {
	require $file;
}
