<?php

function FLAutoloader($class) {
	if (0 !== strpos($class, 'Flubber')) {
		return;
	}

	if (0 === strpos($class, 'Flubber\\')) {
		$class = explode('Flubber\\', $class)[1];
	}
	if (is_file($file = dirname(__FILE__).'/'.$class.'.php')){
		require $file;
		return;
	}
	echo "FLAutoloader could not find ".$file."<br>";
}

spl_autoload_register('FLAutoloader');

include_once dirname(__FILE__).'/vendors/Twig/Autoloader.php';
Twig_Autoloader::register();

?>