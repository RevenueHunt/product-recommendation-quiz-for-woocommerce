<?php
/**
 * Fired during plugin activation
 *
 * @link       https://revenuehunt.com/
 * @since      1.0.0
 *
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/includes
 */

// Prevent direct access.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/includes
 */
class Product_Recommendation_Quiz_For_Ecommerce_Activator {

	/**
	 * Get option keys used by the plugin.
	 *
	 * Uses centralized constants from main plugin file.
	 *
	 * @since 2.2.15
	 * @return array List of option keys.
	 */
	private static function get_option_keys() {
		return array(
			PRQ_OPTION_SHOP_HASHID,
			PRQ_OPTION_API_KEY,
			PRQ_OPTION_DOMAIN,
			PRQ_OPTION_TOKEN,
		);
	}

	/**
	 * Run on plugin activation.
	 *
	 * Initializes default options if they don't already exist.
	 * Options are not autoloaded to minimize memory usage.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate() {
		// Initialize options with empty values if they don't exist
		// Using 'no' for autoload to prevent loading on every page
		foreach ( self::get_option_keys() as $key ) {
			if ( false === get_option( $key ) ) {
				add_option( $key, '', '', 'no' );
			}
		}
	}
}
