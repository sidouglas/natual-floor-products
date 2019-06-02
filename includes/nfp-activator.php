<?php

/**
 * Fired during plugin activation
 *
 * @link       si@simondouglas.com
 * @since      1.0.0
 *
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/includes
 * @author     Simon Douglas <si@simondouglas.com>
 */
class NFP_Activator
{

  /**
   * Adds the nfc_products table if not exists
   * @since    1.0.0
   */
  public static function activate()
  {
    global $wpdb;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $charset_collate = $wpdb->get_charset_collate();

    $create_sql = "
       CREATE TABLE IF NOT EXISTS `nfc_products` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      `code` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
      `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
      `family` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
      `variant` tinytext COLLATE utf8mb4_unicode_ci,
      `status` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
      `price_retail` decimal(10,2) DEFAULT NULL,
      `price_sale` decimal(10,2) DEFAULT NULL,
      `price_unit` tinytext COLLATE utf8mb4_unicode_ci,
      `description` text COLLATE utf8mb4_unicode_ci,
      `length` int(11) DEFAULT NULL,
      `width` int(11) DEFAULT NULL,
      `rugs_only` tinyint(1) DEFAULT NULL,
      `colours` tinytext COLLATE utf8mb4_unicode_ci,
      `textures` tinytext COLLATE utf8mb4_unicode_ci,
      `img_prefix` tinytext COLLATE utf8mb4_unicode_ci,
      PRIMARY KEY (`id`)
    ) $charset_collate";

    dbDelta($create_sql);
    $results = $wpdb->get_results('SELECT COUNT(*) FROM nfc_products', ARRAY_A);
    if ((int)$results[0]['COUNT(*)'] == 0) {
      $sample_sql = "
      INSERT INTO nfc_products VALUES
        (421,'2019-04-14 00:00:00',NULL,'3280','Belgian Charcoal Herringbone Cotton','rug',NULL,'out of stock',35,NULL,'metre',NULL,NULL,45,0,NULL,NULL,NULL),
        (326,'2019-04-14 00:00:00',NULL,'3205','Belgian Poppyseed Chenille','rug',NULL,'current',37,NULL,'metre',NULL,NULL,45,0,NULL,NULL,NULL),
        (460,'2019-04-14 00:00:00',NULL,'2874','Marled Taupe Sisal','rug',NULL,'current',55,NULL,'square metre',NULL,NULL,4000,0,NULL,NULL,NULL),
        (287,'2019-04-14 00:00:00',NULL,'2872','Oyster Sisal ','rug',NULL,'current',55,NULL,'square metre',NULL,NULL,4000,0,NULL,NULL,NULL),
        (88,'2019-04-14 00:00:00',NULL,'2869','Slate Sisal ','rug',NULL,'current',55,NULL,'square metre',NULL,NULL,4000,0,NULL,NULL,NULL),
        (86,'2019-04-14 00:00:00',NULL,'2867','Oriental Rush Sisal','rug',NULL,'current',65,NULL,'square metre',NULL,NULL,4000,0,NULL,NULL,NULL),
        (84,'2019-04-14 00:00:00',NULL,'2864','Tiger Grey Sisal','rug',NULL,'current',65,NULL,'square metre',NULL,NULL,4000,0,NULL,NULL,NULL),
        (356,'2019-04-14 00:00:00',NULL,'2720','Belgian Black Pearl Sisal','rug',NULL,'current',110,NULL,'square metre',NULL,NULL,4000,0,NULL,NULL,NULL),
        (30,'2019-04-14 00:00:00',NULL,'2706','Belgian Natural Herringbone Sisal','rug',NULL,'current',120,NULL,'square metre',NULL,NULL,4000,0,NULL,NULL,NULL),
        (32,'2019-04-14 00:00:00',NULL,'2708','Belgian Grigio Mosaic Wool and Sisal','rug',NULL,'current',140,NULL,'square metre',NULL,NULL,4000,0,NULL,NULL,NULL);
    ";
      dbDelta($sample_sql);
    }
  }

}
