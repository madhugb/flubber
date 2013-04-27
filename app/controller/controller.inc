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
include_once LIB_PATH. 'lib.inc';

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
        $this->_request['get'] = array_merge($request['get'], $permastruct['data']);
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
    include_once DOCROOT. '/../app/view/view.inc';
    $page_meta = $this->get_page_meta_info();
    $page_meta['is_mobile'] = $this->is_mobile;    
    return ( new view($view_data, $page_meta) );
  } 

  /*
   *  Take action according to appropriate case
   */ 
  function take_action()
  {
    switch( $this->_action )
    {    
      case 'home':
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
    $this->take_action();
  }
}

?>
