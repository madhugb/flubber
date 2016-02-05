<?php
namespace Flubber;
/*
 *  Core Handlers
 *
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  @Copyright : (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @Source : http://flubber.co
 *
 */

global $FlubberHandlers;

class Handlers {

	function register($handlers=array()){
		global $FlubberHandlers;
		$FlubberHandlers = $handlers;
	}

	function set($pattern, $handler){
		global $FlubberHandlers;
		array_push($FlubberHandlers, array($pattern,$handler));
	}
}

?>