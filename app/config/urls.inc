<?php

/** 
 *  URL regular expression  
 *  This defines which action to be taken if matches with a URL pattern
 *  
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */

  // Permalinks array containing Expressions to match a perticular URL
  $parmalinks = array
  (
    // If url is `/` then path is home
    "home"  => "/^$/"
  );

  // Link Action array containing Action to match a perticular URL
  $link_args = array
  (
    "home"    => array("_action"=>"home")
  );
  
?>
