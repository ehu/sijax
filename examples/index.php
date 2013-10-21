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
include_once 'autoloader.php';

?>

<!DOCTYPE html>

<html lang="en">

<head>

<!-- PAGE TITLE -->
<title>Sijax examples</title>


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

	

	});
</script>

</head>
 
 

 
<!-- START CONTENT -->
<body>

<!-- Standard navbar from bootstrap -->
<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="#">Sijax Examples</a>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li><a href="example1/">Example 1</a></li>
      <li><a href="example2/">Example 2</a></li>
      <li><a href="example3/">Example 3</a></li>
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>

<div class="page-header">
  <h1>Sijax Examples <small></small></h1>
</div>

<!-- Information panel with details -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="icon-rocket text-info"></i> Examples</h3>
  </div>
  <div class="panel-body">
  <p>Some example uses of Sijax on both client and serverside</p>
	<div class="list-group">
	  <a href="example1/" class="list-group-item">Example 1: simple sijax examples</a>
	  <a href="example2/" class="list-group-item">Example 2: a simple chat application</a>
	  <a href="example3/" class="list-group-item"><span class="badge">comet</span>Example 3: dummy email sending with comet streaming</a>
	</div>
  </div>
</div>




</body>
</html>