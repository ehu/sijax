<?php
/**
 * This is an interface allowing us to take sijax methods out of the controllers
 * yet have them interact with the controller data natively.
 * 
 * @author Einar Huseby <https://github.com/ehu>
 * @author Slavi Pantaleev <https://github.com/spantaleev>
 * @license BSD, see BSD-LICENSE.txt
 */
abstract class SijaxHandler {
	
	private $_handlerObject = null;
	
	public function __construct($handlerObject = null) {
		
		if ($handlerObject === null) {
			throw new Exception ( 'A context object MUST be passed!' );
		}
		
		$this->_handlerObject = $handlerObject;
	}
	
	public function __get($var) {
		
		return $this->_handlerObject->$var;
		
	}	
	
	public function __call($func, $args) {
		
		return call_user_func_array ( array ($this->_handlerObject, $func), $args );
		
	}
	
	public function __set($var, $value) {
		
		$this->_handlerObject->$var = $value;
	}
}
