<?php
// created via http://wpsettingsapi.jeroensormani.com/
require_once (dirname(__DIR__).'/config.php');

class NFP_Options_Page extends Singleton
{
  protected $settings = 'nfp_settings';

  protected $token_name = 'nfp_token';

  public $text_domain = 'natural-floor-products';

  public function add_menu()
  {
    add_options_page('Natural Floor Products', 'Natural Floor Products', 'manage_options', 'natural_floor_products', [$this, 'options_page']);
  }

  public function settings()
  {

    register_setting('options_page', $this->settings);

    add_settings_section(
      'options_page_section',
      __('', $this->text_domain),
      [$this, 'settings_cb'],
      'options_page'
    );

    add_settings_field(
      $this->token_name,
      __('Product Table Update Token', $this->text_domain),
      [$this, 'render'],
      'options_page',
      'options_page_section'
    );
  }

  public function render()
  {
    $token = self::get_token();
    $name = $this->settings . '[' . $this->token_name . ']';
    ?>
    <input type='text' name='<?= $name ?>' value='<?= $token ?>'>
    <?php
  }

  public function settings_cb()
  {
    echo __('', $this->text_domain);
  }

  public function options_page()
  {
    ?>
    <form action='options.php' method='post'>
      <h2>Natural Floor Products</h2>
      <?php
      settings_fields('options_page');
      do_settings_sections('options_page');
      ?>
      <h3>Read Only Google Sheet Settings - see /config.php</h3>
      <pre style="white-space: pre-line;background: #fff;overflow: scroll;padding: 20px;">
        <?php echo json_encode(NFP_Config::$googleSheetConfig, JSON_PRETTY_PRINT); ?>
      </pre>
      <?php submit_button(); ?>
    </form>
    <?php
  }

  public function get_token()
  {
    $options = get_option($this->settings);
    return $options[$this->token_name];
  }
}







