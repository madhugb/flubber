<?php
namespace Flubber;
/**
 *
 *  Base Handler
 *  This contains the default handler
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  Copyright (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *
 */
use Flubber\Response as Response;

class BaseHandler {

  protected $request = array();

  protected $request_method = 'get';

  protected $datastore = null;

  function __construct($request) {
    global $datastore;
    $this->datastore = $datastore;
    $this->request = $request;
    $this->init_csrf();
  }

  private function init_csrf() {
    if ($this->request->csrf_check &&
        in_array($this->request->method, ['post','put'])) {
      $token = "";
      if (isset($this->request->headers['X-Csrf-Token'])){
        $token = $this->request->headers['X-Csrf-Token'];
      }
      if (isset($this->request->data['post']['_csrf'])) {
        $token = $this->request->data['post']['_csrf'];
      }
      $valid = validate_csrf($token);
      if ($valid) return true;
    } else {
      return true;
    }

    throw new FLException('CSRF Token Invalid.',
                        array('status' => 403));
  }

  function options() {
    $this->set_status(405);
    $this->respond("Method Not Allowed");
  }

  function get(){
    $this->set_status(405);
    $this->respond("Method Not Allowed");
  }

  function post(){
    $this->set_status(405);
    $this->respond("Method Not Allowed");
  }

  function put(){
    $this->set_status(405);
    $this->respond("Method Not Allowed");
  }

  function delete(){
    $this->set_status(405);
    $this->respond("Method Not Allowed");
  }

  function show_page($template, $data=array(), $headers=array()){
    $response = new Response($template, $data);
    $response->set_headers($headers);
    return $response->respond();
  }

  function send_json( $data = ''){
    header('Content-Type: application/json; charset=UTF-8;');
    echo json_encode($data);
    return true;
  }

  function respond( $data = ''){
    echo $data;
    return true;
  }

  function set_status($code){
    http_response_code($code);
  }

}

?>