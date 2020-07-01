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
 * @author     Alex <alex@revenuehunt.com>
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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Product_Recommendation_Quiz_For_Ecommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Product_Recommendation_Quiz_For_Ecommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/product-recommendation-quiz-for-ecommerce-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Product_Recommendation_Quiz_For_Ecommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Product_Recommendation_Quiz_For_Ecommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$dataToBePassed = array(
			'shop' => STORE_URL,
			'platform' => 'woocommerce',
			'wooversion' => WOO_VERSION,
			'wpversion' => WP_VERSION
		);

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/product-recommendation-quiz-for-ecommerce-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script($this->plugin_name, ADMIN_URL . '/embed.js?shop=' . STORE_URL, array(), PRODUCT_RECOMMENDATION_QUIZ_FOR_ECOMMERCE_VERSION, true);
		wp_localize_script($this->plugin_name, 'js_vars', $dataToBePassed);
	}

}