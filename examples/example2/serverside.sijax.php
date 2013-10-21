<?php

//Load the autoloader for convenience or include manually Sijax.php and SijaxResponse.php
require_once '../autoloader.php';

/**
 * Basically the same example as the original Sijax implementation
 */
class simpleSijaxChat {
	
	public function saveMessage(SijaxResponse $objResponse, $message) {
		
		//Save $message to database, get new messages etc
	
		if (trim($message) === '') {
			
			//This makes it return immediately
			return $objResponse->alert('Empty messages are not allowed!');
		}
	
		$timeNow = microtime(true);
		$messageId = md5($message . $timeNow);
		$messageContainerId = 'message_' . $messageId;
	
		//The message will be invisible at first, and we'll show it using a jquery effect
		$messageFormatted = '
			<div id="' . $messageContainerId . '" style="opacity: 0;">
				 <i class="icon-comment-alt"></i> [<strong>' . date('H:i:s', (int) $timeNow) . '</strong>] ' . $message . '
			</div>';
	
		//Append the rendered message at the end
		$objResponse->htmlAppend('#messages', $messageFormatted);
	
		//Clear the textbox and give it focus in case it has lost it
		$objResponse->attr('#message', 'value', '');
		$objResponse->script("\$('#message').focus();");
	
		//Scroll down the messages area
// 		$objResponse->script("\$('#messages').attr('scrollTop', $('#messages').attr('scrollHeight'));");
		//Or if the above doesn't work:
		$objResponse->script("var selector    = \$('#messages');
							  var height = selector[0].scrollHeight;
							  selector.scrollTop(height);");
	
		//Make the new message appear in 400ms
		$objResponse->script("\$('#$messageContainerId').animate({opacity: 1}, 400);");
	}
	
	public function clearMessages(SijaxResponse $objResponse) {
		//Delete messages from the database...
	
		//Clear the messages container
		$objResponse->html('#messages', '');
	
		//Clear the textbox
		$objResponse->attr('#message', 'value', '');
	
		//Ensure the textbox has focus
		$objResponse->script("$('#message').focus();");
	}
	
}

//Clean the buffer
Sijax::cleanBuffer();

//Set Sijax arguments via POST (Default)
Sijax::setData ( $_POST );

//We register the class simpleSijaxChat and can access all public methods
Sijax::registerObject ( new simpleSijaxChat () );

// Tries to detect if this is a Sijax request,
// and executes the appropriate callback
Sijax::processRequest ();

// Do not continue processing
Sijax::stopProcessing ();