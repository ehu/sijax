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
<title>Simple Sijax example | example1</title>


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

		//Calling server side method alert(SijaxReponse $objResponse, $message)
		//This will fire right after $(document).ready
		Sijax.request('showAlert', 'serverside.sijax.php', ['You are being alerted via the server side php script on $(document).ready']);
		//Which is essentially the same as this using the Sijax helper Sijax::sijaxRequest. Note that you do not need keys in your array, but the arguments are parsed by their index
		//<?php echo Sijax::sijaxRequest('alert', 'serverside.sijax.php', array('message' => 'You are being alerted via the server side php script')); ?>

		//Generating and binding request event to button sijaxFunction($method, $url, $selector, $param = array(), $script = NULL, $event = 'click', $dom = 'document')
		<?php echo Sijax::sijaxFunction('changeProgress', 'serverside.sijax.php', '#sijaxrandom', array('selector' => '#progress-bar1', 'text' => '#progresstext'));  ?>

		//Binding the text example
		<?php echo Sijax::sijaxFunction('textReplace', 'serverside.sijax.php', '#textreplace', array('selector' => '#textmanipulate'));  ?>
		<?php echo Sijax::sijaxFunction('textAppend', 'serverside.sijax.php', '#textappend', array('selector' => '#textmanipulate'));  ?>
		<?php echo Sijax::sijaxFunction('textPrepend', 'serverside.sijax.php', '#textprepend', array('selector' => '#textmanipulate'));  ?>
		
		//Binding alerts
		<?php echo Sijax::sijaxFunction('alert1', 'serverside.sijax.php', '#alert1');  ?>
		<?php echo Sijax::sijaxFunction('alert2', 'serverside.sijax.php', '#alert2');  ?>
		<?php echo Sijax::sijaxFunction('alert3', 'serverside2.sijax.php', '#alert3');  ?>

	});
</script>

</head>
 
<!-- START CONTENT -->
<body>
<div class="page-header">
  <h1>Sijax Example 1 <small>Simple example</small></h1>
</div>

<!-- Random progress -->
<h3>Generate an alert:</h3>
<div class="btn-group">

<button id="alert1" type="button" class="btn btn-primary">Alert</button>
<button id="alert2" type="button" class="btn btn-primary">Another alert</button>
<button id="alert3" type="button" class="btn btn-primary">Alert from different file</button>
</div>
<!-- Random progress -->
<h3>Random progress:</h3>
<div class="progress">
  <div id="progress-bar1" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
    <span class="sr-only" id="progresstext">60% Complete</span>
  </div>
</div>

<button id="sijaxrandom" type="button" class="btn btn-primary">Random progress</button>

<!-- Append html -->
<h3>Manipulate text/html</h3>
<div class="panel panel-default">
  <div class="panel-body" id="textmanipulate">
    This is the original text for this example
  </div>
</div>
<div class="btn-group">
<button id="textappend" type="button" class="btn btn-primary">Append text</button>
<button id="textprepend" type="button" class="btn btn-primary">Prepend text</button>
<button id="textreplace" type="button" class="btn btn-primary">Replace text</button>
</div>
</body>
</html>