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
		if (SQLDRIVER == 'MYSQL') {
			try {
				$this->connection = mysql_connect(DBHOST, DBUSER, DBPASS);
				mysql_select_db(DBNAME, $this->connection);
			} catch(\Exception $e) {
				logger('Connection failed:', $e , true);
				throw new FLException("Error in connecting to databse");
			}
		}

		if (SQLDRIVER == 'PDO') {
			try {
				$cn = sprintf('mysql:host=%s;dbname=%s;', DBHOST, DBNAME);
				$this->connection = new PDO($cn, DBUSER, DBPASS, array( PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING ));
			} catch(\PDOException $e) {
				logger('Connection failed:', $e , true);
				throw new FLException("Error in connecting to databse");
			}
		}
	}
}

?>
