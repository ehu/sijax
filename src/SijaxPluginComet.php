<?php
/**
 * A helper class to simplify comet usage.
 * 
 * @author Einar Huseby <https://github.com/ehu>
 * @author Slavi Pantaleev <https://github.com/spantaleev>
 * @license BSD, see BSD-LICENSE.txt
 */
final class SijaxPluginComet {
	
	/**
	 * Helper function to simplify registering comet functions with Sijax.
	 *
	 * @param string $functionName        	
	 * @param callback $callback        	
	 * @param array $params        	
	 */
	public static function registerCallback($functionName, $callback, $params = array()) {
		if (! isset ( $params [Sijax::PARAM_RESPONSE_CLASS] )) {
			$params [Sijax::PARAM_RESPONSE_CLASS] = __CLASS__ . 'Response';
		}
		
		Sijax::registerCallback ( $functionName, $callback, $params );
	}
	
	public static function cometRequest($method, $url, $param = array()) {
		
		$out = "sjxComet.request('{$method}','{$url}', ";
		$out .= Sijax::generateJSParam($param);
		$out .= ");";
		
		return $out;
	}
	
	/**
	 * Make a jquery bind to a Sijax call
	 * Can bind to any event supported by jquery
	 *
	 * @param unknown_type $method        	
	 * @param unknown_type $url        	
	 * @param unknown_type $selector        	
	 * @param unknown_type $param        	
	 * @param unknown_type $bind        	
	 * @return string
	 */
	public static function cometFunction($method, $url, $selector, $param = array(), $script = NULL, $event = 'click') {
		
		$out = "\$('{$selector}').bind('{$event}', function() {";
		
		if (isset($script))
			$out .= $script;
		
		$out .= "sjxComet.request('{$method}','{$url}', ";
		
		$out .= Sijax::generateJSParam($param);
		
		$out .= ");";
		$out .= "return false;";
		$out .= "});\n";
		
		return $out;
	
	}
	
	/**
	 * Generating a new event handler for a sijaunction already in place
	 *
	 * @param string $method        	
	 * @param string $url        	
	 * @param string $selector        	
	 * @param array $param        	
	 */
	public static function cometFunctionReBind($method, $url, $selector, $param = array()) {
		
		$out = "$('{$selector}').unbind('click').click(function() {";
		
		$out .= "sjxComet.request('{$method}','{$url}', ";
		
		$out .= Sijax::generateJSParam($param);
		
		$out .= ");";
		
		$out .= "return false;";
		$out .= "});\n";
		
		return $out;
	
	}
}
