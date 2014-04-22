<?php

/**
 *
 *  Controller
 * 
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */

// Include lib
include_once LIB_PATH. 'lib.php';

// main controller class
class controller extends lib
{
  
  protected $_request = array();

  protected $_model;  

  protected $_action;

  protected $_view;

  protected $_status   = array();    

  protected $is_mobile = false;

  /*
   *  construct
   */ 
  function __construct()
  {
    parent::__construct();
  }

  /*
   *  Get appropriate request data for a url / request   
   */ 
  function cleanup_request( $request )
  {
    // check if the request is POST    
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {

      $this->_request = $request;
      foreach ($this->_request['post'] as $str => $value)    
        $this->_request['post'][$str] = $this->sanitize($value);
      $this->_action  = $this->_request['post']['_action'];
      
    }
    else if ($_SERVER['REQUEST_METHOD'] == 'GET')
    {

      // Validate for prema links
      if (!isset($this->_request['get']['_action']))
      {
        // search for pattern in url
        $permastruct = $this->get_action_data();
        // Append the related rgsfrom permastruct to GET array
        if ($permastruct)        
          $this->_request['get'] = array_merge($request['get'], $permastruct['data']);
        else 
          $this->_request['get'] = array("_action" => "404", "message" => "Invalid url");
      }
      // Update the action
      $this->_action  = $this->_request['get']['_action'];    
    }    
  }

  /*
   *  get a model and return a new instance of model 
   */
  protected function getmodel($modelname)
  {
    include_once  DOCROOT. '/../app/model/'.$modelname.'.php';
    return ( new $modelname($this->_request) );        
  }
  
  /*
   * render a view according to the request
   */ 
  protected function getview($view_data = array())
  {
    include_once DOCROOT. '/../app/view/view.php';
    $page_meta = $this->get_page_meta_info();
    $page_meta['is_mobile'] = $this->is_mobile;    
    return ( new view($view_data, $page_meta) );
  } 

  /*
   *  "take_action" is the key to the entire framework. Here, based on the _action requested by the 
   *  client, you will do the following:
   * 
   *  1. Create the model from where data will come. $mod = $this->getmodel('<modelname>')
   *  2. Get the data: $data = $mod->mydatafunction(request)
   *  3. Get a view based upon the data. $view = $this->getview($data). This will initialize the display content .
   *  4. Push the HTML to the browser. return $view->show_page(<templatename>)
   *  
   *  Please note that you must create a <modelname>.php file with class = <modelname>
   *  You should also have a template called <templatename>.php
   *  
   *  `mydatafunction` must return an array of format: array('result'=>'success', <data>=><dataarray>)
   *                                                                 ------or------
   *                                                   array('result'=>'failure', 'message'=> <your error message>)
   *  The variable name <data> can be accessed from the template <templatename>.
   */ 
  
  function take_action()
  { 
    switch( $this->_action )
    { 
      case 'home':
  	    $mod = $this->getmodel('sample_model');
        $mod_date = $mod->test_function($this->_request);
        $view = $this->getview($mod_date);
        return $view->show_page('home');
      break;	  
      case '404':
        $view = $this->getview();
        return $view->show_page('404');
      break;
      default:      
        $view =  $this->getview();
        return $view->show_page('home');                    
      break;
    }
  }

  /*
   * Cleanup request and take action
   */ 
  function run( $request )
  {    
    $this->cleanup_request( $request );
    // Take appropriate action based on the request, and return the output to app
    return $this->take_action();
  }
}

?>
