<?php
session_start();

//Load the autoloader for convenience or include manually Sijax.php and SijaxResponse.php
require_once '../autoloader.php';

class simpleCometSession {

	public function stopComet() {
		
		$_SESSION['sijax']['comet']['stop'] = true;
		session_write_close();
	}

}


//Clean the buffer
Sijax::cleanBuffer();

//Set Sijax arguments via POST (Default)
Sijax::setData ( $_POST );

//We register the class simpleCometSession and can access all public methods
Sijax::registerObject ( new simpleCometSession () );

// Tries to detect if this is a Sijax request,
// and executes the appropriate callback
Sijax::processRequest ();

// Do not continue processing
Sijax::stopProcessing ();