<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 *
 * @package     EDD
 * @subpackage  Classes/EDD DB
 * @since 1.0
 */
abstract class NFP_DB
{

  /**
   * The name of our database table
   *
   * @since 1.0
   */
  public $table_name;

  /**
   * The version of our database table
   *
   * @since 1.0
   */
  public $version;

  /**
   * The name of the primary column
   *
   * @since 1.0
   */
  public $primary_key;

  // meta data for the product table columns
  protected $show_columns = [];

  /**
   * Get things started
   *
   * @since 1.0
   */
  public function __construct()
  {
  }


  /**
   * @return array
   */
  public function get_show_columns()
  {
    global $wpdb;
    if (count($this->show_columns) == 0) {
      $result = $wpdb->get_results("SHOW COLUMNS FROM {$this->table_name}");
      foreach ($result as $obj) {
        $this->show_columns[$obj->Field] = $obj;
      }
    }
    return $this->show_columns;
  }

  /**
   * Get columns and formats
   *
   * @access  public
   * @since 1.0
   */
  public function get_columns()
  {
    $columns = [];
    foreach ($this->get_show_columns() as $obj) {
      $columns[$obj->Field] = $this->determine_type($obj->Type)['prepared'];
    }
    return $columns;
  }

  /**
   * Default column values
   *
   * @since 1.0
   * @return  array
   */
  public function get_column_defaults()
  {
    $columns = [];
    foreach ($this->get_show_columns() as $obj) {
      if ($obj->Default == null) {
        $columns[$obj->Field] = '';
      } elseif ($obj->Type == 'timestamp') {
        $columns[$obj->Field] = date('Y-m-d H:i:s');
      } elseif ($obj->Type == 'datetime') {
        $columns[$obj->Field] = time();
      } else {
        $columns[$obj->Field] = $obj->Default;
      }
    }
    return $columns;
  }

  /**
   * Retrieve a row by the primary key
   *
   * @since 1.0
   * @return  object
   */
  public function get($row_id)
  {
    global $wpdb;
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->table_name WHERE $this->primary_key = %s LIMIT 1;", $row_id));
  }

  public function get_all()
  {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM {$this->table_name}");
  }

  /**
   * Retrieve a row by a specific column / value
   *
   * @since 1.0
   * @return  object
   */
  public function get_by($column, $row_id)
  {
    global $wpdb;
    $column = esc_sql($column);
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->table_name WHERE $column = %s LIMIT 1;", $row_id));
  }

  /**
   * Retrieve a specific column's value by the primary key
   *
   * @since 1.0
   * @return  string
   */
  public function get_column($column, $row_id)
  {
    global $wpdb;
    $column = esc_sql($column);
    return $wpdb->get_var($wpdb->prepare("SELECT $column FROM $this->table_name WHERE $this->primary_key = %s LIMIT 1;", $row_id));
  }

  /**
   * Retrieve a specific column's value by the the specified column / value
   *
   * @since 1.0
   * @return  string
   */
  public function get_column_by($column, $column_where, $column_value)
  {
    global $wpdb;
    $column_where = esc_sql($column_where);
    $column = esc_sql($column);
    return $wpdb->get_var($wpdb->prepare("SELECT $column FROM $this->table_name WHERE $column_where = %s LIMIT 1;", $column_value));
  }

  /**
   * Insert a new row
   *
   * @since 1.0
   * @return  int
   */
  public function insert($data, $type = '')
  {
    global $wpdb;

    // Set default values
    $data = wp_parse_args($data, $this->get_column_defaults());

    // Initialise column format array
    $column_formats = $this->get_columns();

    // Force fields to lower case
    $data = array_change_key_case($data);

    // White list columns
    $data = array_intersect_key($data, $column_formats);

    // Reorder $column_formats to match the order of columns given in $data
    $data_keys = array_keys($data);
    $column_formats = array_merge(array_flip($data_keys), $column_formats);

    $wpdb->insert($this->table_name, $data, $column_formats);
    $wpdb_insert_id = $wpdb->insert_id;

    return $wpdb_insert_id;
  }

  /**
   * Update a row
   *
   * @since 1.0
   * @return  bool
   */
  public function update($row_id, $data = [], $where = '')
  {

    global $wpdb;

    // Row ID must be positive integer
    $row_id = absint($row_id);

    if (empty($row_id)) {
      return false;
    }

    if (empty($where)) {
      $where = $this->primary_key;
    }

    // Initialise column format array
    $column_formats = $this->get_columns();

    // Force fields to lower case
    $data = array_change_key_case($data);

    // White list columns
    $data = array_intersect_key($data, $column_formats);

    // Reorder $column_formats to match the order of columns given in $data
    $data_keys = array_keys($data);
    $column_formats = array_merge(array_flip($data_keys), $column_formats);

    if (false === $wpdb->update($this->table_name, $data, array($where => $row_id), $column_formats)) {
      return false;
    }

    return true;
  }

  /**
   * Delete a row identified by the primary key
   *
   * @since 1.0
   * @return  bool
   */
  public function delete($row_id = 0)
  {

    global $wpdb;

    // Row ID must be positive integer
    $row_id = absint($row_id);

    if (empty($row_id)) {
      return false;
    }

    if (false === $wpdb->query($wpdb->prepare("DELETE FROM $this->table_name WHERE $this->primary_key = %d", $row_id))) {
      return false;
    }

    return true;
  }

  /**
   * Check if the given table exists
   *
   * @since 1.0
   * @param  string $table The table name
   * @return bool          If the table name exists
   */
  public function table_exists($table)
  {
    global $wpdb;
    $table = sanitize_text_field($table);

    return $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE '%s'", $table)) === $table;
  }

  /**
   * Check if the table was ever installed
   *
   * @since 1.0
   * @return bool Returns if the customers table was installed and upgrade routine run
   */
  public function installed()
  {
    return $this->table_exists($this->table_name);
  }

  /**
   * This method is needed for prepared statements. They require
   * the data type of the field to be bound with "i" s", etc.
   * This function takes the input, determines what type it is,
   * and then updates the param_type.
   *
   * @param $item string to determine the type.
   * @return Array The joined parameter types.
   */
  public function determine_type($item)
  {

    $item = strtolower($item);

    $type = [
      'prepared' => '%s',
      'primitive' => 'string',
    ];

    if ($item == 'timestamp') {
      $type['type'] = 'timestamp';
    }

    if ($item == 'datetime') {
      $type['type'] = 'datetime';
    }

    if ($item == 'date') {
      $type['type'] = 'datetime';
    }

    if (strpos($item, 'int') !== false) {
      $type['prepared'] = '%d';
      $type['primitive'] = 'integer';
    }

    if (strpos($item, 'decimal') !== false) {
      $type['prepared'] = '%f';
      $type['primitive'] = 'float';
    }

    if (strpos($item, 'float') !== false) {
      $type['prepared'] = '%f';
      $type['primitive'] = 'float';
    }

    if (strpos($item, 'double') !== false) {
      $type['prepared'] = '%f';
      $type['primitive'] = 'float';
    }

    return $type;
  }
}
