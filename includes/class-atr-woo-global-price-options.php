<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.atarimtr.com/
 * @since      1.0.0
 *
 * @package    Atr_Woo_Gpo
 * @subpackage Atr_Woo_Gpo/includes
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
 * @package    Atr_Woo_Gpo
 * @subpackage Atr_Woo_Gpo/includes
 * @author     Yehuda Tiram <yehuda@atarimtr.co.il>
 */
class Atr_Woo_Gpo {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Atr_Woo_Gpo_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
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

		$this->plugin_name = 'atr-woo-global-price-options';
		$this->version = '1.0.5';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Atr_Woo_Gpo_Loader. Orchestrates the hooks of the plugin.
	 * - Atr_Woo_Gpo_i18n. Defines internationalization functionality.
	 * - Atr_Woo_Gpo_Admin. Defines all hooks for the admin area.
	 * - Atr_Woo_Gpo_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-atr-woo-global-price-options-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-atr-woo-global-price-options-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-atr-woo-global-price-options-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-atr-woo-global-price-options-public.php';
		
		$this->loader = new Atr_Woo_Gpo_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Atr_Woo_Gpo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Atr_Woo_Gpo_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Atr_Woo_Gpo_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$plugin_settings = new Atr_Woo_Gpo_Admin_Settings( $this->get_plugin_name(), $this->get_version() );	
		$this->loader->add_action( 'admin_init', $plugin_settings, 'init' );
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'add_menu_item' );
		$this->loader->add_action( 'admin_notices', $plugin_settings, 'display_gpo_settings_notices' );
		$plugin_basename = $this->plugin_name . '/' . 'atr-woo-global-price-options.php';
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_settings, 'add_action_links' );	
		$this->loader->add_action( 'admin_init', $plugin_settings, 'atr_gpos_settings_export' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'atr_gpo_settings_import' );					
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Atr_Woo_Gpo_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_filter( 'woocommerce_loop_add_to_cart_link', $plugin_public, 'replace_loop_add_to_cart_button', 10, 2 );		
		$this->loader->add_filter( 'woocommerce_is_sold_individually', $plugin_public, 'atr_remove_all_quantity_fields', 10, 2 );	
		
		$this->loader->add_filter( 'woocommerce_is_purchasable', $plugin_public, 'make_product_purchasable', 10, 2 );
		$this->loader->add_action( 'woocommerce_before_add_to_cart_button', $plugin_public, 'add_usage_field_single_product', 10, 2 );
		$this->loader->add_action( 'woocommerce_get_price_html', $plugin_public, 'woocommerce_remove_prices', 10, 2 );
		$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $plugin_public, 'add_to_cart_item_validation', 10, 3 );			

		// Display item license field under cart item 
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_public, 'display_use_ttl_in_cart', 10, 2 );		
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'before_calculate_totals', 10, 1 );
		$this->loader->add_filter( 'atr_gpo_before_price_options', $plugin_public, 'before_price_options', 10, 1 ) ;
		$this->loader->add_filter( 'atr_gpo_after_price_options', $plugin_public, 'after_price_options', 10, 1  );
		$this->loader->add_filter( 'atr_gpo_before_price_options_item', $plugin_public, 'before_price_options_item', 10, 1 ) ;
		$this->loader->add_filter( 'atr_gpo_after_price_options_item', $plugin_public, 'after_price_options_item', 10, 1  );	
		
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'add_cart_item_option_label_to_order_items', 10, 4 );
	}

	// public function activate_price_options_for_categories(){
	// 	$arr_cats = [];
	// 	$options = get_option($this->plugin_name);		
	// 	if ( $options ) {
	// 		foreach ($options as $key => $val) {
	// 			if (is_array($val) && ($key == 'categories_checkboxes')){
	// 				foreach ($val as $key_inner => $val_inner) {
	// 					$arr_cats[] = $val_inner; 
	// 					//echo 'key_inner - ' . $key_inner . ' => ' .  $val_inner . '<br />';
	// 				}
	// 			}
	// 		}				
	// 	}
	// 	return $arr_cats;
	// }	
	
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
	 * @return    Atr_Woo_Gpo_Loader    Orchestrates the hooks of the plugin.
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
