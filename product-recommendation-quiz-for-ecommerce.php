<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://revenuehunt.com/
 * @since             1.0.0
 * @package           Product_Recommendation_Quiz_For_Ecommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Product Recommendation Quiz for eCommerce
 * Plugin URI:        https://revenuehunt.com/product-recommendation-quiz-woocommerce/
 * Description:       Advise and delight your customers by engaging them with a personal shopper experience on your store, guiding your customers from start to cart and helping them find the products that best match their needs.
 * Version:           2.3.2
 * Author:            RevenueHunt
 * Author URI:        https://revenuehunt.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product-recommendation-quiz-for-ecommerce
 * Domain Path:       /languages
 * Requires at least: 3.0.1
 * Tested up to:      6.9
 * Requires PHP:      5.6
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PRQ_PLUGIN_VERSION', '2.3.2' );

/**
 * Option keys used by the plugin.
 * Centralized here to ensure DRY - activator, deactivator, and uninstall all use these.
 *
 * @since 2.2.15
 */
define( 'PRQ_OPTION_SHOP_HASHID', 'rh_shop_hashid' );
define( 'PRQ_OPTION_API_KEY', 'rh_api_key' );
define( 'PRQ_OPTION_DOMAIN', 'rh_domain' );
define( 'PRQ_OPTION_TOKEN', 'rh_token' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-product-recommendation-quiz-for-ecommerce-activator.php
 */
function product_recommendation_quiz_for_ecommerce_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-product-recommendation-quiz-for-ecommerce-activator.php';
	Product_Recommendation_Quiz_For_Ecommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-product-recommendation-quiz-for-ecommerce-deactivator.php
 */
function product_recommendation_quiz_for_ecommerce_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-product-recommendation-quiz-for-ecommerce-deactivator.php';
	Product_Recommendation_Quiz_For_Ecommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'product_recommendation_quiz_for_ecommerce_activate' );
register_deactivation_hook( __FILE__, 'product_recommendation_quiz_for_ecommerce_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-product-recommendation-quiz-for-ecommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function product_recommendation_quiz_for_ecommerce_run() {

	$plugin = new Product_Recommendation_Quiz_For_Ecommerce();
	$plugin->run();
}

add_action( 'rest_api_init', 'register_prq_set_token' );

/**
 * Register the WooCommerce REST API endpoint for token setting.
 *
 * @since 1.0.0
 * @return void
 */
function register_prq_set_token() {
	register_rest_route( 'wc/v3', 'prq_set_token', array(
		'methods'             => 'POST',
		'callback'            => 'prq_set_token',
		'permission_callback' => 'check_woocommerce_api_permission',
	) );
}

/**
 * Check WooCommerce API authentication for REST endpoint.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request The REST API request object.
 * @return bool True if authenticated, false otherwise.
 */
function check_woocommerce_api_permission( $request ) {
	// Ensure WooCommerce is available
	if ( ! class_exists( 'WC_REST_Authentication' ) ) {
		return false;
	}

	$auth = new WC_REST_Authentication();
	// Note we are not trying to authenticate a specific user, so we need to pass false to the function
	$result = $auth->authenticate( false );

	// Return false if authentication failed with an error
	if ( is_wp_error( $result ) ) {
		return false;
	}

	// Return true only if we have a valid authenticated user
	if ( is_a( $result, 'WP_User' ) ) {
		return true;
	}

	// Return false for all other cases (no authentication provided, null, or unexpected values)
	return false;
}

/**
 * Validate shop_hashid format.
 *
 * Shop hashid should be alphanumeric only.
 *
 * @since 2.2.15
 * @param string $shop_hashid The shop hashid to validate.
 * @return bool True if valid, false otherwise.
 */
function prq_validate_shop_hashid( $shop_hashid ) {
	if ( empty( $shop_hashid ) ) {
		return false;
	}
	return (bool) preg_match( '/^[a-zA-Z0-9]+$/', $shop_hashid );
}

/**
 * Check rate limit for REST API requests.
 *
 * Limits requests to 10 per minute per IP address to prevent brute force attacks.
 *
 * @since 2.2.15
 * @return true|WP_Error True if under limit, WP_Error if rate limited.
 */
