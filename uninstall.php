<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://revenuehunt.com/
 * @since      1.0.0
 *
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Define option key constants (must be defined here since main plugin file isn't loaded during uninstall).
// These values must match those in product-recommendation-quiz-for-ecommerce.php.
if ( ! defined( 'PRQ_OPTION_SHOP_HASHID' ) ) {
	define( 'PRQ_OPTION_SHOP_HASHID', 'rh_shop_hashid' );
	define( 'PRQ_OPTION_API_KEY', 'rh_api_key' );
	define( 'PRQ_OPTION_DOMAIN', 'rh_domain' );
	define( 'PRQ_OPTION_TOKEN', 'rh_token' );
}

// Clean up all plugin data.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-product-recommendation-quiz-for-ecommerce-deactivator.php';
Product_Recommendation_Quiz_For_Ecommerce_Deactivator::cleanup( true );
