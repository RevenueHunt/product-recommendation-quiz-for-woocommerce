<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://revenuehunt.com/
 * @since      1.0.0
 *
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/admin
 * @author     Alex <alex@revenuehunt.com>
 */
class Product_Recommendation_Quiz_For_Ecommerce_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) { 

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/product-recommendation-quiz-for-ecommerce-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
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
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/product-recommendation-quiz-for-ecommerce-admin.js', array('jquery'), $this->version, false);
	}

	public function prquiz_get_woocommerce_auth_url() {

		$auth_base = get_site_url(null, '/wc-auth/v1/authorize/');

		$return_url = admin_url() . 'admin.php?page=prqfw';

		$callback_url = PRQ_API_URL . '/api/v1/woocommerce/create';

		$params = array(
			'app_name' => 'Product Recommendation Quiz',
			'scope' => 'read_write', // 'read', 'write', 'read_write'
			'user_id' => PRQ_STORE_URL, // Local user ID
			'return_url' => $return_url,
			'callback_url' => $callback_url, // TODO TO DO -> Must be https
		);

		// Add PHP_QUERY_RFC3986 so spaces are encoded as %20 and not +
		$query = http_build_query($params, null, '&', PHP_QUERY_RFC3986);

		return $auth_base . '?' . $query;
	}

	public function prquiz_get_oauth_url( $token, $hashid ) { 
		
		if (preg_match('/\.local/i', PRQ_STORE_URL)) {
			// development environment
			$oauth_url = 'http://localhost:9528/public/woocommerce/oauth';
		} else {
			// production environment
			$oauth_url = 'https://admin.revenuehunt.com/public/woocommerce/oauth';
		}

		return $oauth_url . '?token=' . $token . '&shop_hashid=' . $hashid . '&signature=' . time();
	}

	public function prquiz_authenticated_visit( $token, $hashid ) { 
		?>
		<div class="wrap">
			<img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/revenuehunt-logo.png'); ?>" width="24" height="24" alt="RevenueHunt Logo" /> 
			<p class="fright h-24 mtop-0" style="font-size: 14px; width: calc(100% - 35px);">Product Recommendation Quiz for eCommerce<span class="fright">by <a href="https://revenuehunt.com/" target="_blank">RevenueHunt</a></span></p>
			<iframe title="Product Recommendation Quiz" src="<?php echo esc_url($this->prquiz_get_oauth_url($token, $hashid)); ?>" name="app-iframe" context="Main" style="position: relative; border: none; width: calc(100% + 41px); margin-left: -21px; height: calc(100vh - 85px);"></iframe>
		</div>            
		<?php
	}

	public function prquiz_first_visit() {
		?>
		<div class="wrap">
			<img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/revenuehunt-logo.png'); ?>" width="24" height="24" alt="RevenueHunt Logo" /> 
			<p class="fright h-24 mtop-0" style="font-size: 14px; width: calc(100% - 35px);">Product Recommendation Quiz for eCommerce<span class="fright">by <a href="https://revenuehunt.com/" target="_blank">RevenueHunt</a></span></p>
			<hr>
			<h1 class="mtop-60 alcenter">Congratulations!</h1>
			<p class="lg alcenter">You're on step away from getting more conversions and sales in your store.</p>
			<p class="lg alcenter">We just need you to grant this app permission to access your eCommerce plugin:</p>
			<p class="lg alcenter mtop-30"><a class="btn btn-main" href="<?php echo esc_url($this->prquiz_get_woocommerce_auth_url()); ?>">grant permission now</a></p>
		</div>
		<?php
	}

	public function woocommerce_missing() {
		?>
		<div class="error">
			<p><strong>Product Recommendation Quiz for eCommerce requires the WooCommerce plugin to be installed and active. You can download <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> here. If you want this plugin developed for your eCommerce platform, please send us a message.</strong></p>
		</div>
		<?php
	}

	public function https_ssl_missing() {
		?>
		<div class="error">
			<p><strong>Product Recommendation Quiz for eCommerce requires your website to have a valid HTTPS/SSL certificate.</strong></p>
		</div>
		<?php
	}

	public function knock() {

		$locale = explode('_', get_locale());
		// TODO TO DO https://woocommerce.wp-a2z.org/oik_api/loaderget_currency_settings/
		//extract data from the post
		//set POST variables
		$url = PRQ_API_URL . '/api/v1/woocommerce/knock';
		$args = array(
			'domain' => urlencode(PRQ_STORE_URL),
			'channel' => 'wordpress',
			'plugin_version' => PRQ_PLUGIN_VERSION,
			'woo_version' => PRQ_WOO_VERSION,
			'wp_version' => PRQ_WP_VERSION,
			'name' => get_bloginfo('name'),
			'email' => get_bloginfo('admin_email'),
			'locale' => $locale[0],
			'timezone' => get_option('gmt_offset'),
			'currency' => get_woocommerce_currency(),
			'symbol' => html_entity_decode(get_woocommerce_currency_symbol()),
			'signature' => time()
		);

		$response = wp_remote_post($url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => $args,
			'cookies' => array()
				)
		);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			echo esc_html("Something went wrong: $error_message");
		} else {
			return $response;
		}


		// https://stackoverflow.com/questions/8655515/get-utc-time-in-php
		// https://stackoverflow.com/questions/2707967/php-how-can-i-generate-a-hmacsha256-signature-of-a-string
		// https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	}

	public function prquiz_options() {

		if (!class_exists('WooCommerce')) {
			// Your website doesn't appear to have WooCommerce installed and activated - ERROR
			$this->woocommerce_missing();
			die();
		}
		
		if (preg_match('/\.local/i', PRQ_STORE_URL)) {
			// Local environment - OK
			define('PRQ_HTTPS_STORE', true);
		} else if ( ( !empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS'] ) || ( !empty($_SERVER['SERVER_PORT']) && 443 == $_SERVER['SERVER_PORT'] ) ) {
			// Your website does have HTTPS - OK
			define('PRQ_HTTPS_STORE', true);
		} else {
			// Your website doesn't have HTTPS - ERROR
			$this->https_ssl_missing();
			die();
		}

		// DO A KNOCK (send domain & time signature)
		$knock = $this->knock();

		if (200 == $knock['response']['code']) {

			// check if we have auth token
			$stored_token = get_option('rh_token');
			$stored_hashid = get_option('rh_shop_hashid');

			if ($stored_token) {
				// already have permissions, go to oauth
				$token = $stored_token;
				delete_option('rh_token');
				delete_option('rh_shop_hashid');
				$this->prquiz_authenticated_visit($token, $stored_hashid);
				die();
			} else {
				$this->prquiz_first_visit();
				die();
			}
		} else {
			// echo $knock['response']['code'];
			// if 401 go to prquiz_first_visit
			$this->prquiz_first_visit();
			die();
		}
	}

	public function my_plugin_menu() {

		// if (!function_exists('my_plugin_options')) {
		// wp_die( __( 'function does\'t exist.' ) );
		// }

		add_menu_page(
				'Product Recommendation Quiz', 'Product Quiz', 'manage_options', 'prqfw', array($this, 'prquiz_options'), 'dashicons-format-chat', 58 /* https://developer.wordpress.org/reference/functions/add_menu_page/#default-bottom-of-menu-structure */
		);
	}

}
