<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       si@simondouglas.com
 * @since      1.0.0
 *
 * @package    Natural_Floor_Products
 * @subpackage Natural_Floor_Products/admin/partials
 */
?>

<style type="text/css">
  .actions {
    display: none;
  }
</style>

<div class="wrap">
  <h2>
    <?php _e('NFC Product Database', $this->plugin_name); ?> <br />
    <span style="font-size:60%">Read only representation of the NFC product table</span>
  </h2>
  <form id="<?=$this->plugin_name?>" method="get">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <?php $this->admin_product_table->display(); ?>
  </form>
</div>

