<?php

/**
 * This is the main Sijax class that takes care of
 * registering callable functions, processing incoming data and dispatching calls.
 * 
 * @author Einar Huseby <https://github.com/ehu>
 * @author Slavi Pantaleev <https://github.com/spantaleev>
 * @license BSD, see BSD-LICENSE.txt
 */
final class Sijax {
	
	const PARAM_REQUEST = 'sijax_rq';
	const PARAM_ARGS = 'sijax_args';
	const EVENT_BEFORE_PROCESSING = 'beforeProcessing';
	const EVENT_AFTER_PROCESSING = 'afterProcessing';
	const EVENT_INVALID_REQUEST = 'invalidRequest';
	const PARAM_CALLBACK = 'callback';
	const PARAM_RESPONSE_CLASS = 'responseClass';
	private static $_requestUri = null;
	private static $_jsonUri = null;
	private static $_registeredMethods = array ();
	private static $_events = array ();
	
	/**
	 * Would contain the request data (usually $_POST)
	 * 
	 * @var unknown
	 */
	private static $_data = array ();
	
	/**
	 * Would store a cached version of the arguments to pass to the requested function
	 *
	 * @var unknown
	 */
	private static $_requestArgs = null;
	
	/**
	 * Sets the incoming data array.
	 * This is usually $_POST or whatever the framework uses.
	 *
	 * @param array $data        	
	 */
	public static function setData(array $data) {
		self::$_data = $data;
	}
	
	/**
	 * Returns the incoming data array.
	 *
	 * @return array $_data
	 */
	public static function getData() {
		return self::$_data;
	}
	
	/**
	 * Tells Sijax the URI to submit ajax requests to.
	 * If you don't pass a request URI, the current URI would be
	 * detected and set automatically.
	 *
	 * @param string $uri        	
	 */
	public static function setRequestUri($uri) {
		self::$_requestUri = $uri;
	}
	
	/**
	 * Sets the URI to an external JSON library (json2.js),
	 * for browsers that do not support native JSON (such as IE<=7).
	 *
	 * The specified script will only be loaded if such a browser is detected.
	 * If this is not specified, Sijax will not work at all in IE<=7.
	 *
	 * @param
	 *        	$uri
	 */
	public static function setJsonUri($uri) {
		self::$_jsonUri = $uri;
	}
	
	/**
	 * Returns the name of the requested function
	 * or NULL if no function is requested.
	 *
	 * @return NULL string
	 */
	public static function getRequestFunction() {
		if (! isset ( self::$_data [self::PARAM_REQUEST] )) {
			return null;
		}
		
		return ( string ) self::$_data [self::PARAM_REQUEST];
	}
	
	/**
	 * Returns an array of arguments to pass to the requested function.
	 *
	 * @todo implement escaping
	 * @return array arguments
	 */
	public static function getRequestArgs() {
		if (self::$_requestArgs === null) {
			if (isset ( self::$_data [self::PARAM_ARGS] )) {
				self::$_requestArgs = ( array ) json_decode ( self::$_data [self::PARAM_ARGS], true );
			} else {
				self::$_requestArgs = array ();
			}
		}
		
		return self::$_requestArgs;
	}
	
	/**
	 * Sets the request arguments, possibly overriding the autodetected arguments array.
	 * This is useful for plugins that would like to "rewrite" the arguments array.
	 *
	 * @param array $args        	
	 */
	public static function setRequestArgs(array $args) {
		self::$_requestArgs = $args;
	}
	
