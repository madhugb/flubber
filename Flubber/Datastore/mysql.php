<?php

/*
*  Mysql Database
*
*  Its a wrapper for MySQL
*
*  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
*  @License : The MIT License (MIT)
*  Copyright (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
*
*/
class mysql {

	public $connection = null;

	protected $database  = null;

	function __construct() {
		try {
			$this->connection = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		} catch(\Exception $e) {
			logger('Connection failed:', $e , true);
			throw new FLException("Error in connecting to databse");
		}
	}

	function query($query) {
		try {
			return $this->connection->query($query);
		} catch(\Exception $e) {
			$message = sprintf("Error executing query: %s [ERROR: %s]", $query, $e->getmessage());
			throw new FLException($message);
		}
		return false;
	}
}

?>
