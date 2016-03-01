<?php
namespace Flubber;
/**
 *
 *  Request handler
 *
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  @Copyright : (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @Source : http://flubber.co
 *
 */
use Flubber\FLException as FLException;

$FLRequest = null;

class Request {

    public $method = 'get';

    public $headers = array();

    public $handler = null;

    public $params = array();

    public $body = '';

    public $data =  array(
        'get' => array(),
        'post' => array(),
        'put' => array(),
        'patch' => array(),
        'delete' => array());

    public $csrf_check = true;

    public $exception = null;

    function __construct() {
        $this->headers = $this->getallheaders();
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->body = file_get_contents('php://input');
        $this->prepare_data();

        // search for pattern in url
        $permastruct = $this->get_handler_data();
        if ($permastruct) {
            $this->handler = $permastruct['handler'];
            $this->params = $permastruct['params'];
        }
    }

    public function init() {
        global $FLRequest;
        $FLRequest = new Request();
    }

    function prepare_data() {
        // GET and FILES are simple.
        if (count($_GET) > 0) {
            $this->data['get'] = sanitizeArray($_GET);
        }
        if (count($_FILES) > 0) {
            $this->data['files'] = $_FILES;
        }
        // other type of requests may need special formatting
        if (in_array($this->method, ['post', 'put', 'delete','patch'])) {
            $type = explode(";", $this->headers['Content-Type'])[0];

            switch($type) {
                case 'application/json':
                    $this->data[$this->method] = json_decode($this->body, true);
                break;
                case 'multipart/form-data':
                    if ($this->method == 'post'){
                        $this->data['post'] = $_POST;
                    } else {
                        $boundary = explode("boundary=", $this->headers['Content-Type'])[1];
                        $boundary = explode(";", $boundary)[0];
                        $blocks = preg_split("/-+$boundary/", $this->body);
                        array_pop($blocks);
                        foreach ($blocks as $id => $block) {
                            if (empty($block)) continue;
                            if (strpos($block,'application/octet-stream')!== false){
                                preg_match('/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s', $block, $matches);
                                $this->data[$this->method][$matches[1]] = trim($matches[2]);
                            } else {
                                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                                $this->data[$this->method][$matches[1]] = trim($matches[2]);
                            }
                        }
                    }
                break;
                default:
                    parse_str($this->body, $this->data[$this->method]);
                break;
            }
        }
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

    /*
     *  Get request parameterfrom URL
     */
    private function get_request_params() {
        $uri   = (isset($_SERVER['REQUEST_URI']))  ? $_SERVER['REQUEST_URI']  : false;
        $query = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';

        // Find actual url path
        $url = str_replace($query, '', $uri);
        // trim the trailing slash and query
        $url = rtrim($url, "?");
        $url = rtrim($url, "/");
        $url = trim($url, "/");
        return $url;
    }

    /*
     *  Identify handler and extract params from url
     */
    private function get_handler_data($url = null) {
        global $FlubberHandlers;

        if ( $url == null ) {
            $url = $this->get_request_params();
        }

        foreach( $FlubberHandlers as $i => $cnf ) {
            $matches = false;
            preg_match($cnf[0], $url, $matches);
            if ( $matches ) {
                $params = array();
                if (isset($cnf[2])) {
                    $params = $cnf[2];
                }
                foreach($matches as $key => $value) {
                    if (!is_numeric($key)) {
                        $params[$key]  = $value;
                    }
                }

                return array(
                    'params' => $params,
                    'handler' => $cnf[1]
                );
            }
        }
        return false;
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
