<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       si@simondouglas.com
 * @since      1.0.0
 *
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/admin
 */
include 'nfp-products-table.php';
include 'nfp-options-page.php';
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/admin
 * @author     Simon Douglas <si@simondouglas.com>
 */
class NFP_Admin
{

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $plugin_name The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $version The current version of this plugin.
   */
  private $version;

  /**
   * @var $admin_product_table
   * The instance of the product table
   */
  private $admin_product_table;
  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string $plugin_name The name of this plugin.
   * @param      string $version The version of this plugin.
   * @param      string $table_name the table_name of this product table
   */
  public function __construct($plugin_name, $version, $table_name)
  {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->admin_product_table  = $table_name;
  }

  public function admin_menu()
  {
    $page_hook = add_menu_page(
      'NFC Products',
      'NFC Products',
      'edit_posts',
      'nfc-products-admin',
      function () {
        require_once dirname(__FILE__) . '/partials/nfp-admin-display.php';
      },
      'dashicons-cart',
      3
    );

    add_action( 'load-'.$page_hook, function(){
      $this->admin_product_table = new NFP_Products_Table($this->plugin_name, $this->admin_product_table);
      $this->admin_product_table->prepare_items();
    });
  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles()
  {
   // wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/natural-floor-products-admin.css', array(), $this->version, 'all');
  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts()
  {
   // wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/natural-floor-products-admin.js', array('jquery'), $this->version, false);
  }
}
