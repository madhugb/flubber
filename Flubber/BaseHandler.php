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

    protected $datastore = null;

    protected $response_status = null;

    protected $headers = array();

    protected $session = null;

    function __construct($args=array()) {
        global $datastore, $FLRequest, $FLSession;
        $this->datastore = $datastore;
        $this->session = $FLSession;

        if (is_a($FLRequest, 'Flubber\Request')) {
            $this->request = $FLRequest;
        } else {
            throw new FLException("Invalid Request in BaseHandler.",
                            array('status' => 500));
        }

        if (isset($args['auth']) && count($args['auth']) > 0) {
            if (in_array($this->request->method,$args['auth']) &&
                !$this->is_authenticated()) {
                relocate(LOGIN_URI);
            }
        }

        if (isset($args['csrf_check'])) {
            $this->request->csrf_check = $args['csrf_check'];
        }
        $this->init_csrf();
    }

    function is_authenticated() {
        if ($this->session->get('uid')) {
            return true;
        }
        return false;
    }

    private function init_csrf() {

        if ( $this->request->csrf_check &&
                in_array($this->request->method, ['post','put']) ) {

            $token = "";
            if (isset($this->request->headers['X-Csrf-Token'])) {
                $token = $this->request->headers['X-Csrf-Token'];
            }

            if (isset($this->request->data['post']['_csrf'])) {
                $token = $this->request->data['post']['_csrf'];
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

    function options() {
        throw new FLException('Method Not Allowed.',
                    array('status' => 405));
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

        $response = new Response($template, $data);
        if (isset($this->response_status)) {
            $response->set_status($this->response_status);
        }

        if (isset($this->headers)) {
            $response->set_headers($this->headers);
        }

        return $response->respond();
    }

    function send_json( $data = '') {
        $response = new Response('JSON', $data);
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
        $response = new Response('TEXT', $data);
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

?>