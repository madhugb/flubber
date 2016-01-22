<?php
namespace Flubber;
/**
 *
 *  Session
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  Copyright (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *
 */

require 'vendors/HTTP_Session/Session.php';
use HTTP_Session;

$FLSession = null;

class Session {

    public $id = null;

    function __construct(){ }

    function init() {
        global $FLSession;
        $FLSession = new Session();
        $id = $FLSession->start();
        $FLSession->id = $id;
    }

    public function start($id=false) {
        if (empty($id)) {
            HTTP_Session::start(SESSION_NAME, null);
            $id = HTTP_Session::id();
        } else {
            HTTP_Session::start(SESSION_NAME, $id);
        }

        HTTP_Session::setExpire(time() + SESSION_EXPIRY);
        HTTP_Session::setIdle(time() + SESSION_IDLE);

        if (HTTP_Session::isIdle() || HTTP_Session::isExpired()) {
            return false;
        }
        return $id;
    }

    function destroy($id=false) {
        HTTP_Session::destroy($id);
    }

    /**
     * Is key defined in session?
     */
    static function has($key) {
        return HTTP_Session::is_set($key);
    }

    /**
     * Get value for the key.
     */
    static function get($key, $defvalue = '') {
        return HTTP_Session::get($key, $defvalue);
    }

    /**
     * Set value for the key.
     */
    static function set($key, $value) {
        HTTP_Session::set($key, $value);
    }
}

?>