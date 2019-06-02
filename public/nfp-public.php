<?php
require_once 'nfp-rest-controller.php';
/**
 * The public-facing functionality of the plugin.
 *
 * @link       si@simondouglas.com
 * @since      1.0.0
 *
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/public
 * @author     Simon Douglas <si@simondouglas.com>
 */
class NFP_Public
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
  private $table_name;
  private $rest_controller;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string $plugin_name The name of the plugin.
   * @param      string $version The version of this plugin.
   */
  public function __construct($plugin_name, $version, $table_name)
  {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->table_name = $table_name;
  }


  public function allow_cors()
  {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');

    add_filter('rest_pre_serve_request', function ($value) {

      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Methods: GET HEAD POST');

      return $value;

    }, 15);
  }

  public function rest_api_init()
  {
    $this->rest_controller = new NFP_Rest_Controller($this->plugin_name, $this->version, $this->table_name);
    $this->rest_controller->register_routes();
  }

}
