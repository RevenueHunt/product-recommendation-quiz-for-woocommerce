<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://revenuehunt.com/
 * @since      1.0.0
 *
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/public
 */
class Product_Recommendation_Quiz_For_Ecommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) { 

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		$dataToBePassed = array(
			'shop' => PRQ_STORE_URL,
			'platform' => 'woocommerce',
			'channel' => 'wordpress',
			'plugin_version' => PRQ_PLUGIN_VERSION,
			'woo_version' => PRQ_WOO_VERSION,
			'wp_version' => PRQ_WP_VERSION
		);

		wp_enqueue_script($this->plugin_name, PRQ_ADMIN_URL . '/embed.js?shop=' . PRQ_STORE_URL, array(), PRQ_PLUGIN_VERSION, true);
		wp_localize_script($this->plugin_name, 'js_vars', $dataToBePassed);
	}

}
