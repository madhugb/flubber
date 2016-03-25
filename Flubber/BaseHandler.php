<?php
namespace Flubber;
/**
 *
 *  Base Handler
 *  This contains the default handler
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  @Copyright : (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @Source : http://flubber.co
 *
 */
use Flubber\Response as Response,
    Flubber\FLException as FLException;

class BaseHandler {

    protected $request = null;

    protected $request_method = 'get';

    protected $domain = null;

    protected $cors = array(
        'allowCors' => true,
        'maxAge' => 86400, // 1 day
        'allowCredentials' => 'true',
        'allowedHeaders' => array("X-Requested-With"),
        'allowedOrigins' => array("http://flubber.co"),
        'allowedMethods' => array("GET", "POST", "PUT",
                                "PATCH", "DELETE", "OPTIONS")
    );

    protected $datastore = null;

    protected $response_status = null;

    protected $headers = array();

    protected $locale = "en";

    protected $session = null;

    function __construct($args=array()) {

        global $datastore, $FLRequest, $FLSession;
        $this->domain = ( HAS_SSL ? "https://" : "http://") + SITEURL;

        $this->datastore = $datastore;
        $this->session = $FLSession;

        if (is_a($FLRequest, 'Flubber\Request')) {
            $this->request = $FLRequest;
        } else {
            throw new FLException("Invalid Request in BaseHandler.",
                            array('status' => 500));
        }

        if (isset($args['cors'])) {
            $this->cors = $args['cors'];
        }

        if (isset($args['auth']) && count($args['auth']) > 0) {
            if (in_array($this->request->method, $args['auth']) &&
                !$this->is_authenticated()) {
                relocate(LOGIN_URI);
            }
        }

        if (isset($args['csrf_check'])) {
            $this->request->csrf_check = $args['csrf_check'];
        }

        if (isset($args['locale'])) {
            $this->locale = $args['locale'];
        }

        $this->init_csrf();
        $this->set_default_headers();

    }

    function is_authenticated() {
        if ($this->session->get('uid')) {
            return true;
        }
        return false;
    }

    private function init_csrf() {

        if ( $this->request->csrf_check &&
                in_array($this->request->method, ['post','put','patch']) ) {

            $token = "";
            if (isset($this->request->headers['X-Csrf-Token'])) {
                $token = $this->request->headers['X-Csrf-Token'];
            }

            if ($this->request->method == 'post' &&
                    isset($this->request->data['post']['_csrf'])) {
                $token = $this->request->data['post']['_csrf'];
            }

            if ($this->request->method == 'put' &&
                    isset($this->request->data['put']['_csrf'])) {
                $token = $this->request->data['put']['_csrf'];
            }

            if ($this->request->method == 'patch' &&
                    isset($this->request->data['patch']['_csrf'])) {
                $token = $this->request->data['patch']['_csrf'];
            }

            $valid = validate_csrf($token);
            if ( $valid ) {
                return true;
            }

        } else {
            return true;
        }

        throw new FLException('CSRF Token Invalid.',
                                array('status' => 403));

    }

    function set_default_headers() {

        if (isset($this->request->headers['Origin'])) {

            $origin = $this->request->headers['Origin'];

            if ($this->cors['allowCors'] &&
                    ($this->cors['allowedOrigins'] == "*" or
                        in_array($origin, $this->cors['allowedOrigins']))) {
                $this->headers["Access-Control-Allow-Origin"] = $origin;
                $this->headers["Access-Control-Allow-Credentials"] = $this->cors['allowCredentials'];
                $this->headers["Access-Control-Max-Age"] = $this->cors['maxAge'];

            }
        }
    }

    /*
     * Handle preflight requests as exception
     */
    function options() {
        if ($this->cors['allowCors'] && isset($this->request->headers['Origin'])) {

            $origin = $this->request->headers['Origin'];
            if (in_array("*",$this->cors['allowedOrigins']) or
                    in_array($origin, $this->cors['allowedOrigins'])) {

                http_response_code(200);
                header("Access-Control-Allow-Origin: ". $origin);

                $methods = implode(",", $this->cors["allowedMethods"]);
                header("Access-Control-Allow-Methods: ". $methods);

                $headers = implode(",", $this->cors['allowedHeaders']);
                header("Access-Control-Allow-Headers: ". $headers);
            }
        } else{
            http_response_code(405);
        }
        exit(0);
    }

    function get() {
        throw new FLException('Method Not Allowed.',
                    array('status' => 405));
    }

    function post() {
        throw new FLException('Method Not Allowed.',
                    array('status' => 405));
    }

    function put() {
        throw new FLException('Method Not Allowed.',
                    array('status' => 405));
    }

    function delete() {
        throw new FLException('Method Not Allowed.',
                    array('status' => 405));
    }

    function show_page(
            $template,
            $data = array()) {

        $response = new Response($template, $data,
                            array("locale" => $this->locale));
        if (isset($this->response_status)) {
            $response->set_status($this->response_status);
        }

        if (isset($this->headers)) {
            $response->set_headers($this->headers);
        }

        return $response->respond();
    }

    function send_json( $data = '') {
        $response = new Response('JSON', $data,
                            array("locale" => $this->locale));
        $response->set_header('Content-Type','application/json; charset=UTF-8;');

        if (isset($this->response_status)) {
            $response->set_status($this->response_status);
        }

        if (isset($this->headers)) {
            $response->set_headers($this->headers);
        }

        return $response->respond();
    }

    function send_response($data = "") {
        $response = new Response('TEXT', $data,
                            array("locale" => $this->locale));
        $response->set_header('Content-Type','text/html; charset=UTF-8;');

        if (isset($this->response_status)) {
            $response->set_status($this->response_status);
        }
        if (isset($this->headers)) {
            $response->set_headers($this->headers);
        }
        return $response->respond();
    }

    function set_status($status) {
        $this->response_status = $status;
    }

}
