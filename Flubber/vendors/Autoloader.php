<?php

class FLVendor_Autoloader
{
    public static function register($prepend = false)
    {
        spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
    }

    public static function autoload($class)
    {
        echo $class . "<br>";
        if (0 !== strpos($class, 'FLVendor')) {
            return;
        }

        if (is_file($file = dirname(__FILE__).'/../vendors/'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
            require $file;
        }
    }
}
