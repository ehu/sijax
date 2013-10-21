<?php
/**
 * This is the Sijax response class for comet callbacks.
 * It supports everything that the base response class provides, and only adds flushing capabilities (using the `flush()` method), 
 * allow you to push commands to the browser, without exiting the response function.
 * 
 * @author Einar Huseby <https://github.com/ehu>
 * @author Slavi Pantaleev <https://github.com/spantaleev>
 * @license BSD, see BSD-LICENSE.txt
 */
final class SijaxPluginCometResponse extends SijaxResponse {

	private $_flushesCount = 0;

	/**
	 * Sends the commands accumulated so far to the browser.
	 * You can continue pushing new commands which will be
	 * sent either at the next flush() or when the response functions exits.
	 */
	public function flush() {
		echo $this->getJson();

		if ($this->_flushesCount === 0) {
			//The first flush is ignored in Chrome and IE for some reason
			//Echoing this additional data, seems to fix it
			echo "\n<script type='text/javascript'></script>\n\n";
		}

		if(ob_get_level() != 0) ob_flush();
		flush();

		++ $this->_flushesCount;

		return parent::clearCommands();
	}

	public function getJson() {
		
		//Clean buffer!
		Sijax::cleanBuffer();
		
		ob_start();

		echo '
		<script type="text/javascript">
			window.parent.Sijax.processCommands(', parent::getJson(), ');
		</script>';

		return ob_get_clean();
	}

}
?>
