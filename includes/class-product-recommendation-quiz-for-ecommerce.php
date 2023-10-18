<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://revenuehunt.com/
 * @since      1.0.0
 *
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Product_Recommendation_Quiz_For_Ecommerce
 * @subpackage Product_Recommendation_Quiz_For_Ecommerce/includes
 */
class Product_Recommendation_Quiz_For_Ecommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @var      Product_Recommendation_Quiz_For_Ecommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		$this->version = PRQ_PLUGIN_VERSION;
		
    	// Assign $storeurl based on the extracted domain or fall back to get_site_url()
		$currentUrl = $this->getCurrentUrlSanitized();
		$storeurl = $this->extractDomainAndPath($currentUrl) ? $this->extractDomainAndPath($currentUrl) : get_site_url();
		
		// Remove 'http://' or 'https://'
		$storeurl = preg_replace('#^https?://#', '', $storeurl);

		/* DEFINE CONSTANTS */
		define('PRQ_STORE_URL', $storeurl);
		define('PRQ_WOO_VERSION', $this->get_woo_version());
		define('PRQ_WP_VERSION', get_bloginfo('version'));
				
		if (preg_match('/\.local/i', PRQ_STORE_URL)) {
			// development environment
			// ssh -R 80:localhost:3000 ssh.localhost.run
			define('PRQ_API_URL', 'https://xxx-xxx.localhost.run');
			define('PRQ_ADMIN_URL', 'http://localhost:9528');
		} else {
			// production environment
			define('PRQ_API_URL', 'https://api.revenuehunt.com');
			define('PRQ_ADMIN_URL', 'https://admin.revenuehunt.com');
		}

		$this->plugin_name = 'product-recommendation-quiz-for-ecommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();		
	}

	private function getCurrentUrlSanitized() {
		// Use WordPress's is_ssl() to check for HTTPS
		$scheme = is_ssl() ? 'https' : 'http';

		$host = false;
		$requestUri = '';
		
		// Use esc_url_raw() to sanitize the host and request URI
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$host = esc_url_raw($_SERVER['HTTP_HOST']);
		}
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$requestUri = esc_url_raw($_SERVER['REQUEST_URI']);
		}

		if ( !$host ) {
			return false;
		}
		
		return $scheme . '://' . $host . $requestUri;
	}

	private function extractDomainAndPath($url) {

		if ( !$url ) {
			return false;
		}

		$pattern = '/https?:\/\/(.*?)\/wp-admin\//';
		preg_match($pattern, $url, $matches);
		
		return isset($matches[1]) ? $matches[1] : false;
	}

	private function get_woo_version() {
		// If get_plugins() isn't available, require it
		if ( !function_exists('get_plugins') ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );			
		}

		// Create the plugins folder and file variables
		$plugin_folder = get_plugins('/woocommerce');
		$plugin_file = 'woocommerce.php';

		// If the plugin version number is set, return it 
		if (isset($plugin_folder[$plugin_file]['Version'])) {
			return $plugin_folder[$plugin_file]['Version'];
		} else {
			// Otherwise return null
			return null;
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Product_Recommendation_Quiz_For_Ecommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Product_Recommendation_Quiz_For_Ecommerce_I18n. Defines internationalization functionality.
	 * - Product_Recommendation_Quiz_For_Ecommerce_Admin. Defines all hooks for the admin area.
	 * - Product_Recommendation_Quiz_For_Ecommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-product-recommendation-quiz-for-ecommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-product-recommendation-quiz-for-ecommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-product-recommendation-quiz-for-ecommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-product-recommendation-quiz-for-ecommerce-public.php';

		$this->loader = new Product_Recommendation_Quiz_For_Ecommerce_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Product_Recommendation_Quiz_For_Ecommerce_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new Product_Recommendation_Quiz_For_Ecommerce_I18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Product_Recommendation_Quiz_For_Ecommerce_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'my_plugin_menu');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_public_hooks() {

		$plugin_public = new Product_Recommendation_Quiz_For_Ecommerce_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Product_Recommendation_Quiz_For_Ecommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
