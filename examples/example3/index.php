<?php

/**
 * Simple Sijax example
 * 
 * Consists of:
 * - index.php with client side code (this file)
 * - serverside.sijax.php with server side processing
 * 
 * Requires the simple 'examples/autoloader.php' autoloader, or you can include your files manually
 * 
 * This files combines php and html, look at the generated html source to see what's going on
 */
session_start();
include_once '../autoloader.php';

?>

<!DOCTYPE html>

<html lang="en">

<head>

<!-- PAGE TITLE -->
<title>Sijax comet streaming example | example3</title>


<!-- JAVASCRIPT LINKED FILES -->
<!-- 1. Include jquery -->
<script type="text/javascript" src="//codeorigin.jquery.com/jquery-1.10.2.min.js"></script>

<!-- 2. Include sijax.js -->
<script type="text/javascript" src="../../src/js/sijax.js"></script>

<!-- 3. Include sijax_comet.js for comet streaming support -->
<script type="text/javascript" src="../../src/js/sijax_comet.js"></script>

<!-- 4. Including bootstrap and fontawesome (Optional) -->
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css" rel="stylesheet"> 
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script> 
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">
  
<!-- INLINE JAVASCRIPT -->
<script type="text/javascript" language="javascript">
	$(document).ready(function() {

		//cometFunction($method, $url, $selector, $param = array(), $script = NULL, $event = 'click', $dom = 'document')
		<?php echo SijaxPluginComet::cometFunction('cometProgress', 'serverside.sijax.comet.php','#startcomet', array('selector' => '#progress-bar'));  ?>

		//Stop via a simple sijax function event on the stopcomet button:
		<?php echo Sijax::sijaxFunction('stopComet', 'serverside.sijax.php', '#stopcomet');  ?>

	});
</script>

</head>
 
 

 
<!-- START CONTENT -->
<body>

<div class="page-header">
  <h1>Sijax Example 3 <small>Comet poll</small></h1>
</div>

<!-- Progress bar -->
<h3>Sending emails:</h3>
<p>Simple progressbar width set via comet where serverside iterates from 1-100% width of progressbar and provides a frontend for in this example sending many emails or other operations that iterate and take time and you want to show some feedback to the user</p>
<div class="progress">
  <div class="progress-bar progress-bar-success" style="width: 0%"  id="progress-bar-success">
    <!-- <span class="sr-only">0% Complete (success)</span> -->
  </div>
</div>

<!-- Information panel with details -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="icon-info-sign text-info"></i> Information panel</h3>
  </div>
  <div class="panel-body">
    <span id="progress-bar-text">Waiting 100 emails to send via comet streaming</span><br />
    <span id="progress-bar-text-emails">0</span> emails sent<br />
    Speed <span id="progress-bar-text-speed">0.0000</span> seconds/email<br />
    Time elapsed <span id="progress-bar-text-elapsed">0.0000</span> seconds<br /><br />
    <p class="text-warning"><i class="icon-warning-sign"></i> 
    This example also demonstrates how to handle a stopsignal via a sijax server backend file using $_SESSION to pass the argument 
    - and how important it is to control sessions via session_start() and session_close_write() 
    since sessions in php are file based and locked until the script returns and your comet streaming will be locked and blocks 
    the sijax request (jquery.ajax) until the comet streaming is finished.<br />
    This method can be used to make more control structures like pause operation
    </p>
  </div>
</div>

<div class="btn-group">
<button id="startcomet" type="button" class="btn btn-primary"><i class="icon-envelope-alt"></i> Start sending emails</button>
<button id="stopcomet" type="button" class="btn btn-danger" disabled="disabled"><i class="icon-power-off"></i> Stop sending emails</button>
</div>
<br />


</body>
</html>