<?php

/**
 *
 *  A model template
 * 
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */

// Include lib
include_once LIB_PATH. 'lib.php';

/*
 *  make sure class name is same as model name
 *  generally there will be only one class in a model
 * 
 *  You can add two classes in one model file
 *  but there should be one class with the name of filename, which will be invoked by controller.
 *  You can trigger other class inside the main model, but not from the controller
 * 
 */ 
class sample_model extends lib
{
   
  /*
   * construct
   */  
  function __construct()
  {

  }

  /*
   *
   */ 
  function test_function( $request )
  {
     /*
      *   At this point of time     
      *    
      *   1. All the configuration are set
      *   2. Database in initialized
      *   3. Sessions will be active, 
      *  
      *   This function should just get the request and act upon it
      *   return the data in the specified format
      *
      *    If result is success then
      * 
      *    array
      *    (
      *       'result'=>'success',
      *       'data'=> your data in array
      *    )
      *
      *    If you need to show  error
      *
      *    array
      *    (
      *        'result'  => 'failure',
      *        'message' => 'Some error message'
      *    )
      */
      
     $some_array = array('welcome_message' => 'Welcome');
     $data = array('result'=>'success', 'data'=> $some_array); 
     return $data;  
  }
  
}

?>
