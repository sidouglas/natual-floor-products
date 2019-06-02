<?php
// start by cd . and php -S localhost:3000;
require __DIR__ . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config.php';


class NFP_Googlesheet
{
  private $fields_wanted = [];
  private $gsheet_config = [];
  private $fields_sent = [];

  /**
   * NFP_Googlesheet constructor.
   * @param array $fields_wanted - the array list of keys to populate left to right in the google spreadsheet
   * @param array $gsheet_config - the google doc configuration
   */
  public function __construct(array $fields_sent, array $gsheet_config)
  {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      exit;
    }
    $this->fields_wanted = $gsheet_config['fields_wanted'];
    $this->fields_sent = $fields_sent;
    $this->gsheet_config = $gsheet_config;
    $this->init();
  }

  public function init()
  {
    $request = $_SERVER['REQUEST_METHOD'];
    if ($request == 'POST') {
      $fields = array_merge($_POST, (array)json_decode(file_get_contents('php://input')));
      $this->record_message($fields);
    }
  }

  public function record_message($fields)
  {
    $sheetData = array_map(function ($key) use ($fields) {
      return $fields[$key] ? $fields[$key] : '';
    }, ($this->fields_wanted));

    try {
      $sheet = new GSheet($this->gsheet_config);
      $sheet->appendRow($sheetData);

      $this->return_json(
        [
          'status' => 201,
          'success' => true,
          'text' => 'Message sent successfully.',
          'id' => (int) $fields['id'],
        ],
        201
      );
    } catch (Error $error) {
      $this->return_json(
        [
          'text' => $error->getMessage(),
          'success' => false,
          'id' => (int) $fields['id'],
        ],
        406
      );
    }
  }

  public function return_json($data, $headerCode = 200)
  {
    $this->cors();
    http_response_code($headerCode);
    $value = is_array($data) ? json_encode($data) : $data;
    echo $value;
  }

  /**
   *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
   *  origin.
   *
   *  In a production environment, you probably want to be more restrictive, but this gives you
   *  the general idea of what is involved.  For the nitty-gritty low-down, read:
   *
   *  - https://developer.mozilla.org/en/HTTP_access_control
   *  - http://www.w3.org/TR/cors/
   *
   */
  function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
      // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
      // you want to allow, and if so:
      header("Access-Control-Allow-Origin: http://client.nfc:8080");
      header('Access-Control-Allow-Methods: GET, POST');
      header("Access-Control-Allow-Headers: X-Requested-With");
      header('Content-type: text/plain');
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

      exit(0);
    }
  }
}


new NFP_Googlesheet(['date', 'code', 'productname', 'message'], NFP_Config::$googleSheetConfig);
