<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              si@simondouglas.com
 * @since             1.0.0
 * @package           Natural_Floor_Products
 *
 * @wordpress-plugin
 * Plugin Name:       Natural Floor Products
 * Plugin URI:        natural-floor-products
 * Description:       This adds a read only table, REST endpoint for the NFC product table
 * Version:           1.0
 * Author:            Simon Douglas
 * Author URI:        si@simondouglas.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       natural-floor-products
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('NATURAL_FLOOR_PRODUCTS_VERSION', '1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/nfp-activator.php
 */
function activate_natural_floor_products()
{
  require_once plugin_dir_path(__FILE__) . 'includes/nfp-activator.php';
  NFP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/nfp-deactivator.php
 */
function deactivate_natural_floor_products()
{
  require_once plugin_dir_path(__FILE__) . 'includes/nfp-deactivator.php';
  NFP_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_natural_floor_products');
register_deactivation_hook(__FILE__, 'deactivate_natural_floor_products');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/nfp-products.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_natural_floor_products()
{

  $plugin = new NFP_Products();
  $plugin->run();

}

run_natural_floor_products();
