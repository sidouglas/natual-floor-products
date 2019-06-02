<?php
include_once 'nfp-db.php';

class NFP_Product_DB extends NFP_DB
{

  public function __construct($table_name, $version)
  {
    parent::__construct();
    $this->table_name = $table_name;
    $this->primary_key = 'id';
    $this->version = $version;
  }

  public function get_show_columns()
  {
    return NFP_Cache::get_transient($this->table_name . '_show_columns', function () {
      return parent::get_show_columns();
    });
  }

  public function get_columns()
  {
    return NFP_Cache::get_transient($this->table_name . '_get_columns', function () {
      return parent::get_columns();
    });
  }

}
