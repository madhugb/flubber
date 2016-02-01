<?php

namespace Flubber;
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