<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       si@simondouglas.com
 * @since      1.0.0
 *
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/includes
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
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/includes
 * @author     Simon Douglas <si@simondouglas.com>
 */
class NFP_Products
{

  /**
   * The loader that's responsible for maintaining and registering all hooks that power
   * the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      NFP_loader $loader Maintains and registers all hooks for the plugin.
   */
  protected $loader;

  /**
   * The unique identifier of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $plugin_name The string used to uniquely identify this plugin.
   */
  protected $plugin_name;

  /**
   * The current version of the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $version The current version of the plugin.
   */
  protected $version;

  protected $table_name;
  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function __construct()
  {
    if (defined('NATURAL_FLOOR_PRODUCTS_VERSION')) {
      $this->version = NATURAL_FLOOR_PRODUCTS_VERSION;
    } else {
      $this->version = '1';
    }
    $this->plugin_name = 'natural-floor-products';
    $this->table_name = 'nfc_products';

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
   * - Natural_Floor_Products_Loader. Orchestrates the hooks of the plugin.
   * - Natural_Floor_Products_i18n. Defines internationalization functionality.
   * - Natural_Floor_Products_Admin. Defines all hooks for the admin area.
   * - Natural_Floor_Products_Public. Defines all hooks for the public side of the site.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_dependencies()
  {

    /*utility classes */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/nfp-singleton.php';

    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/nfp_validation.php';


    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/nfp-loader.php';

    /**
     * The class responsible for defining internationalization functionality
     * of the plugin.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/nfp-i18n.php';

    /**
     * The class responsible for defining all actions that occur in the admin area.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/nfp-admin.php';

    /**
     * The class responsible for defining all actions that occur in the public-facing
     * side of the site.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'public/nfp-public.php';

    $this->loader = new NFP_loader();

  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the Natural_Floor_Products_i18n class in order to set the domain and to register the hook
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function set_locale()
  {

    $plugin_i18n = new NFP_i18n();

    $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

  }

  /**
   * Register all of the hooks related to the admin area functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_admin_hooks()
  {

    $plugin_admin = new NFP_Admin($this->get_plugin_name(), $this->get_version(), $this->table_name);
    $plugin_options_page = NFP_Options_Page::get_instance();

    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    $this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu');
    // options page
    $this->loader->add_action('admin_menu', $plugin_options_page, 'add_menu');
    $this->loader->add_action('admin_init', $plugin_options_page, 'settings');
  }

  /**
   * Register all of the hooks related to the public-facing functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_public_hooks()
  {

    $plugin_public = new NFP_Public($this->get_plugin_name(), $this->get_version(), $this->table_name);

    $this->loader->add_action('rest_api_init', $plugin_public, 'rest_api_init');
    $this->loader->add_action('rest_api_init', $plugin_public, 'allow_cors');
  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   *
   * @since    1.0.0
   */
  public function run()
  {
    $this->loader->run();
  }

  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   *
   * @since     1.0.0
   * @return    string    The name of the plugin.
   */
  public function get_plugin_name()
  {
    return $this->plugin_name;
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @since     1.0.0
   * @return    NFP_loader    Orchestrates the hooks of the plugin.
   */
  public function get_loader()
  {
    return $this->loader;
  }

  /**
   * Retrieve the version number of the plugin.
   *
   * @since     1.0.0
   * @return    string    The version number of the plugin.
   */
  public function get_version()
  {
    return $this->version;
  }

}
