<?php
session_start();
//Load the autoloader for convenience or include manually Sijax.php and SijaxResponse.php
require_once '../autoloader.php';

class cometClass {
	
	/**
	 * Simple dummy sending emails class
	 * 
	 * For comet streaming the response object is buffered, but will be dispatched when you call flush()
	 * 
	 * @param SijaxPluginCometResponse $objResponse
	 * @param string $selector
	 */
	public function cometProgress(SijaxPluginCometResponse $objResponse, $selector) {
		
// 		$objResponse->alert('Starting comet streaming...');
// 		$objResponse->flush();
		
		$stime=explode(' ', microtime());
		$start_time = $stime[1]+$stime[0];
		
		//Make sure we set this to false
		$_SESSION['sijax']['comet']['stop'] = false;
		// close the session so it's available for the stop comet
		session_write_close();
		
		//Make sure we reset if button is clicked again
		$objResponse->css("{$selector}-success", 'width', '0%');
		
		//Now make the comet button disabled
		$objResponse->attr('#startcomet', 'disabled', 'disabled');
		$objResponse->flush();
		
		//Make the comet stop button enabled
		$objResponse->attr('#stopcomet', 'disabled', '');
		$objResponse->flush();
		
		//Add some information to the information panel
		$objResponse->html($selector.'-text','Started comet streaming!');
		$objResponse->flush();
		
		$objResponse->html($selector.'-text-speed','0');
		$objResponse->flush();
		
		$objResponse->html($selector.'-text-elapsed','0');
		$objResponse->flush();
		
		//This is the loop where you do some work 1% to 100% width of progress bar!
		for($i=0;$i<100;$i++) {
			
			//If we set the stop - stopit! NB: need to close the session since it will block us!
			session_start();
			if($_SESSION['sijax']['comet']['stop'] === true) break;
			session_write_close();
			
			//Keep track of time
			$stime=explode(' ',microtime());
			$stime=$stime[1]+$stime[0];
			$old = $stime;
			
			$objResponse->html($selector.'-text-elapsed',round($stime-$start_time,4));
			$objResponse->flush();
			
			//Dummy - if you are sending an email it takes a little time for each email...
			//This is where you want to call the internal method or external to send you email
			usleep(rand(10000,600000));
			
			$objResponse->css("{$selector}-success", 'width', $i.'%');
			$objResponse->flush();
			
			$objResponse->html("{$selector}-text-emails", ($i+1));
			$objResponse->flush();
			
			$stime=explode(' ',microtime());
			$stime=$stime[1]+$stime[0];
			$a=round($stime-$start_time,4);
			
			$objResponse->html("{$selector}-text-speed",round($stime-$old,4));
			$objResponse->flush();
			
			$objResponse->html($selector.'-text-elapsed',$a);
			$objResponse->flush();
			
			
		}
		
		//Finish off
		if($i==100)
			$objResponse->html($selector.'-text',"Finished email sending {$i} emails with comet streaming in ".round(($stime-$start_time),4).' seconds');
		else $objResponse->html($selector.'-text',"Sent {$i} emails with comet streaming in ".round(($stime-$start_time),4).' seconds before <strong>receiving stop signal</strong>');
		$objResponse->flush();
		
		//Make comet stop button disabled again
		//Make the comet stop button enabled
		$objResponse->attr('#stopcomet', 'disabled', 'disabled');
		$objResponse->flush();
		
		//Now make the comet button enabled again
		$objResponse->attr('#startcomet', 'disabled', '');
		$objResponse->flush();
	}
}

Sijax::cleanBuffer();
Sijax::setData($_POST);


SijaxPluginComet::registerCallback('cometProgress', array(new cometClass(), 'cometProgress'));

//Sijax_Plugin_Comet::registerCallback('readit', array(new CometHandler(), 'readit'));
// Sijax::registerObject(new rpc());
//Tries to detect if this is a Sijax request,
//and executes the appropriate callback
Sijax::processRequest();

//Do not continue processing
Sijax::stopProcessing();
