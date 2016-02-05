<?php
namespace Flubber;
/**
 *
 *  Datastore Handler
 *  This creates a global datastore instance from config
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  @Copyright : (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @Source : http://flubber.co
 *
 */
global $datastore;

if (DBTYPE) {
  require 'Datastore/'.DBTYPE.'.php';
}

class Datastore {

    function __construct() {

    }

    function init() {
        global $datastore;
        $driver = DBTYPE;
        if (class_exists($driver)) {
            $datastore = new $driver();
        } else if (DBTYPE != ''){
            throw new FLException("Database drive type ".DBTYPE." is not valid.");
        }
    }
}

?>