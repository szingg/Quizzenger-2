<?php

spl_autoload_register(function($class) {
	$namespace = 'quizzenger';
	if(strncmp($class, $namespace, strlen($namespace)) === 0) {
		require_once str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	}
});

?>
