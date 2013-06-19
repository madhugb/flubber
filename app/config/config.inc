<?php

/**
 *
 *  Global Configuration
 * 
 *  Its a wrapper for including configurations
 *  and initializing Controller   
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */

  // Timezone
  $timezone   = "Asia/Kolkata";  
  if (function_exists('date_default_timezone_set'))
    date_default_timezone_set($timezone);

  /*
   *  name of the site where you host
   *  example:  flubbermvc.com
   *            labs.example.com
   *  NOTE: Do not put http:// or https:// here
   * 
   */   
  define('SITEURL',        'YOUR_SITE_URL');
      
  /*
   * Configuration global session variables
   * Once you start using session you need a name for your session
   *  by default PHP will name it as `PHPSESSID`
   */ 
  define('SESSIONNAME',       '_ses');


  // Defining path
  define('LOCALE_PATH',     CONFIG_PATH . '/locale/');
  define('LIB_PATH',        DOCROOT     . '../lib/');  
  define('MODEL_PATH',      DOCROOT     . '../app/model/');
  define('VIEW_PATH',       DOCROOT     . '../app/view/');
  define('CONTROLLER_PATH', DOCROOT     . '../app/controller/');
  
  // Load the url collections
  require_once CONFIG_PATH . 'urls.inc';
  
  // Load the core functions
  require_once LIB_PATH . 'functions.inc';
  
  // Load database configuration
  require_once CONFIG_PATH . 'settings.inc';

  /*
   *  Set a locale language
   *  Set a default language by default for alll operations   
   */ 
  require_once CONFIG_PATH . 'locale.inc';  
  set_locale("en");
  
?>
