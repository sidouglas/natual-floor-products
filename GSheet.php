<?php
class GSheet {
  private $SHEET_ID = null;
  private $client = null;
  private $config = null;
  private $sheets = null;

  public $data = [];

  public function __construct( $googleCredentialsConfig )
  {
    $this->SHEET_ID = NFP_Config::$googleSheetConfig['sheet_id'];
    $this->client = new \Google_Client();
    $this->config = $googleCredentialsConfig;
    $this->sheets = $this->init();
  }

  private function init()
  {
    $this->client->setApplicationName($this->config['application_name'] ?? 'GSheet');
    $this->client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $this->client->setAccessType('offline');
    $this->client->setAuthConfig($this->config);
    return new \Google_Service_Sheets($this->client);
  }

  /**
   * appendRow
   * Adds a row to the bottom of the spreadsheet
   * @param array $fields
   */
  public function appendRow( array $fields )
  {
    $range = 'A1:B';

    $fields = array_map(function ($key) use ($fields) {
      return isset($fields[$key]) ? $fields[$key] : '';
    }, ($this->config['fields_wanted']));
    $valueRange= new Google_Service_Sheets_ValueRange();
    $valueRange->setValues(['values' => $fields]);
    return $this->sheets->spreadsheets_values->append($this->SHEET_ID, $range, $valueRange, ['valueInputOption' => 'RAW'], ['insertDataOption' => 'INSERT_ROWS']);
  }

  /**
   * getData
   * @param string $range -  The range of A2:H will get columns A through H and all rows starting from row 2
   * @return array|Google_Service_Sheets_ValueRange
   */
  public function getData( $range )
  {
    $this->data = $this->sheets->spreadsheets_values->get($this->SHEET_ID, $range, ['majorDimension' => 'ROWS']);
    return $this->data;
  }

  /**
   * updateRow
   * @param int:$range e.g 2 => A2
   * @param array:$data e.g ['2018', 'column 2 data', 'column 3 data' ]
   */
  public function updateRow( int $rowNumber, array $data )
  {
    $range = 'A' . $rowNumber;
    $updateBody = new \Google_Service_Sheets_ValueRange([
      'range' => $range,
      'majorDimension' => 'ROWS',
      'values' => $data,
    ]);

    $this->sheets->spreadsheets_values->update(
      $this->SHEET_ID,
      $range,
      $updateBody,
      ['valueInputOption' => 'USER_ENTERED']
    );
  }
}

