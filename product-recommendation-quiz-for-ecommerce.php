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
 * Version:           1.0.7
 * Author:            RevenueHunt
 * Author URI:        https://revenuehunt.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product-recommendation-quiz-for-ecommerce
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PRQ_PLUGIN_VERSION', '1.0.7');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-product-recommendation-quiz-for-ecommerce-activator.php
 */
function activate_product_recommendation_quiz_for_ecommerce() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-product-recommendation-quiz-for-ecommerce-activator.php';
	Product_Recommendation_Quiz_For_Ecommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-product-recommendation-quiz-for-ecommerce-deactivator.php
 */
function deactivate_product_recommendation_quiz_for_ecommerce() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-product-recommendation-quiz-for-ecommerce-deactivator.php';
	Product_Recommendation_Quiz_For_Ecommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_product_recommendation_quiz_for_ecommerce');
register_deactivation_hook(__FILE__, 'deactivate_product_recommendation_quiz_for_ecommerce');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-product-recommendation-quiz-for-ecommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_product_recommendation_quiz_for_ecommerce() {

	$plugin = new Product_Recommendation_Quiz_For_Ecommerce();
	$plugin->run();
}

function prq_set_token() {
	$post = $_REQUEST;

	if (!$post) {
		return 'die1';
	}

	if (time() - $post['signature'] > 600) {
		return 'die2';
	}

	update_option('rh_token', $post['token']);
	update_option('rh_shop_hashid', $post['shop_hashid']);

	return get_option('rh_shop_hashid');
}

add_action('rest_api_init', function() {
	register_rest_route('prq/v1', 'settoken', array(
		'methods' => 'POST',
		'callback' => 'prq_set_token',
	));
});

run_product_recommendation_quiz_for_ecommerce();