	/**
	 * Tells whether the current request is a Sijax request.
	 *
	 * @todo can also use fRequest::isAjax (flourish-lib)
	 *      
	 * @return boolean is Sijax request
	 */
	public static function isSijaxRequest() {
		if (! isset ( self::$_data [self::PARAM_REQUEST] )) {
			return false;
		}
		
		if (! isset ( self::$_data [self::PARAM_ARGS] )) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Registers all methods of the specified object instance (or class).
	 * These methods will be callable from the browser.
	 *
	 * The optional $params array allows the response class
	 * to be changed from the default one (SijaxResponse).
	 *
	 * @param object/class $object        	
	 * @param array $params        	
	 * @throws Exception
	 */
	public static function registerObject($object, $params = array()) {
		if ($object === null) {
			throw new Exception ( 'Object is NULL!' );
		}
		
		foreach ( get_class_methods ( $object ) as $methodName ) {
			
			if (isset ( self::$_registeredMethods [$methodName] )) {
				// Don't register methods on top of another methods..
				continue;
			}
			
			$params [self::PARAM_CALLBACK] = array ($object,$methodName 
			);
			
			self::$_registeredMethods [$methodName] = $params;
		}
	}
	
	/**
	 * Registers the specified callback function (closure, class method, function name),
	 * to be callable from the browser.
	 *
	 * The optional $params array allows the response class
	 * to be changed from the default one (SijaxResponse).
	 *
	 * @param string $functionName        	
	 * @param string $callback        	
	 * @param array $params        	
	 */
	public static function registerCallback($functionName, $callback, $params = array()) {
		$params [self::PARAM_CALLBACK] = $callback;
		
		self::$_registeredMethods [$functionName] = $params;
	}
	
	/**
	 * Executes the specified callback (closure, class method, function name),
	 * passing the specified arguments to it.
	 *
	 * The optional $params array allows the response class
	 * to be changed from the default one (SijaxResponse).
	 *
	 * @param callback $callback        	
	 * @param array $args        	
	 * @param array $params        	
	 */
	public static function executeCallback($callback = null, $args = array(), $params = array()) {
		if (isset ( $params [self::PARAM_RESPONSE_CLASS] )) {
			$responseClass = $params [self::PARAM_RESPONSE_CLASS];
		} else {
			$responseClass = __CLASS__ . 'Response';
		}
		
		$objResponse = new $responseClass ( $args );
		
		self::fireEvent ( $objResponse, self::EVENT_BEFORE_PROCESSING, array () );
		
		self::_callFunction ( $callback, $objResponse );
		
		self::fireEvent ( $objResponse, self::EVENT_AFTER_PROCESSING, array () );
		
		die ( $objResponse->getJson () );
	}
	
	/**
	 * Inspects the data array (as specified by setData()) to determine
	 * if the current server request is to be handled by sijax.
	 *
	 * If this is a normal page request, this simply returns without doing anything.
	 *
	 * If this is a VALID sijax request (for a registered function), it gets called.
	 *
	 * If this is an INVALID sijax request, the EVENT_INVALID_REQUEST event gets fired.
	 * In case no custom event handler is specified, the default one is triggered (_invalidRequestCallback).
	 */
	public static function processRequest() {
		if (! self::isSijaxRequest ()) {
			return;
		}
		
		$requestFunction = self::getRequestFunction ();
		$callback = null;
		$args = array ();
		$params = array ();
		
		if (isset ( self::$_registeredMethods [$requestFunction] )) {
			$params = self::$_registeredMethods [$requestFunction];
			$callback = $params [self::PARAM_CALLBACK];
			$args = self::getRequestArgs ();
		} else {
			if (self::hasEvent ( self::EVENT_INVALID_REQUEST )) {
				$callback = self::$_events [self::EVENT_INVALID_REQUEST];
			} else {
				$callback = array (__CLASS__,'invalidRequestCallback' 
				);
			}
			
			$args = array ($requestFunction 
			);
		}
		
		self::executeCallback ( $callback, $args, $params );
	}
	private static function _invalidRequestCallback(SijaxResponse $objResponse, $functionName) {
		$objResponse->alert ( 'The action you performed is currently unavailable! (Sijax error)' );
	}
	
	/**
	 * Prepares the callback function arguments and calls it.
	 *
	 * The optional $requestArgs array allows the detected request args
	 * which may have been altered by the response object to be overriden.
	 * It's not used for normal requests.
	 * Events and manually executed callbacks however override the request args.
	 *
	 * @param callback $callback        	
	 * @param SijaxResponse $objResponse        	
	 * @param array $requestArgs        	
	 */
	private static function _callFunction($callback, SijaxResponse $objResponse, $requestArgs = null) {
		if ($requestArgs === null) {
			/**
			 * Normal functions are called like this.
			 * The object response class was given the args before,
			 * but may have changed them
			 */
			$requestArgs = $objResponse->getRequestArgs ();
		}
		
		$args = array_merge ( array ($objResponse 
		), $requestArgs );
		
		call_user_func_array ( $callback, $args );
	}
	
	/**
	 * Sets a callback function to be called when the specified event occurs.
	 * Only one callback can be executed per event.
	 *
	 * The provided EVENT_* constants should be used for handling system events.
	 * Additionally, you can use any string to define your own events and callbacks.
	 *
	 * If more are needed, they may be chained manually.
	 * Certain functionality to allow this (getEvent()) is missing though.
	 *
	 * @param string $eventName        	
	 * @param callback $callback        	
	 */
	public static function registerEvent($eventName, $callback) {
		self::$_events [$eventName] = $callback;
	}
	
	/**
	 * Tells whether there's a callback function to be called
	 * when the specified event occurs.
	 *
	 * @param string $eventName        	
	 */
	public static function hasEvent($eventName) {
		return isset ( self::$_events [$eventName] );
	}
	
	/**
	 * Fires the specified event.
	 *
	 * @param SijaxResponse $objResponse        	
	 * @param string $eventName        	
	 * @param array $args        	
	 */
	public static function fireEvent(SijaxResponse $objResponse, $eventName, array $args) {
		if (! self::hasEvent ( $eventName )) {
			return;
		}
		
		return self::_callFunction ( self::$_events [$eventName], $objResponse, $args );
	}
	
	/**
	 * Tries to detect the current request URI.
	 * Sijax requests would be sent to the same URI.
	 * If you want to avoid autodetection, or override this, use setRequestUri().
	 *
	 * @return void
	 */
	private static function _detectRequestUri() {
		$requestUri = strip_tags ( isset ( $_SERVER ['REQUEST_URI'] ) ? $_SERVER ['REQUEST_URI'] : '' );
		self::setRequestUri ( $requestUri );
	}
	
	/**
	 * Returns the javascript needed to prepare sijax for running on a page.
	 *
	 * Typical output is:
	 * Sijax.setRequestUri("\/index.php?page=main");
	 * Sijax.setJsonUri("\/var\/www\/localhost\/htdocs\/svn\/siteengine\/testbin\/Sijax\/js\/json2.js");
	 *
	 * @deprecated this is handeled by asset manager or direct inclusion of files
	 *            
	 * @return mixed $script
	 */
	public static function getJs() {
		if (self::$_requestUri === null) {
			self::_detectRequestUri ();
		}
		
		$script = "";
		
		// @todo remove this, now using multiple uri
		$script .= "Sijax.setRequestUri(" . json_encode ( self::$_requestUri ) . ");";
		$script .= "Sijax.setJsonUri(" . json_encode ( self::$_jsonUri ) . ");";
		
		return $script;
	}
	
	/**
	 * Parses sijax_args to javascript notation
	 *
	 * @param array mixed $args ('key' => 'value' etc in sijax only the placement of the value is required!)
	 */
	public static function generateJSParam($args) {
	
		$sijax_args = '["';
	
		if (is_array ( $args ) && count ( $args ) > 0) {
				
			$arg_arr = array();
				
			foreach ($args AS $key => $val) {
	
				if(is_numeric($val)) $arg_arr[] = $val;
				elseif(is_bool($val)) {
					($val) ? $arg_arr[] = 'true' : $arg_arr[] = 'false';
				}
				else $arg_arr[] = $val;
			}
				
			$sijax_args .= implode('","', $arg_arr);
		}
		elseif (is_array ( $args ) && count ( $args ) == 0) {
			$sijax_args .= '';
		}
		else
			$sijax_args .= $args;
	
		$sijax_args .= '"]';
		return $sijax_args;
	}
	
	/**
	 * Helper: Remove the output buffer
	 *
	 * Loops all levels of buffer with ob_end_clean
	 */
	public static function cleanBuffer() {
		// Remove output buffer!
		while ( ob_get_level () > 0 ) {
			
			ob_end_clean ();
		}
	}
	
	/**
	 * Helper: Exit wrapper
	 */
	public static function stopProcessing() {
		exit ();
	}
	
	/**
	 * Helper:
	 * Sijax js request helper
	 * Generates a valid sijax javascript request
	 *
	 * @param varchar $method        	
	 * @param varchar $url        	
	 * @param array $param        	
	 * @return string $string sijax javascript request call
	 */
	static public function sijaxRequest($method, $url, $args = array()) {
		$string = "Sijax.request('{$method}','{$url}' ";
		
		$string .= "," . self::generateJSParam ( $args );
		
		$string .= ");";
		
		return $string;
	}
	
	/**
	 * Helper method to generate valid post data for various calls
	 * Eg for use in data option in a $.ajax call to a Sijax backend eg a jquery plugin
	 *
	 * @param string $method        	
	 * @param array $args        	
	 */
	static public function sijaxPopulatePostData($sijax_rq, $args) {
		
		// sijax_rq=getJSON&sijax_args=[\"%QUERY\"]
		return self::PARAM_REQUEST . "={$sijax_rq}&" . self::PARAM_ARGS . "=" . self::generateJSParam ( $args );
	}
	
	/**
	 * Make a jquery bind to a Sijax request
	 * Can bind to any event supported by jquery
	 *
	 * @param string $method        	
	 * @param string $url        	
	 * @param string $selector        	
	 * @param array $param        	
	 * @param mixed $script        	
	 * @param string $event
	 *        	click, hover etc
	 * @return string $string javascript sijax function call
	 */
	public static function sijaxFunction($method, $url, $selector, $param = array(), $script = NULL, $event = 'click', $dom = 'document') {
		$string = "\$({$dom}).on('{$event}','{$selector}', function() {";
		
		if (isset ( $script ))
			$string .= $script;
		
		$string .= "Sijax.request('{$method}','{$url}'";
		$string .= "," . self::generateJSParam ( $param );
		$string .= ");";
		$string .= "return false;";
		$string .= "});\n";
		
		return $string;
	}
	
	/**
	 * Generating a new event handler for a sijaxFunction already in place
	 *
	 * @param string $method
	 * @param string $url
	 * @param string $selector
	 * @param array $args
	 * @return string $string javascript
	 */
	public static function sijaxFunctionReBind($method, $url, $selector, $args = array()) {
		$string = "$('{$selector}').unbind('click').click(function() {";
	
		$string .= "Sijax.request('{$method}','{$url}'";
	
		$string .= "," . self::generateJSParam ( $args );
	
		$string .= ");";
	
		$string .= "return false;";
		$string .= "});\n";
	
		return $string;
	}
	
	/**
	 * Get form values wrapper
	 *
	 * @param varchar $selector        	
	 * @return string sijax get form values javascript function call
	 */
	public static function sijaxGetFormValues($selector) {
		return "Sijax.getFormValues('{$selector}');";
	}
	
	/**
	 * Helper:
	 * Using the sijaxGetFormValues which returns an object
	 * We then json encode the object as parameter
	 * Using jQuery bind to bind event
	 *
	 * @todo Changed from jQuery live to on method for binding - how to rebind?
	 *      
	 * @param varchar $method        	
	 * @param varchar $url        	
	 * @param varchar $selector        	
	 * @param varchar $form_id        	
	 * @param varchar $script        	
	 * @param varchar $event        	
	 * @return string $string javascript
	 */
	public static function sijaxFunctionFormValues($method, $url, $selector, $form_selector, $script = NULL, $event = 'click', $dom = 'document') {
		
		$string = "\$({$dom}).on('{$event}','{$selector}', function() {";
		
		if ($script != NULL)
			$string .= $script;
			
			// Form data
		$string .= "var data = " . self::sijaxGetFormValues ( $form_selector );
		$string .= "console.log('That data 1: ' + data);";
		// JSON encode the data from the form in javascript clientside
		$string .= "data = JSON.stringify(data, null);";
		$string .= "console.log('That data 2: ' + data);";
		// Sijax request made with generated data as args
		$string .= "Sijax.request('{$method}','{$url}',''+ data + ''";
		
		$string .= ");";
		$string .= "return false;";
		$string .= "});\n";
		
		return $string;
	}
	
	/**
	 * Decodes formdata from sijaxFunctionFormValues()
	 *
	 * @param string $formdata
	 *        	json form data
	 * @return object (formdata->element)
	 */
	public static function sijaxDecodeFormValues($formdata) {
		return json_decode ( $formdata );
	}
	

	
} //End class

