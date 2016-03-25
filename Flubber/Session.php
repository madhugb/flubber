<?php
namespace Flubber;
/**
 *
 *  Session Handler
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  @Copyright : (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @Source : http://flubber.co
 *
 */

$FLSession;

class Session {

    const SESSION_STARTED = TRUE;
    const SESSION_NOT_STARTED = FALSE;

    private $sessionState = self::SESSION_NOT_STARTED;

    private $id = null;

    private function __construct() {}

    public function init(){
        global $FLSession;
        if ( !isset($FLSession)) {
            $FLSession = new self;
        }
        $FLSession->start();
    }

    public function start() {
        session_name(SESSION_NAME);
        ini_set('session.gc_maxlifetime', SESSION_EXPIRY);
        session_set_cookie_params(SESSION_EXPIRY, // expire in 24 hours
                    '/',                          // path
                    '.'.SITEURL,                  // domain
                    false,                        // "secure only"
                    true);                        // only over http

        if ( $this->sessionState == self::SESSION_NOT_STARTED ) {
            $this->sessionState = session_start(array(
                                    'cookie_lifetime' => SESSION_EXPIRY));
        }


        if (SESSION_AUTO_RENEW
                && isset($_SESSION['timeout_idle'])
                && $_SESSION['timeout_idle'] < time()) {
            $this->updateExpiry( time() + SESSION_EXPIRY);
        }

        $_SESSION['timeout_idle'] = $newidle = time() + SESSION_IDLE;

        return $this->sessionState;
    }

    private function updateExpiry($lifetime) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(),
            session_id(),
            $lifetime,
            $cookie['path'],
            $cookie['domain'],
            $cookie['secure'],
            $cookie['httponly']);
    }

    public function set( $name , $value ) {
        $_SESSION[$name] = $value;
    }

    public function get( $name ) {
        if ( isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
    }


    public function is_set( $name ) {
        return isset($_SESSION[$name]);
    }


    public function un_set( $name ) {
        unset( $_SESSION[$name] );
    }

    public function destroy() {
        if ( $this->sessionState == self::SESSION_STARTED ) {
            $this->sessionState = !session_destroy();
            unset( $_SESSION );

            return !$this->sessionState;
        }
        return FALSE;
    }
}


