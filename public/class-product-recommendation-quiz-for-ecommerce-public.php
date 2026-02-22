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

// Prevent direct access.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
	 * Skips loading on WooCommerce checkout and cart pages where the quiz
	 * is never rendered, so a connection timeout to our servers can never
	 * block those critical pages.
	 *
	 * The script is loaded with the 'async' strategy so that even on pages
	 * where it IS enqueued, a slow or failed connection to admin.revenuehunt.com
	 * will not block page rendering or execution.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		// Don't load the embed script on WooCommerce checkout or cart pages.
		// The quiz is never rendered there, and a connection timeout would
		// degrade the shopping experience for no benefit.
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return;
		}
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			return;
		}

		$data_to_pass = array(
			'shop'           => PRQ_STORE_URL,
			'platform'       => 'woocommerce',
			'channel'        => 'wordpress',
			'plugin_version' => PRQ_PLUGIN_VERSION,
			'woo_version'    => PRQ_WOO_VERSION,
			'wp_version'     => PRQ_WP_VERSION,
		);

		wp_enqueue_script( $this->plugin_name, PRQ_ADMIN_URL . '/embed.js?shop=' . rawurlencode( PRQ_STORE_URL ), array(), PRQ_PLUGIN_VERSION, true );
		wp_localize_script( $this->plugin_name, 'js_vars', $data_to_pass );
	}

	/**
	 * Add 'async' attribute to the embed.js script tag.
	 *
	 * This ensures that even if admin.revenuehunt.com is unreachable (e.g. due
	 * to ISP routing issues), the merchant's page continues to load and function
	 * normally. The quiz simply won't appear — graceful degradation.
	 *
	 * @since    2.3.3
	 * @param string $tag    The full script tag HTML.
	 * @param string $handle The script's registered handle.
	 * @param string $src    The script source URL.
	 * @return string Modified script tag with async attribute.
	 */
	public function add_async_to_embed_script( $tag, $handle, $src ) {
		if ( $this->plugin_name !== $handle ) {
			return $tag;
		}

		// Don't double-add if WordPress already added it (WP 6.3+ strategy support).
		if ( false !== strpos( $tag, ' async' ) ) {
			return $tag;
		}

		return str_replace( '<script ', '<script async ', $tag );
	}

}
