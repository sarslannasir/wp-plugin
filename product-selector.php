<?php
/**
 * Plugin Name: WP Product Selector
 * Plugin URI: https://wpdevshed.com/wp-product-selector/
 * Description: WP Product Selector makes it easy to deploy guided quiz style product selectors on your WooCommerce site so that consumers can find the perfect product for their specific requirements. Let your customers pick the right product based on their specific requirements.
 * Version: 1.0.6
 * Author: WP Dev Shed
 * Author URI: http://wpdevshed.com/
 * Requires at least: 4.6
 * Tested up to: 5.3.2
 * License: GPL2
 *
 * Text Domain: wp-product-selector
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if class already exist
if( ! class_exists('WP_Product_Selector')) :

/**
 * Main Product Selector
 *
 * @class WP_Product_Selector
 * @version	1.0
 */
final class WP_Product_Selector {

	/**
	 * @var WP_PLUGIN_SELECTOR The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;
	
	/**
	 * Main WP_PLUGIN_SELECTOR Instance
	 *
	 * Ensures only one instance of WP_PLUGIN_SELECTOR is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WC()
	 * @return WP_PLUGIN_SELECTOR - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Cloning is forbidden.
	 * @since  1.0
	 * @access public
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-product-selector' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since  1.0
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-product-selector' ), '1.0' );
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since  1.0
	 * @access public
	 * @return void
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong( "WP_Product_Selector::{$method}", __( 'Method does not exist.', 'wp-product-selector' ), '1.0' );
		unset( $method, $args );
		
		return null;
	}
	
	/**
	 * @desc	Construct the plugin object
	 */
	public function __construct()
	{
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}
	
	/**
	 * Define Constants
	 */
	private function define_constants() {
		$this->define( 'PLUGIN_SELECTOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'PLUGIN_SELECTOR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'PLUGIN_SELECTOR_CSS_DIR', PLUGIN_SELECTOR_PLUGIN_URL .'assets/css' );
		$this->define( 'PLUGIN_SELECTOR_JS_DIR', PLUGIN_SELECTOR_PLUGIN_URL .'assets/js' );
		$this->define( 'PLUGIN_SELECTOR_ADMIN_CSS_DIR', PLUGIN_SELECTOR_PLUGIN_URL .'assets/admin/css' );
		$this->define( 'PLUGIN_SELECTOR_ADMIN_JS_DIR', PLUGIN_SELECTOR_PLUGIN_URL .'assets/admin/js' );
		$this->define( 'PLUGIN_SELECTOR_INC_PATH', PLUGIN_SELECTOR_PLUGIN_PATH .'includes' );
		$this->define( 'PLUGIN_SELECTOR_LIB_PATH', PLUGIN_SELECTOR_PLUGIN_PATH .'library' );
	}
	
	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
	
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		include_once( PLUGIN_SELECTOR_INC_PATH . '/class-product-selector-post-type.php' );
		include_once( PLUGIN_SELECTOR_INC_PATH . '/class-product-selector-actions.php' );
		include_once( PLUGIN_SELECTOR_INC_PATH . '/class-product-selector-filters.php' );

		include_once( PLUGIN_SELECTOR_INC_PATH . '/class-product-selector-functions.php' );
		include_once( PLUGIN_SELECTOR_INC_PATH . '/class-product-selector-shortcodes.php' );
		include_once( PLUGIN_SELECTOR_INC_PATH . '/class-product-selector-ajax.php' );

		include_once( PLUGIN_SELECTOR_INC_PATH . '/class-product-selector-settings-page.php' );
	}
	
	
	/**
	 * Hook into actions and filters
	 * @since  1.0
	 */
	private function init_hooks() {
		// Init classes
		$this->post_types = new Product_Selector_Post_Type();

		add_action( 'init', array( $this->post_types, 'product_selector_register_post_types' ) );
		add_action( 'init', array( $this, 'product_selector_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'product_selector_enqueue_styles_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'product_selector_enqueue_admin_styles_scripts' ) );
		add_action( 'after_setup_theme', array( $this, 'product_selector_after_setup_theme_func' ) );


		register_activation_hook( __FILE__, array( $this->post_types, 'product_selector_rewrite_flush' ) );
	}

	public function product_selector_after_setup_theme_func() {

		global $wp_query;
		
		// Localize wp-ajax
		wp_enqueue_script( 'product-selector-ajax-script', PLUGIN_SELECTOR_JS_DIR . '/product-selector-ajax.js', array( 'jquery' ) );
		wp_localize_script( 'product-selector-ajax-script', 'ps_ajax', array(
			'url'		=> admin_url( 'admin-ajax.php' ),
			'site_url' 	=> get_bloginfo('url')
		) );

	}
	

	public function product_selector_init() {

		wp_register_style('product-selector-google-font-roboto', '//fonts.googleapis.com/css?family=Roboto:400,700,900&display=swap');

		// Scripts
		wp_register_script( 'product-selector-script', PLUGIN_SELECTOR_JS_DIR . '/script.js' , ('jquery'), '20122016' );


		/* Back-end */
		// Style
		wp_register_style( 'product-selector-admin-style', PLUGIN_SELECTOR_ADMIN_CSS_DIR . '/style.css', false, '1.0', 'all' );
		
		// Scripts
		wp_register_script( 'product-selector-admin-script', PLUGIN_SELECTOR_ADMIN_JS_DIR . '/script.js' , ('jquery'), '08062016' );
		wp_register_script( 'product-selector-remove-tax-desc', PLUGIN_SELECTOR_ADMIN_JS_DIR . '/remove-desc.js' , ('jquery'), '' );
	
	}
	
	public function product_selector_enqueue_styles_scripts() {
		// Style
		wp_enqueue_style( 'product-selector-google-font-roboto' );

		// Scripts
		wp_enqueue_script( 'product-selector-script' );
	}

	public function product_selector_enqueue_admin_styles_scripts() {
		// Style
		wp_enqueue_style( 'product-selector-admin-style' );

		
		// Scripts
		wp_enqueue_script( 'product-selector-admin-script' );
		

		$screen = get_current_screen();
		if ( 'selector-category' == $screen->taxonomy ) {
			wp_enqueue_script( 'product-selector-remove-tax-desc' );
			if ( 'edit-tags' == $screen->base ) {
				$script_params = array(
					'screen' => "tags"
				);
				
			} elseif ( 'term' == $screen->base ) {
				$script_params = array(
					'screen' => "term"
				);
			}
		}


		wp_localize_script( 'product-selector-remove-tax-desc', 'scriptParams', $script_params );
	}
	

}
	
endif;



/**
 * Returns the main instance of WP_Product_Selector to prevent the need to use globals.
 *
 * @since  1.0
 * @return WP_Product_Selector
 */
function Product_Selector() {
	return WP_Product_Selector::instance();
}

// Global for backwards compatibility.
$GLOBALS['product_selector'] = Product_Selector();