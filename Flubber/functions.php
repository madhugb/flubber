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

// Print a string in current locale
function _s( $key ) {
  global $locale_str;
  if (isset($locale_str[$key]))
    return $locale_str[$key];
  else
    return $locale_str['_nostr'];
}

// Get instance of module
function gethandler($request) {
  if (file_exists(HANDLER_PATH.'/'.$request->handler.'.php')) {
    include_once  HANDLER_PATH. '/'.$request->handler.'.php';
    $handler = $request->handler;
    return ( new $handler($request) );
  }
  throw new FLException('Unknown Handler.', array('status' => 404));
}

// Generate a new csrf token
function get_csrf_token($id=null) {
  if (!$id) {
    $id = mt_rand();
  }
  $token = dechex($id).'.'.dechex(time());
  $hash = hash_hmac('sha1', $token, CSRF_SECRET);
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
      if ($hash === hash_hmac('sha1', $token, CSRF_SECRET)) {
        $time = hexdec(explode('.', $token)[1]);
        if (time() - $time <= CSRF_EXPIRY ) {
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
        }
        catch(Exception $e) {}
        $data = sanitize($data);
      }
    }
  }
  return $arr;
}

/*
 *  Get request parameterfrom URL
 */
function get_request_params() {
  $uri   = (isset($_SERVER['REQUEST_URI']))  ? $_SERVER['REQUEST_URI']  : false;
  $query = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';

  // Find actual url path
  $url = str_replace($query, '', $uri);
  // trim the trailing slash and query
  $url = rtrim($url, "?");
  $url = rtrim($url, "/");
  $url = trim($url, "/");
  return $url;
}

/*
 *  Identify handler and extract params from url
 */
function get_handler_data($url = null) {
  global $urls;
  if ( $url == null ) {
    $url = get_request_params();
  }
  foreach( $urls as $i => $cnf ) {
    $matches = false;
    preg_match($cnf[0], $url, $matches);
    if ( $matches ) {
      $params = array();
      if (isset($cnf[2])) {
        $params = $cnf[2];
      }
      foreach($matches as $key => $value) {
        if (!is_numeric($key)) {
          $params[$key]  = $value;
        }
      }
      return array(
        'params' => $params,
        'handler' => $cnf[1]);
    }
  }
  return false;
}

?>
