<?php
namespace Flubber;

use Flubber\FLException as FLException;

$FLRequest = null;

class Request {

    public $method = 'get';

    public $headers = array();

    public $handler = null;

    public $params = array();

    public $body = '';

    public $data =  array(
        'post' => array(),
        'get' => array(),
        'put' => array(),
        'delete' => array());

    public $csrf_check = true;

    public $exception = null;

    function __construct() {
        $this->headers = $this->getallheaders();
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->body = file_get_contents('php://input');
        // GET and FILES are simple.
        if (count($_GET) > 0) {
            $this->data['get'] = sanitizeArray($_GET);
        }
        if (count($_FILES) > 0) {
            $this->data['files'] = $_FILES;
        }
        // other type of requests may need special formatting
        if (in_array($this->method, ['post', 'put', 'delete'])) {
            $type = explode(";", $this->headers['Content-Type'])[0];
            switch($type) {
                case 'application/json':
                    $this->data[$this->method] = json_decode($this->body, true);
                break;
                default:
                    parse_str($this->body, $this->data[$this->method]);
                break;
            }
        }
        // search for pattern in url
        $permastruct = get_handler_data();
        if ($permastruct) {
            $this->handler = $permastruct['handler'];
            $this->params = $permastruct['params'];
        }
    }

    public function init() {
        global $FLRequest;
        $FLRequest = new Request();
    }

    function getallheaders() {
        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace(
                                        '_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            }
        }
        if (!isset($headers['Content-Type'])){
            $headers['Content-Type'] = '';
        }
        return $headers;
    }

    function get_arguments() {
        return $this->data;
    }

    function get($key) {
        return $this->data['get'][$key];
    }

    function param($key) {
        return $this->params[$key];
    }

    function post($key) {
        return $this->data['post'][$key];
    }
}

?>