function prq_check_rate_limit() {
	// Get client IP - check for proxies
	$ip = '';
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		// Take the first IP if multiple are provided
		$forwarded_ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		$ip = trim( $forwarded_ips[0] );
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	}

	if ( empty( $ip ) ) {
		return true; // Can't rate limit without IP
	}

	$transient_key = 'prq_rate_' . md5( $ip );
	$attempts      = (int) get_transient( $transient_key );

	if ( $attempts >= 10 ) {
		return new WP_Error(
			'rate_limited',
			__( 'Too many requests. Please try again later.', 'product-recommendation-quiz-for-ecommerce' ),
			array( 'status' => 429 )
		);
	}

	set_transient( $transient_key, $attempts + 1, MINUTE_IN_SECONDS );
	return true;
}

/**
 * Handle REST API token setting request.
 *
 * Stores shop_hashid and api_key from the RevenueHunt platform
 * if they don't already exist.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response|WP_Error Response with shop_hashid or error.
 */
function prq_set_token( $request ) {
	// Check rate limit
	$rate_limit_check = prq_check_rate_limit();
	if ( is_wp_error( $rate_limit_check ) ) {
		return $rate_limit_check;
	}

	// Use WP_REST_Request methods instead of $_REQUEST for security
	$new_shop_hashid = sanitize_text_field( $request->get_param( 'shop_hashid' ) );
	$new_api_key     = sanitize_text_field( $request->get_param( 'api_key' ) );

	// Validate shop_hashid format if provided
	if ( ! empty( $new_shop_hashid ) && ! prq_validate_shop_hashid( $new_shop_hashid ) ) {
		return new WP_Error(
			'invalid_shop_hashid',
			__( 'Invalid shop hashid format. Only alphanumeric characters are allowed.', 'product-recommendation-quiz-for-ecommerce' ),
			array( 'status' => 400 )
		);
	}

	// Get existing values
	$shop_hashid = get_option( PRQ_OPTION_SHOP_HASHID );
	$api_key     = get_option( PRQ_OPTION_API_KEY );

	// Only set if not already present
	if ( ! $shop_hashid && ! empty( $new_shop_hashid ) ) {
		update_option( PRQ_OPTION_SHOP_HASHID, $new_shop_hashid, false );
	}

	if ( ! $api_key && ! empty( $new_api_key ) ) {
		update_option( PRQ_OPTION_API_KEY, $new_api_key, false );
	}

	return rest_ensure_response( get_option( PRQ_OPTION_SHOP_HASHID ) );
}

/**
 * Verify HMAC signature for secondary REST endpoint.
 *
 * This endpoint is called by the RevenueHunt platform to set tokens.
 * Validates the HMAC signature to ensure the request is authentic.
 *
 * @since 2.2.15
 * @param WP_REST_Request $request The REST API request object.
 * @return bool True if signature is valid, false otherwise.
 */
function prq_verify_signature( $request ) {
	$signature   = sanitize_text_field( $request->get_param( 'signature' ) );
	$shop_hashid = sanitize_text_field( $request->get_param( 'shop_hashid' ) );
	$token       = sanitize_text_field( $request->get_param( 'token' ) );
	$timestamp   = sanitize_text_field( $request->get_param( 'timestamp' ) );

	// All parameters must be present
	if ( empty( $signature ) || empty( $shop_hashid ) || empty( $token ) ) {
		return false;
	}

	// If timestamp provided, check it's not too old (5 minute window)
	if ( ! empty( $timestamp ) ) {
		$request_time = absint( $timestamp );
		$current_time = time();
		if ( abs( $current_time - $request_time ) > 300 ) {
			return false;
		}
	}

	// If we have an existing API key, verify the signature.
	// NOTE: On first-time setup, API key doesn't exist yet, so signature
	// verification is skipped. This is intentional - the initial credentials
	// come from RevenueHunt's server during the OAuth flow. Once credentials
	// are set, prq_set_token() only allows updates if current values are empty,
	// so the window for exploitation is minimal.
	$api_key = get_option( PRQ_OPTION_API_KEY );
	if ( $api_key ) {
		// Reconstruct the data that should have been signed
		$data = sprintf( 'hashid=%s&token=%s', $shop_hashid, $token );
		if ( ! empty( $timestamp ) ) {
			$data .= '&timestamp=' . $timestamp;
		}
		$expected_signature = base64_encode( hash_hmac( 'sha256', $data, $api_key, true ) );

		// Use hash_equals for timing-safe comparison
		if ( ! hash_equals( $expected_signature, $signature ) ) {
			return false;
		}
	}

	return true;
}

add_action( 'rest_api_init', function() {
	register_rest_route( 'prq/v1', 'settoken', array(
		'methods'             => 'POST',
		'callback'            => 'prq_set_token',
		'permission_callback' => 'prq_verify_signature',
	) );
} );

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

product_recommendation_quiz_for_ecommerce_run();
