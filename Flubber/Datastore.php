<?php
namespace Flubber;

if (DBTYPE) {
  // require FLIB.'Datastore/'.DBTYPE.'.php';
  require 'Datastore/'.DBTYPE.'.php';
  global $datastore;
}

class Datastore {

	function __construct() {

	}

	function init() {
		global $datastore;
		$driver = DBTYPE;
		$datasotre = new $driver();
	}
}

?>