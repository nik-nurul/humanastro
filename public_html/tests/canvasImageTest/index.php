<?php
// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// user ID - this would be the user ID already created in the demographic question process
$_id = new MongoDB\BSON\ObjectId(bin2hex(random_bytes(12)));
$userIdStr = (string)$_id;
	
require_once 'includes/functions.php';
//$userIdStr = sanitise_input($_GET["userId"]); // defend against malicious GET requests

?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="styles/canvasImageTest.css">
		<script src="https://api.gazerecorder.com/GazeCloudAPI.js"></script>
		<script src="javascript/canvasImageTest.js"></script>
	</head>
<body>

<header id="header">
	<div id="demo">
	  <h2>Change the image<br/>
	  click button or press the spacebar</h2>
	  <button type="button" id="changeContent">Change Image</button>
	</div>
	<button  type="button" id="startEyeTracking">Start</button>
	<button  type="button" id="stopEyeTracking">Stop</button>
	<div >
		<p >  
			Real-Time Result:
			<p id = "Calibration" > </p>
			<p id = "StartTime" > </p>
			<p id = "TimeData" > </p>
			<p id = "SessionTime" > </p>
			<p id = "Frame" > </p>
			<p id = "imageName" > </p>
			<p id = "windowPixelRatio" > </p>
			<p id = "CanvasScale" > </p>
			<p id = "innerWidth" > </p>
			<p id = "innerHeight" > </p>
			<p id = "AbsInnerWidth" > </p>
			<p id = "AbsInnerHeight" > </p>
			<p id = "PagePlotX" > </p>
			<p id = "PagePlotY" > </p>
			<p id = "MouseDocX" > </p>
			<p id = "MouseDocY" > </p>
			<p id = "MouseAbsDocX" > </p>
			<p id = "MouseAbsDocY" > </p>
			<p id = "MouseScaledDocX" > </p>
			<p id = "MouseScaledDocY" > </p>
			<p id = "GazeDataDocX" > </p>
			<p id = "GazeDataDocY" > </p>
			<!--p id = "MouseScreenX" > </p>
			<p id = "MouseScreenY" > </p>
			<p id = "GazeDataX" > </p>
			<p id = "GazeDataY" > </p>
			<p id = "HeadPhoseData" > </p>
			<p id = "HeadRotData" > </p-->
		</p>
	</div>
	
	
</header>

<!-- the circle that follows the user's gaze -->
<div id ="gaze"></div>

<canvas id="myCanvas">

</body>
</html>
