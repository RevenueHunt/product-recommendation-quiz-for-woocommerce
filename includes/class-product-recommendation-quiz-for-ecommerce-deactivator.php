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
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$GLOBALS['wp_object_cache']->delete( 'rh_token', 'options' );
		$GLOBALS['wp_object_cache']->delete( 'rh_shop_hashid', 'options' );
		$GLOBALS['wp_object_cache']->delete( 'rh_domain', 'options' );
		$GLOBALS['wp_object_cache']->delete( 'rh_api_key', 'options' );

		delete_option('rh_token');
		delete_option('rh_shop_hashid');
		delete_option('rh_domain');
		delete_option('rh_api_key');
	}

}
