<?php

//Load the autoloader for convenience or include manually Sijax.php and SijaxResponse.php
require_once '../autoloader.php';

class differentClass {

	
	public function alert3(SijaxResponse $objResponse) {

		$objResponse->alert('This alert is from alert3() in a different file '.__FILE__);
	}

}


//Clean the buffer
Sijax::cleanBuffer();

//Set Sijax arguments via POST (Default)
Sijax::setData ( $_POST );

//We register the class simpleSijaxBackend and can access all public methods
Sijax::registerObject ( new differentClass () );

// Tries to detect if this is a Sijax request,
// and executes the appropriate callback
Sijax::processRequest ();

// Do not continue processing
Sijax::stopProcessing ();