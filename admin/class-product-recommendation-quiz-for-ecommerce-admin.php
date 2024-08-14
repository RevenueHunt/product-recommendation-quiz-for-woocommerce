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
	 * Register the stylesheets and JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/product-recommendation-quiz-for-ecommerce-admin.css', array(), $this->version, 'all');
		
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

	public function prquiz_get_woocommerce_auth_url() {

		$auth_base = get_site_url(null, '/wc-auth/v1/authorize/');

		$return_url = admin_url() . 'admin.php?page=prqfw';

		$callback_url = PRQ_API_URL . '/api/v1/woocommerce/create';

		$params = array(
			'app_name' => 'Product Recommendation Quiz',
			'scope' => 'read_write', // 'read', 'write', 'read_write'
			'user_id' => PRQ_STORE_URL, // Local user ID
			'return_url' => $return_url,
			'callback_url' => $callback_url, // Must be https
		);

		// Add PHP_QUERY_RFC3986 so spaces are encoded as %20 and not +
		$query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

		return $auth_base . '?' . $query;
	}
	
	public function prquiz_get_oauth_url () {
		
		if (preg_match('/\.local/i', PRQ_STORE_URL)) {
			// development environment
			$oauth_url = 'http://localhost:9528/public/woocommerce/oauth';
		} else {
			// production environment
			$oauth_url = 'https://admin.revenuehunt.com/public/woocommerce/oauth';
		}
		
		$shop_hashid	= get_option('rh_shop_hashid');
		$api_key		= get_option('rh_api_key');
		$country		= WC()->countries->get_base_country();
		$time			= time();
		
		$data = sprintf('hashid=%s&domain=%s&plugin_version=%s&timestamp=%s', $shop_hashid, PRQ_STORE_URL, PRQ_PLUGIN_VERSION, (string) $time );
		$hmac = base64_encode(hash_hmac('sha256', $data, $api_key, true));
				
		$request = array(
			'timestamp' => $time,
			'domain' => urlencode(PRQ_STORE_URL),
			'shop_hashid' => $shop_hashid,
 			'channel' => 'wordpress',
 			'country' => $country,
			'plugin_version' => PRQ_PLUGIN_VERSION,
			'woo_version' => PRQ_WOO_VERSION,
			'wp_version' => PRQ_WP_VERSION,
			'name' => get_bloginfo('name'),
			'email' => get_bloginfo('admin_email'),
			'locale' => explode('_', get_locale())[0],
			'timezone' => get_option('gmt_offset'),
			'currency' => get_woocommerce_currency(),
			'symbol' => html_entity_decode(get_woocommerce_currency_symbol(), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401),
			'hmac' => urlencode($hmac)
		);
		
		$create = $oauth_url . '?';
		
		foreach ($request as $key => $value) {
			$create .= $key . '=' . $value . '&';
		}

		$create = trim($create, ' &' );
		return $create;
	}

	public function prquiz_authenticated_visit() { 
		?>
		<div class="wrap">
			<img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/revenuehunt-logo.png'); ?>" width="24" height="24" alt="<?php esc_html_e( 'RevenueHunt', 'product-recommendation-quiz-for-ecommerce' ); ?>" /> 
			<p class="fright h-24 mtop-0 prq-author">
				<?php esc_html_e( 'Product Recommendation Quiz for eCommerce', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<span class="fright"><?php esc_html_e( 'by', 'product-recommendation-quiz-for-ecommerce' ); ?> 
					<a href="https://revenuehunt.com/" target="_blank"><?php esc_html_e( 'RevenueHunt', 'product-recommendation-quiz-for-ecommerce' ); ?></a>
				</span>
			</p>
			<iframe title="<?php esc_html_e( 'Product Recommendation Quiz for eCommerce', 'product-recommendation-quiz-for-ecommerce' ); ?>" src="<?php echo esc_url($this->prquiz_get_oauth_url()); ?>" name="app-iframe" context="Main" class="prq-iframe"></iframe>
		</div>
		<?php
	}

	public function prquiz_first_visit() {
		?>
		<div class="wrap">
			<img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/revenuehunt-logo.png'); ?>" width="24" height="24" alt="<?php esc_html_e( 'RevenueHunt', 'product-recommendation-quiz-for-ecommerce' ); ?>" /> 
			<p class="fright h-24 mtop-0 prq-author">
				<?php esc_html_e( 'Product Recommendation Quiz for eCommerce', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<span class="fright"><?php esc_html_e( 'by', 'product-recommendation-quiz-for-ecommerce' ); ?> 
					<a href="https://revenuehunt.com/" target="_blank"><?php esc_html_e( 'RevenueHunt', 'product-recommendation-quiz-for-ecommerce' ); ?></a>
				</span>
			</p>
			<hr>
			<h1 class="mtop-60 alcenter"><?php esc_html_e( 'Congratulations!', 'product-recommendation-quiz-for-ecommerce' ); ?></h1>
			<p class="lg alcenter"><?php esc_html_e( 'You\'re one step away from getting more conversions and sales in your store.', 'product-recommendation-quiz-for-ecommerce' ); ?></p>
			<p class="lg alcenter"><?php esc_html_e( 'We just need you to grant this plugin permission to access your WooCommerce store:', 'product-recommendation-quiz-for-ecommerce' ); ?></p>
			<p class="lg alcenter mtop-30">
				<a class="btn btn-main" href="<?php echo esc_url($this->prquiz_get_woocommerce_auth_url()); ?>"><?php esc_html_e( 'grant permission now', 'product-recommendation-quiz-for-ecommerce' ); ?></a>
			</p>
			<p class="alcenter mtop-30">
				<?php esc_html_e( 'Are you having trouble granting access? ', 'product-recommendation-quiz-for-ecommerce' ); ?><?php esc_html_e( 'Check out ', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<a href="https://revenuehunt.com/faqs/troubleshooting-product-recommendation-quiz-app-issues-for-wordpress-woocommerce/" target="_blank"><?php esc_html_e( 'this article', 'product-recommendation-quiz-for-ecommerce' ); ?></a>
			</p>
		</div>
		<?php
	}

	public function check_plain_permalink() {
		// Get the current permalink structure
		$permalink_structure = get_option('permalink_structure');
	
		// Check if permalinks are set to "Plain"
		if (empty($permalink_structure)) {
			return true;
		} else {
			return false;
		}
	}

	public function woocommerce_missing() {
		?>
		<div class="error">
			<p><strong><?php esc_html_e( 'Product Recommendation Quiz for eCommerce requires the WooCommerce plugin to be installed and active. You can download', 'product-recommendation-quiz-for-ecommerce' ); ?> 
					<a href="https://wordpress.org/plugins/woocommerce/" target="_blank"><?php esc_html_e( 'WooCommerce', 'product-recommendation-quiz-for-ecommerce' ); ?></a> 
					<?php esc_html_e( 'here. If you want this plugin developed for your eCommerce platform, please send us a message.', 'product-recommendation-quiz-for-ecommerce' ); ?></strong></p>
		</div>
		<?php
	}

	public function https_ssl_missing() {
		?>
		<div class="error">
			<p><strong><?php esc_html_e( 'Product Recommendation Quiz for eCommerce requires your website to have a valid HTTPS/SSL certificate.', 'product-recommendation-quiz-for-ecommerce' ); ?></strong></p>
		</div>
		<?php
	}

	public function is_localhost() {
		?>
		<div class="error">
			<p><strong><?php esc_html_e( 'This plugin does not work on local environments. It needs to be installed on a live website. Your website needs to be public and not hidden by a site under construction plugin because it needs connection to our server in order to work.', 'product-recommendation-quiz-for-ecommerce' ); ?></strong></p>
		</div>
		<?php
	}

	public function plain_permalink_warning() {
		?>
		<div class="error">
			<p><strong><?php esc_html_e( 'Your current permalink structure is set to "Plain". For this plugin to authenticate correctly, a different permalink structure (such as "Post name") is required.', 'product-recommendation-quiz-for-ecommerce' ); ?></strong></p>
			<p><?php esc_html_e( 'Please update your permalink settings under ', 'product-recommendation-quiz-for-ecommerce' ); ?><a href="<?php echo esc_url(admin_url('options-permalink.php')); ?>"><?php esc_html_e( 'Settings > Permalinks', 'product-recommendation-quiz-for-ecommerce' ); ?></a><?php esc_html_e(' to ensure seamless authentication.', 'product-recommendation-quiz-for-ecommerce' ); ?></p>
		</div>
		<?php
	}
	
	public function wpml_active() {
		?>
		<div class="error">
			<p><strong><?php esc_html_e( 'There\'s an issue with the WPML Multilingual CMS plugin which interferes with the authentication process of other plugins. Please deactivate the WPML Multilingual CMS plugin temporarily, you can reactivate it later.', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<?php esc_html_e( 'More info', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<a href="https://revenuehunt.com/faqs/woocommerce-authentication-error-404-not-found-missing-parameter-app-name/" target="_blank"><?php esc_html_e( 'here', 'product-recommendation-quiz-for-ecommerce' ); ?></a>.
				</strong></p>
		</div>
		<?php
	}

	public function wp_json_error() {
		?>
		<div class="error">
			<p><strong>
				<?php esc_html_e( 'It seems like there\'s something interfering with your', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<a href="https://developer.wordpress.org/rest-api/" target="_blank"><?php esc_html_e( 'WordPress REST API', 'product-recommendation-quiz-for-ecommerce' ); ?></a>.
				<?php esc_html_e( 'This needs to be fixed in order to grant access to this plugin.', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<?php esc_html_e( 'More info', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<a href="https://revenuehunt.com/faqs/woocommerce-authentication-error-404-not-found-missing-parameter-app-name/" target="_blank"><?php esc_html_e( 'here', 'product-recommendation-quiz-for-ecommerce' ); ?></a>. <?php esc_html_e( 'We\'re getting the following error accessing your WooCommerce API from our server:', 'product-recommendation-quiz-for-ecommerce' ); ?>
				</strong></p>
		</div>
		<?php
	}

	public function wp_json_error_html_content_type( $domain ) {
		?>
		<div class="error">
			<p><strong>
				<?php esc_html_e( 'The following REST API endpoint is returning a valid JSON but the returned content-type is text/html instead of the expected application/json:', 'product-recommendation-quiz-for-ecommerce' ); ?>
				<a href="https://<?php echo esc_html_e($domain); ?>/wp-json/wc/v3/" target="_blank">https://<?php echo esc_html_e($domain); ?>/wp-json/wc/v3/</a>
			</strong></p>
		</div>
		<?php
	}

	public function wp_json_error_body( $wp_api_check_body ) {
		?>
		<div class="error">
			<p><strong><?php echo esc_html( wp_strip_all_tags( $wp_api_check_body ) ); ?></strong></p>
		</div>
		<?php
	}

	public function migration_warning() {
		?>
		<div class="error">
			<p><strong><?php esc_html_e( 'We\'ve detected that you\'ve changed the domain name. We\'re migrating your Product Recommendation Quiz account from', 'product-recommendation-quiz-for-ecommerce' ); ?> 
				<?php echo esc_html( get_option('rh_domain') ); ?> <?php esc_html_e( 'to', 'product-recommendation-quiz-for-ecommerce' ); ?> <?php echo esc_html( PRQ_STORE_URL ); ?></p>
			<p><?php esc_html_e( 'Please', 'product-recommendation-quiz-for-ecommerce' ); ?> 
				<a href="https://revenuehunt.com/contact/" target="_blank"><?php esc_html_e( 'contact us', 'product-recommendation-quiz-for-ecommerce' ); ?></a> 
				<?php esc_html_e( 'if you encounter any issues.', 'product-recommendation-quiz-for-ecommerce' ); ?></strong></p>
		</div>
		<?php
	}
	
	public function isJson( $string ) {
		json_decode($string);
		return json_last_error() === JSON_ERROR_NONE;
	}
	
	public function api_check_json ( $domain ) {
		if ( !function_exists( 'curl_init' ) ) { 
			die('CURL is not installed!');
		}
		$url = 'https://api.revenuehunt.com/api/v1/woocommerce/check?domain=' . $domain;
		$ch = curl_init();
		
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$user_agent = sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] );
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);			
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		/* curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Uncomment only for debugging locally */
		if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return array($httpcode,$output);
	}

	public function check_wpml() {
		if ( function_exists('icl_object_id') ) {
			// WPML is active
			$shouldCallWpmlActive = true; // Flag to determine if we should trigger warning
	
			if ( defined('ICL_SITEPRESS_VERSION') ) {
				$current_version = ICL_SITEPRESS_VERSION;
				if (version_compare($current_version, '4.5.0', '>=')) {
					// WPML version is 4.5.0 or higher, no need to trigger warning
					// https://wpml.org/errata/endpoints-containing-slashes-are-incorrectly-encoded/
					$shouldCallWpmlActive = false;
				}
			}

			if ($shouldCallWpmlActive) {
				$this->wpml_active();
				die();
			}
		}
	}

	public function prquiz_options() {

		$domain = parse_url(site_url(), PHP_URL_HOST);					

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

		if ('localhost' == $domain) {
			// Does not work on local environments
			$this->is_localhost();
			die();
		}

		if ( $this->check_plain_permalink() ) {
			$this->plain_permalink_warning();
			die();
		}
		
		$wp_api_check = $this->api_check_json(PRQ_STORE_URL);
		
		/* RESPONSE CODES:
		200 success, OK
		400 invalid domain was passed
		404 invalid JSON received 
		500 valid domain but no connection
		429 tested more than 10 times per minute
		*/
		
		if ( 404 === $wp_api_check[0] ) {
			$this->wp_json_error();

			$wp_api_check_json = json_decode($wp_api_check[1]);
			$is_html_type = strpos($wp_api_check_json->content_type, 'text/html') !== false;
			$is_json_body = $this->isJson($wp_api_check_json->body);

			if ($is_html_type && $is_json_body) {
				$this->wp_json_error_html_content_type(PRQ_STORE_URL);
			}

			$this->wp_json_error_body($wp_api_check_json->body);
			die();
		}
		
		// NEW OAUTH
		$shop_hashid = get_option('rh_shop_hashid');
				
		if ($shop_hashid) {
			// already have permissions, go to oauth
			$this->prquiz_authenticated_visit();
		} else {
			// needs to receive credentials from our server
			// check if WPML is active, it causes authentication issues
			// https://stackoverflow.com/questions/65776787/woocommerce-is-encoding-the-authorization-endpoint
			$this->check_wpml();
			$this->prquiz_first_visit();
		}
	}

	public function my_plugin_menu() {

		add_menu_page(
				'Product Recommendation Quiz', 'Product Quiz', 'manage_options', 'prqfw', array($this, 'prquiz_options'), 'dashicons-format-chat', 58 /* https://developer.wordpress.org/reference/functions/add_menu_page/#default-bottom-of-menu-structure */
		);
	}

}