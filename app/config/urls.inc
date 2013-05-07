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

  // Permalinks array containing Expressions to match a perticular URL. 
  // Basically it allows you to split a particular URL into a proper get request, to be fed to _action 
  // with many variables 
  
  $permalinks = array
  (
    // If url is `/` then path is home
    "home"  => "/^$/"
    
     // Here the actual article-id will be store in the variable article-id and will be used in 
     // the action show_article. In short, it (with help of link_args) creates a complete GET request from a URL
    //"blog" => "?P<article_id>[0-9]+"
    
  );

  // This array maps the static url's requested to different actions. It is needed only for GET requests.
  $link_args = array
  (
    "home"    => array("_action"=>"home")
    // Here the article_id is taken from permanlinks above. The key 'article_id' should be same as the <article_id>
    // of the Regular Expression above.
    //"blog"    => array("_action"=>"blog", "article_id"=>"")
  );
  
?>
