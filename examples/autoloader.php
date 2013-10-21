<?php

/**
 * Simple autoloader for classes in src/ just for the examples
 * 
 * You should really write your own autoloader or you could include the required files manually.
 * 
 * @author Einar Huseby <einar.huseby@gmail.com>
 */

spl_autoload_register(function ($class) {
	include_once (dirname(__FILE__).'/../src/' . $class . '.php');
});