<?php

/** 
 *  Core Functions
 *  
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */

/*
 *  Function to debug information in apache log
 */ 
function logger()
{
  $args = func_get_args();
  foreach( $args as $arg)
  {
    file_put_contents('php://stderr', print_r( $arg, TRUE) . "\n");
  }
}

?>
