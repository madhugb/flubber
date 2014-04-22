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

  #
  # urls give the navigation ability to web app with simple url strucutre 
  #
  $urls = array
  (
    #
    # Each URL is matched with a regex which is in first index
    #
    # If the regex matches with the URL path then the second index is passed as GET argument
    # 
    # Regex are enclosed within `#`
    #

    array( "#^$#",  array("_action" => "home") )
    # the above line states 
    #   If the array[0](i.e #^$#) matches with the url path in this case if the url path is empty
    #     Then take the request _action from the second action 
    #     extend the values in second index with php GET global array
    # 
    # Some examples 
    # 1. array("#^dashboard$#", array("_action" => "dashboard"))
    #     Same example as above but the regex is different 
    #     this matches then the _action will be dashboard
    #     
    # 2. array("#^user/(?P<user_id>[0-9]+)$#", array("_action" => "user_view"))
    #      Here the value matching with `(?P<user_id>[0-9]+)` variable in the path 
    #      will be sent along with the rest of the values 
    #      so the final GET argument will be as below
    #
    #      if the url path is `http://example.com/user/10`
    #      then GET request will be transformed into
    #      array("_action"=>"user_view","user_id"=>"10")
  );
?>
