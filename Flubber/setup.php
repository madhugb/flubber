#!/usr/bin/env php
<?php

$config_keys =  array(
	'FILE', 'URL', 'SSL', 'ADMIN', 'TIMEZONE',
	'SESSION_NAME', 'SESSION_EXPIRY', 'SESSION_IDLE', 'LOGIN_PATH',
	'TOKEN_SECRET', 'TOKEN_EXPIRY',
	'DB_TYPE', 'DB_DRIVER', 'DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME'
);


function logger($str) {
	if (is_array($str)) {
		print_r($str);
	} else {
		echo $str;
	}
	echo "\n";
}

function extract_config_from_cmdline($arguments) {
	global $config_keys;
	$config = array();
	$re = "/^--([a-z]+)\\=(.*)$/m";
	foreach ($arguments as $key => $value) {
		if (preg_match_all($re, $value, $matches)) {
			if (!isset($matches[1])) continue;
			if (!isset($matches[2])) continue;
			if (!in_array(strtoupper($matches[1][0]), $config_keys)) continue;
			$config[strtoupper($matches[1][0])] = $matches[2][0];
		}
	}
	return $config;
}

function get_sample_config($config) {
	global $config_keys;
	$template  = "<?php
/**
 *
 *  Global Configuration
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  Copyright (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 */

// Defining path
define('APPROOT', dirname(__FILE__).'/..');


/*
 *  host name (flubber.co ,labs.example.com)
 *  NOTE: Do not put http:// or https://
 */
define('SITEURL', '__URL__');

define('HAS_SSL', __SSL__);

// Admin email address
define('SITEADMIN', __ADMIN__);

// Timezone
define('TIMEZONE', '__TIMEZONE__');

// Session cookie name
define('SESSION_NAME', '__SESSION_NAME__');

define('SESSION_EXPIRY', __SESSION_EXPIRY__);

define('SESSION_IDLE', __SESSION_IDLE__);

define('LOGIN_URI', '__LOGIN_PATH__');

// CSRF token secret, make sure to put a long random string.
define('TOKEN_SECRET', '__TOKEN_SECRET__');

// CSRF tokens expiry time (in seconds)
define('TOKEN_EXPIRY', __TOKEN_EXPIRY__);

// Type of database for the app
define('DBTYPE', '__DB_TYPE__');

define('SQLDRIVER', '__DB_DRIVER__');

// DB configuration
define('DBHOST', '__DB_HOST__');

define('DBUSER', '__DB_USER__');

define('DBPASS', '__DB_PASS__');

define('DBNAME', '__DB_NAME__');

// set timezone for the application
if (function_exists('date_default_timezone_set')) {
  date_default_timezone_set(TIMEZONE);
}

// Load the url collections
require_once CONFIG_PATH . 'urls.php';

// Load custom functions
require_once CONFIG_PATH . 'functions.php';

?>";
	foreach($config_keys as $ind => $key) {
		$re = "/__".$key."__/m";
		$subst = "";
		if (array_key_exists($key, $config)) {
			$subst = $config[$key];
		}
		$template = preg_replace($re, $subst, $template);
	}
	return $template;
}

logger("Starting Installation");

$FlubberPath = __DIR__ . '/';

logger("Flubber Path : ".$FlubberPath);

$config = extract_config_from_cmdline($argv);
$config_content = get_sample_config($config);

logger($config_content);

?>