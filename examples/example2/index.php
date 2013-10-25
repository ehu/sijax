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

//Always include the autoloader
include_once '../autoloader.php';

?>

<!DOCTYPE html>

<html lang="en">

<head>
<!-- PAGE TITLE -->
<title>Simple Sijax example | example 2 chat</title>


<!-- JAVASCRIPT LINKED FILES -->
<!-- 1. Include jquery -->
<script type="text/javascript" src="//codeorigin.jquery.com/jquery-1.10.2.min.js"></script>
<!-- 2. Include sijax.js -->
<script type="text/javascript" src="../../src/js/sijax.js"></script>
<!-- 3. Including bootstrap and fontawesome (Optional) -->
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css" rel="stylesheet"> 
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script> 
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">
  
<!-- INLINE JAVASCRIPT -->
<script type="text/javascript" language="javascript">
	$(document).ready(function() {

		//Generate event listener to submitting message
		//We use $(selector).val() to get value of field and to make an argument get parsed as javascript you need to escape it like in a javascript string with \" + js + \"
		<?php echo Sijax::sijaxFunction('saveMessage', 'serverside.sijax.php', '#submit', array('message' => "\" + \$('#message').val() + \"")); ?>

		//Focus on message input
		$('#message').focus();

		//Generate event listener to clearing messages
		<?php echo Sijax::sijaxFunction('clearMessages', 'serverside.sijax.php', '#clear'); ?>
		

	});
</script>

</head>
 
<!-- START CONTENT -->
<body>
<div class="page-header">
  <h1>Sijax Example 2 <small>Simple chat example</small></h1>
</div>

<p>This is a simple example of sending the value of the input to serverside, then appending ($objResponse->htmlAppend) to the html already present in the textbox.</p>
<p>For a real chat application one can also consider the comet plugin and on serverside wait for a new message and push that to the client also using append</p>
<p></p>


<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="icon-comments"></i> Chat box</h3>
  </div>
  <div class="panel-body">
    <!-- This is our message list updated by Sijax -->
    <div id="messages" style="height: 200px;  overflow-y: scroll;"></div>
  </div>
</div>

	

<form class="form-inline col-lg-10" role="form">
  <div class="form-group">
    <label class="sr-only" for="message">Message</label>
    <input type="text" class="form-control" id="message" placeholder="Enter message" autocomplete="off">
  </div>
  <div class="form-group">
    <button id="submit" type="submit" class="btn btn-primary"><i class="icon-comments-alt"></i> Send</button>
    <button id="clear" type="reset" class="btn btn-danger"><i class="icon-remove"></i> Clear chat messages</button>
  </div>
  
  
</form>
	
</body>
</html>