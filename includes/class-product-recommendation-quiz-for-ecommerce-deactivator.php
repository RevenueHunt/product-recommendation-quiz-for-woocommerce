<?php
/**
 * Fired during plugin deactivation
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
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/includes
 */
class Product_Recommendation_Quiz_For_Ecommerce_Deactivator {

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
	 * Clean up plugin data.
	 *
	 * Clears cached options and optionally deletes them from the database.
	 * This method is the single source of truth for plugin cleanup.
	 *
	 * @since 2.2.15
	 * @param bool $delete_options Whether to delete options from database (default: true).
	 * @return void
	 */
	public static function cleanup( $delete_options = true ) {
		// Clear object cache for all option keys using WordPress function
		foreach ( self::get_option_keys() as $key ) {
			wp_cache_delete( $key, 'options' );
		}

		// Delete options from database if requested
		if ( $delete_options ) {
			foreach ( self::get_option_keys() as $key ) {
				delete_option( $key );
			}
		}
	}

	/**
	 * Plugin deactivation handler.
	 *
	 * Called when the plugin is deactivated. Clears all plugin data
	 * including options from the database.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate() {
		self::cleanup( true );
	}
}
