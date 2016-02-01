<?php
namespace Flubber;
/**
*
*  Locale settings and functions
*
*
*  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
*  @License : The MIT License (MIT)
*  Copyright (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
*
*/

use Flubber\FLException as FLException;

$FlubberLocale = null;

class Locale {

    public $locale = "";

    public $languages = array();

    public $strings = array();

    public function autoload() {
        global $FlubberLocale;
        $FlubberLocale = new Locale();
        // TODO: Load all the locale files from `LOCALE_PATH`
        $locales = scandir(LOCALE_PATH);
        foreach($locales as $locale) {

            if ($locale == '.' || $locale == '..') continue;
            if (preg_match('/.ini$/', $locale)) {
                $locale_str =  parse_ini_file(LOCALE_PATH.$locale);
                $lang = explode(".ini",$locale);
                $FlubberLocale->register($lang[0],$locale_str);
            }
            if (preg_match('/.php$/', $locale)) {
                include_once LOCALE_PATH.$locale;
            }
        }
    }

    /*
     * Register strings for a new language
     */
    function register($lang, $strings) {
        $this->strings[$lang] = $strings;
    }

    /*
     * Set locale for the current session
     */
    function set_locale($lang) {
        if (isset($this->languages[$lang]) && isset($this->strings[$lang])) {
            $this->locale  = $lang;
        } else {
            $this->locale = "en";
        }
    }

    /*
     * Get locale string
     *
     * If the string is not present then it will print
     *   `{lang:name}` So that you can add that you can test it.
     */
    function get($name) {
        return isset($this->strings[$this->locale][$name]) ?
                $this->strings[$this->locale][$name] :
                "{". $this->locale . ":". $name ."}";
    }
}


?>
