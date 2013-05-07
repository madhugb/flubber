<?php

/** 
 *  Lib Class for all core behaviors 
 *  This connects with datastore 
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */

// Include Datastore 
include_once LIB_PATH.'datastore/datastore.inc';

class lib extends datastore
{
  
  /*
   *
   */ 
  public $page_meta_info = array
  (
    "page_title"       => "Welcome Guest",
    "page_description" => "This is default page description"    
  );

  /*
   *  Initialize database connection set the database 
   */  
  function __construct($dbname = DBNAME)
  {
    parent::__construct();        
  }
  
  /*
   *  Function to Respond to clients request
   */
  function respond( $data = '')
  {
    
  }
   
  /*
   *  Get request parameterfrom URL        
   */ 
  function get_request_params()
  {
    $uri   = (isset($_SERVER['REQUEST_URI']))  ? $_SERVER['REQUEST_URI']  : false;
    $query = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';

    // Find actual url path
    $url   = str_replace($query, '', $uri);
        
    // trim the trailing slash and query
    $url   = rtrim($url,"?");    
    $url   = rtrim($url,"/");

    //
    // If DOCROOT is not server Document Root
    //
    // if ( DOCROOT != $_SERVER['DOCUMENT_ROOT'])
    //   $url   = trim($url, DOCROOT);      
      
    return $url;
  }

  /*
   *  setting meta information of a page
   */ 
  function set_page_info($info_title, $info_value)
  {
    $this->page_meta_info[$info_title] = $info_value;
    return true;
  }

  function get_page_info($info_title)
  {
    return $this->page_meta_info[$info_title];    
  }
  
  function get_page_meta_info()
  {
    return $this->page_meta_info;
  }
  
  /*
   *  Check if the regular expression matches a string
   */
  function check_reg($rule, $str)
  {
    global $permalinks;
    preg_match($permalinks[$rule],$str, $match);
    if (isset($match[0]))
      return $match[0];
    return false;    
  }

  /*
   *  Get action data from url 
   */ 
  function get_action_data($url = null)
  {
    global $permalinks, $link_args;
    $matches = false;
    if ( $url == null )
      $url = $this->get_request_params();
    //logger("url",$url);
    foreach( $permalinks as $id => $reg )
    {      
      preg_match($reg, $url, $matches);    
      logger($reg,$url,$matches);  
      if ( $matches )
      {        
        $vars = $link_args[$id];        
        foreach($matches as $key => $value)
        {          
          if (isset($vars[$key])) $vars[$key]  = $value;
        }        
        return array('id' => $id, 'data'=> $vars);
      }
    }    
    return false;
  }

  public function sanitize($str)
  {   
    return addslashes($str);
  }
  
  /*
   *   $data = array
   *   (
   *      'key' => 'value'
   *   )
   * 
   *   $data = array
   *   (
   *      'key' => array
   *      (
   *        'sub_key' => 'sub_value' 
   *      )
   *   )   
   */ 
  
  public function sanitizeArray($arr)
  {   
    foreach($arr as $key => &$data)
    {
      if(is_array($data))
      {
        $data = $this->sanitizeArray($data);
      }
      else
        $data = $this->sanitize($data);
    }
    return $arr;
  }
}
 
?>
