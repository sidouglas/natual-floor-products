<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/includes/nfp-cache.php';
require_once dirname(__DIR__) . '/includes/nfp-product-db.php';
require_once dirname(__DIR__) . '/GSheet.php';

class NFP_Rest_Controller extends WP_REST_Controller
{

  protected $name;
  protected $namespace;
  protected $table_name;
  protected $db;
  protected $validator;
  protected $token;

  public function __construct($name, $version, $table_name)
  {
    $this->name = $name;
    $this->namespace = $name . '/v' . $version;
    $this->table_name = $table_name;
    $this->db = new NFP_Product_DB($table_name, $version);
    $this->validator = new Validation();
    $this->token = NFP_Options_Page::get_instance()->get_token();
  }

  public function register_routes()
  {
    register_rest_route($this->namespace, '/products', [
      [
        'callback' => [$this, 'get_products'],
        'methods' => WP_REST_Server::READABLE,
      ],
    ]);

    register_rest_route($this->namespace, '/products/gsheet', [
      [
        'callback' => [$this, 'log_to_gsheet'],
        'methods' => WP_REST_Server::EDITABLE,
      ],
    ]);

    register_rest_route($this->namespace, '/products/post', [
      [
        'callback' => [$this, 'add_or_update'],
        'methods' => WP_REST_Server::EDITABLE,
        'args' => [
          'token' => [
            'required' => true,
            'validate_callback' => function ($token) {
              return $token === $this->token;
            }
          ]
        ]
      ],
    ]);
  }

  public function get_products()
  {
    $data = NFP_Cache::get_transient($this->namespace, function () {
      return $this->db->get_all();
    });
    return new WP_REST_Response($data, 200);
  }

  public function add_or_update(WP_REST_Request $request)
  {
    $params = $request->get_params();
    // delete the token as we know already that the post is valid
    unset($params['token']);
    if (array_key_exists($this->db->primary_key, $params)) {
      return $this->prepare_post($params, 'update');
    } else {
      return $this->prepare_post($params, 'insert');
    }
  }

  public function prepare_post(Array $params, $verb)
  {
    if ($is_invalid = $this->has_errors($params)) {
      return $this->post_error_response("rest_invalid_{$verb}", __("Invalid {$verb} data posted"), $is_invalid);
    } else {
      // try and do the verb action
      $arg1 = ($verb == 'update') ? $params['id'] : $params;
      $arg2 = ($verb == 'update') ? $params : '';

      if ($response = $this->db->$verb($arg1, $arg2)) {

        delete_transient($this->namespace);

        return new WP_REST_Response(
          [
            'code' => "success_{$verb}",
            'message' => ucfirst($verb) . ' was successful',
            'data' => [
              'status' => 200,
              'params' => [$response],
            ],
            'success' => true,
          ],
          200
        );
      } else {
        return $this->post_error_response('rest_invalid_query', __('query failed'), ['params' => $response]);
      }
    }
  }

  public function has_errors(Array $post_data)
  {
    $expected_columns = $this->db->get_show_columns();
    $errors = [];
    foreach ($post_data as $field_name => $field_value) {
      if (!array_key_exists($field_name, $expected_columns)) {
        $errors[$field_name] = 'field ' . $field_name . ' not expected in schema';
      } else {
        $rule = $this->db->determine_type($expected_columns[$field_name]->Type)['primitive'];
        $this->validator->name($field_name)->value($field_value)->pattern($rule);
      }
    }
    if ($this->validator->isSuccess() && count($errors) === 0) {
      return false;
    } else {
      foreach ($this->validator->errors as $key => $value) {
        $errors[$key] = $value;
      }
    }
    return ['params' => $errors];
  }

  public function post_error_response($code, $message, $error_fields, $status_code = 400)
  {
    $error_fields['status'] = $status_code;
    return new WP_Error(
      $code,
      $message,
      $error_fields
    );
  }

  public function log_to_gsheet(WP_REST_Request $request){
    $body = (array) json_decode($request->get_body());
    if($body){
      $GSheet = new GSheet(NFP_Config::$googleSheetConfig);
      $GSheet->appendRow($body);
      return new WP_REST_Response(
        [
          'code' => "success_insert",
          'message' => 'Message successfully sent',
          'id' => (int) $body['id'],
          'data' => [
            'status' => 200,
            'params' => [],
          ],
          'success' => true,
        ],
        200
      );
    } else {
      return $this->post_error_response('rest_invalid_message', __('message post failed'), ['params' => ['message data empty']]);
    }
  }
}
