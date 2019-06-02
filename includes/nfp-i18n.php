<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       si@simondouglas.com
 * @since      1.0.0
 *
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/includes
 * @author     Simon Douglas <si@simondouglas.com>
 */
class NFP_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'natural-floor-products',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
