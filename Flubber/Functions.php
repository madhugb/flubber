<?php

/*
 *  Functions
 *
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  Copyright (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *
 */
use Flubber\FLException as FLException;

// Function to debug information in apache log
function logger() {
    $args = func_get_args();
    foreach( $args as $arg) {
        file_put_contents('php://stderr', print_r( $arg, true) . "\n");
    }
}

// Redirect url
function redirect( $url ) {
    header('Location: '.$url);
    exit;
}

// redirect url
function relocate( $path ) {
    $http = 'http';
    if (HAS_SSL) {
        $http = 'https';
    }
    $str = sprintf('Location: %s://%s%s', $http, SITEURL, $path);
    header($str);
    exit;
}

// return a string in current locale
function _s( $name ) {
    global $FlubberLocale;
    return $FlubberLocale->get($name);
}

// Get instance of module
function gethandler($handler) {
    if (file_exists(HANDLER_PATH.'/'.$handler.'.php')) {
        include_once  HANDLER_PATH. '/'.$handler.'.php';
        return ( new $handler() );
    }
    throw new FLException('Unknown Handler.', array('status' => 404));
}

function secret_val($val) {
    return sprintf('%s:%s', strlen($val), $val);
}

function decode_secret_val($val) {
    $val_array = explode(":", $val);
    if (int($val_array) == count($val_array[1])){
        return $val_array[1];
    } else {
        throw new FLException('malformed signed value field');
    }
}

function sign_value($to_sign) {
    $hash = hash_hmac('sha1', $to_sign, TOKEN_SECRET);
    return $hash;
}

function create_signed_value($name, $value, $timestamp=null) {
    if (!$timestamp) {
        $timestamp = time();
    }

    $to_sign = array(
        '1',                     // Version of the signed value
        secret_val($timestamp),  // Timestamp
        secret_val($name),       // Name
        secret_val($value)       // Secret value
    );
    $sign = implode("|", $to_sign);
    $hash = sign_value($sign);
    $signed = $sign . "|" . $hash;
    return htmlspecialchars($signed);
}

function decode_signed_value($signed) {
    $values = explode("|", $signed);
    $version = decode_secret_val($values[0]);
    $timestamp = decode_secret_val($values[1]);
    $name = decode_secret_val($values[2]);
    $value = decode_secret_val($values[3]);
    $sign = $values[4];

    $new_sign = explode("|", create_signed_value($name, $value, $timestamp));
    if ($new_sign[4] == $sign) {
        if (time() - int($timestamp) <= TOKEN_EXPIRY ) {
            return array($name, $value);
        } else {
            throw new FLException('Signed value expired');
        }
    } else {
        throw new FLException('Signature is malformed');
    }
}

// Generate a new csrf token
function get_csrf_token($id=null) {
    if (!$id) {
        $id = mt_rand();
    }
    $token = dechex($id).'.'.dechex(time());
    $hash = sign_value($token);
    $signed = $token.'-'.$hash;
    return htmlspecialchars($signed);
}

// Validate a token
function validate_csrf($key) {
    $isok = FALSE;
    try {
        $parts = explode('-', $key);
        if (count($parts) === 2) {
            list($token, $hash) = $parts;
            if ($hash === sign_value($token)) {
                $time = hexdec(explode('.', $token)[1]);
                if (time() - $time <= TOKEN_EXPIRY ) {
                    $isok = true;
                }
            }
        }
    } catch(Exception $e) {
        $isok = false;
    } finally {
        return $isok;
    }
    return $isok;
}

// Check if the regular expression matches a string
function check_reg($rule, $str) {
    global $permalinks;
    preg_match($permalinks[$rule],$str, $match);
    if (isset($match[0])) {
        return $match[0];
    }
    return false;
}

/*
 * Sanitize a string by adding slash
 * @return string
 */
function sanitize($str) {
    return addslashes($str);
}

/*
 * Sanitize an array
 * @return string
 */
function sanitizeArray( $arr ) {
    if (is_array($arr)) {
        foreach($arr as $key => &$data) {
            if (is_array($data)) {
                $data = sanitizeArray($data);
            } else {
                try {
                    $json = json_decode( $data , true);
                    if (is_array($json)) {
                        $data = sanitizeArray( $json );
                        continue;
                    }
                } catch(Exception $e) {
                    $data = sanitize($data);
                }
            }
        }
    }
    return $arr;
}


?>
