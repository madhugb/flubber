<?php

/**
 *
 *  Locale
 * 
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */
 
$locale     = "en";
$locale_str = array();

 // Initialize a language 
function init_locale()
{
  global $locale_str, $locale;
  if (!file_exists(LOCALE_PATH. $locale. '.inc'))
  {
    die("Strings not found");
  }
  include_once LOCALE_PATH. $locale. '.inc';  
  $locale_str = $strings;  
}

// set a language
function set_locale($name)
{
  global $locale;
  $locale = $name;
  init_locale();
}

// Get locale type
function get_locale()
{
  global $locale;
  return $locale;
}

// return a string in current locale format
function _s( $key )
{
  global $locale_str;  
  if (isset($locale_str[$key]))
    return $locale_str[$key];
  else
    return $locale_str['_nostr'];
}

?>
