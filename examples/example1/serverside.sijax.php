<?php

//Load the autoloader for convenience or include manually Sijax.php and SijaxResponse.php
require_once '../autoloader.php';

class simpleSijaxBackend {
	
	/**
	 * 
	 * @param SijaxReponse $objResponse the response object
	 * @param string $message a message to be shown via javascript 'alert()' on client side
	 */
	public function showAlert(SijaxResponse $objResponse, $message) {
		
		$objResponse->alert($message);
		
	}
	
	public function changeProgress(SijaxResponse $objResponse, $selector, $text_selector) {
		
		$value = rand(1,100);
		
		//Change width of progress bar
		$objResponse->css($selector, 'width', $value.'%');
		
		//Change text
		$objResponse->html($text_selector, $value.'%');
	}
	
	public function textAppend (SijaxResponse $objResponse, $selector) {
		
		$objResponse->htmlAppend($selector, ' Append some text <strong>and also html</strong>');
	}
	public function textReplace (SijaxResponse $objResponse, $selector) {
		
		$objResponse->html($selector, 'The entire text or html has been replaced by this' );
	}
	public function textPrepend (SijaxResponse $objResponse, $selector) {
		
		$objResponse->htmlPrepend($selector, 'Prepend some text ' );
	}
	
	public function alert1(SijaxResponse $objResponse) {
		
		$objResponse->alert('This is the alert from alert1() in file '.__FILE__);
	}
	public function alert2(SijaxResponse $objResponse) {
		
		$objResponse->alert('This is the alert from alert2() in file '.__FILE__);
	}
	
}


//Clean the buffer
Sijax::cleanBuffer();

//Set Sijax arguments via POST (Default)
Sijax::setData ( $_POST );

//We register the class simpleSijaxBackend and can access all public methods
Sijax::registerObject ( new simpleSijaxBackend () );

// Tries to detect if this is a Sijax request,
// and executes the appropriate callback
Sijax::processRequest ();

// Do not continue processing
Sijax::stopProcessing ();