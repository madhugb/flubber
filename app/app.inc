<?php

/**
 *
 *  Application Class
 * 
 *  Its a wrapper for including configurations
 *  and initializing Controller   
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */



define('CONFIG_PATH',       DOCROOT.'../app/config/');

// Load all the configurations 
require CONFIG_PATH . 'config.inc';

include_once CONTROLLER_PATH. 'controller.inc';

// Application extends controller
class app extends controller
{
  /*
   *  
   */ 
  private $request    = array();
  
  /*
   * initialize class variables
   */   
  public function __construct($getargs = array(), $postargs = array(), $fileargs = array())
  {
           
    $this->request['get']   = $getargs;   
    $this->request['post']  = $postargs; 
    $this->request['files'] = $fileargs;
    
  }         
  
  /*
   * Initialize Main application
   */   
  public function init()
  {
    $response = false;

    // Run the application passing the request to get appropriate response
    $response = $this->run( $this->request );

    /*
     *  Respond to client with data, if it is valid response
     */ 
    if (!is_bool($response))
    {    
      $this->respond($response);
    }    
    return true;
  }
}

?>
