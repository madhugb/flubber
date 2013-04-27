<?php

/**
 *
 * This is the entry point of application
 *
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */

  /*
   * Define Document root 
   */ 
  define('DOCROOT', dirname(__FILE__) .'/');

  /*
   * Include App
   */  
  include_once DOCROOT. '../app/app.inc';

  /*
   *  Create new application instance and pass all the agruments
   *  such as POST, GET, FILES
   *  and initialize app through `init()`
   */ 
  $app = new app($_GET, $_POST, $_FILES);
  $app->init();

  
?>
