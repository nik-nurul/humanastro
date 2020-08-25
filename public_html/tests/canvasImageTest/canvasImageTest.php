<?php
// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<html>
<link rel="stylesheet" type="text/css" href="canvasImageTest.css">
<script src="canvasImageTest.js"></script>
<body>

<header id="header">
	<p><h1>Test of changing images in a canvas</h1>
	<p>

	<div id="demo">
	  <h2>Change the image - click button or press the spacebar</h2>
	  <button type="button" id="changeContent">Change Image</button>
	</div>
</header>

<canvas id="myCanvas">

</body>
</html>
