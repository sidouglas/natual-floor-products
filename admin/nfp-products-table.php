<?php
if (!class_exists('WP_Links_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
  require_once(ABSPATH . 'wp-admin/includes/class-wp-links-list-table.php');
}

class NFP_Products_Table extends WP_Links_List_Table
{

  /**
   * The text domain of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $plugin_text_domain The text domain of this plugin.
   */
  protected $plugin_text_domain;

  protected $table_name;

  protected $product_cols = [];

  /*
 * Call the parent constructor to override the defaults $args
 *
 * @param string $plugin_text_domain	Text domain of the plugin.
 *
 * @since 1.0.0
 */
  public function __construct($plugin_text_domain, $table_name)
  {

    $this->plugin_text_domain = $plugin_text_domain;
    $this->table_name = $table_name;
    parent::__construct(array(
      'plural' => 'products',  // Plural value used for labels and the objects being listed.
      'singular' => 'product',    // Singular label for an object being listed, e.g. 'post'.
      'ajax' => false,    // If true, the parent class will call the _js_vars() method in the footer
    ));
  }

  /**
   * Prepares the list of items for displaying.
   *
   * Query, filter data, handle sorting, and pagination, and any other data-manipulation required prior to rendering
   *
   * @since   1.0.0
   */
  public function prepare_items()
  {
    // check if a search was performed.
    $product_search_key = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';

    $this->_column_headers = $this->get_column_info();

    // check and process any actions such as bulk actions.
    // $this->handle_table_actions();

    // fetch table data
    $table_data = $this->fetch_table_data();

    // filter the data in case of a search.
    if ($product_search_key) {
      $table_data = $this->filter_table_data($table_data, $product_search_key);
    }

    // required for pagination
    $products_per_page = $this->get_items_per_page('products_per_page');;

    $table_page = $this->get_pagenum();

    // provide the ordered data to the List Table.
    // we need to manually slice the data based on the current pagination.
    $this->items = array_slice($table_data, (($table_page - 1) * $products_per_page), $products_per_page);;

    // set the pagination arguments
    $total = count($table_data);
    $this->set_pagination_args([
      'total_items' => $total,
      'per_page' => $products_per_page,
      'total_pages' => ceil($total / $products_per_page),
    ]);
  }


  public function display_rows()
  {
    foreach ($this->items as $link) {
      ?>
      <tr id="link-<?= $link->id; ?>">
        <?php $this->single_row_columns($link); ?>
      </tr>
      <?php
    }
  }

  /**
   * Get a list of columns. The format is:
   * 'internal-name' => 'Title'
   *
   * @since 1.0.0
   *
   * @return array
   */
  public function get_columns()
  {
    global $wpdb;
    $columns = $wpdb->get_col("DESC {$this->table_name}", 0);
    $table_columns = [];
    foreach ($columns as $key) {
      $value = __(str_replace('_', ' ', ucwords($key, '_')), $this->plugin_text_domain);
      $table_columns[$key] = $value;
      $this->product_cols[$key] = $value;
    }
    return $table_columns;
  }

  /**
   * Get a list of sortable columns. The format is:
   * 'internal-name' => 'orderby'
   * or
   * 'internal-name' => array( 'orderby', true )
   *
   * The second format will make the initial sorting order be descending
   *
   * @since 1.1.0
   *
   * @return array
   */
  protected function get_sortable_columns()
  {
    /*
    * actual sorting still needs to be done by prepare_items.
    * specify which columns should have the sort icon.
    *
    * key => value
    * column name_in_list_table => columnname in the db
    */
    /*
     * actual sorting still needs to be done by prepare_items.
     * specify which columns should have the sort icon.
     *
     * key => value
     * column name_in_list_table => columnname in the db
     */
    $sortable_columns = [];
    foreach ($this->product_cols as $key => $value) {
      $sortable_columns[$key] = $key;
    }
    return $sortable_columns;
  }

  /**
   * Text displayed when no user data is available
   *
   * @since   1.0.0
   *
   * @return void
   */
  public function no_items()
  {
    _e('No Products Available.', $this->plugin_text_domain);
  }

  /*
   * Fetch table data from the WordPress database.
   *
   * @since 1.0.0
   *
   * @return	Array
   */

  public function fetch_table_data()
  {
    global $wpdb;

    $orderby = (isset($_GET['orderby'])) ? esc_sql($_GET['orderby']) : 'id';
    $order = (isset($_GET['order'])) ? esc_sql($_GET['order']) : 'ASC';

    $query = "SELECT * FROM {$this->table_name} ORDER BY $orderby $order";

    $query_results = $wpdb->get_results($query);

    // return result array to prepare_items.
    return $query_results;
  }

  /*
   * Filter the table data based on the user search key
   *
   * @since 1.0.0
   *
   * @param array $table_data
   * @param string $search_key
   * @returns array
   */
  public function filter_table_data($table_data, $search_key)
  {
    $filtered_table_data = array_values(array_filter($table_data, function ($row) use ($search_key) {
      foreach ($row as $row_val) {
        if (stripos($row_val, $search_key) !== false) {
          return true;
        }
      }
    }));

    return $filtered_table_data;

  }

  /**
   * Render a column when no column specific method exists.
   *
   * @param array $item
   * @param string $column_name
   *
   * @return mixed
   */
  public function column_default($item, $column_name)
  {
    if (property_exists($item, $column_name)) {
      return $item->$column_name;
    }
    return '';
  }

  public function column_name($link)
  {
    echo $link->name;
  }

  public function handle_row_actions($item, $column_name, $primary)
  {
    $item->link_id = $item->id;
    $item->link_name = $item->name;
    parent::handle_row_actions($item, $column_name, $primary);
  }

}
