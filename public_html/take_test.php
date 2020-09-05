<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbName = 'humanastro';		// database name
$collName = 'users';	// collection name

require_once 'includes/functions.php';
// don't do anything if consent is not true
if ( isset( $_SESSION["consent"] ) and $_SESSION["consent"] ) {
	$userIdStr = sanitise_input($_GET["userId"]); // defend against malicious GET requests
	
	try {
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB
		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
		$_id = new MongoDB\BSON\ObjectID($userIdStr);
		$filter = [ "_id" => $_id ];
		set_current_page($bulk, $_id, basename(__FILE__)); // write the name of the current page to the user record
		$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk);
		
	// exception handling for the database connection	
	} catch (MongoDB\Driver\Exception\Exception $e) {

		$filename = basename(__FILE__);
		
		echo "The $filename script has experienced an error.\n"; 
		echo "It failed with the following exception:\n";
		
		echo "Exception:", $e->getMessage(), "\n";
		echo "In file:", $e->getFile(), "\n";
		echo "On line:", $e->getLine(), "\n";       
	}	
} else {
	// consent was false
	echo '
<!DOCTYPE html>
<html lang="en">
	<p>Consent was not given</p>
	<p>Do something!</p>
</html>
';
// TO DO - handle no consent error better here
exit();
	}		
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Tutorial Test</title>
  <!-- For testing, I remove the includes/head-base first. will be added again once finalized-->
  <meta charset="utf-8" />
  <meta name="description" content="SEPA Project G3" />
  <meta name="keywords" content="Swinburne, Astronomy, Astrophysics, Research, Survey" />
  <meta name="author" content="SWE40001_Group_3" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta property="og:title" content="GazeCloudAPI | Cross-platform Real-Time Eye-Tracking">
  <meta property="og:description" content="Accurate & Robust WebCam Online Eye-Tracking.">
  <meta property="og:image" content="https://api.gazerecorder.com/gazecloudapi.png">
  <meta property="og:url" content="https://api.gazerecorder.com">
  <meta name="twitter:description" content="Accurate & Robust WebCam Online Eye-Tracking.">
  <meta name="twitter:title" content="GazeCloudAPI | Cross-platform Real-Time Eye-Tracking">

  <link rel="stylesheet" type="text/css" href="styles/WebsiteStyle2.css">

  <!-- API Javascript for the eye gaze implementation -->
  <script src="https://api.gazerecorder.com/GazeCloudAPI.js" ></script>

  <!-- Javascript file for the images slide -->
  <script src="javascript/visualisation_test.js"></script>

</head>
<body>
	<!-- embedding user ID in page for javascript to access -->
	<label id="userId" hidden="true"><?php echo $userIdStr ?></label>

    <!-- division part for the explanation -->
	<div id="explanationDiv" class="content_paragraph" style="padding-top:10px; margin-top:0px; margin-bottom:20px; display:block;">
        <hr style="margin-top:0px;">
        <h2 id="testHeading" class="heading_font" style="margin-top: 0px;"> Eye Calibration </h2>
        <hr class="heading" style="height:2px;"/>
<!--    <div id="explanationDiv" class="content_paragraph" style="display:block;">
        <hr class="heading"/>
        <h2 id="testHeading" class="heading_font"> Eye Calibration </h2>
        <hr class="heading"/> -->
        <p id="explanationPara" class="paragraph_font">
            There will be an eye calibration test before the visualisation tests take place.<br/><br/><br/>
            <b>The browser's window will be put into fullscreen mode. Please do not resize it.</b><br/>
        </p><br/><br/>
    </div>

    <!-- division for images-->
    <div id="canvasDiv" style="display:none;">
        <!-- canvas to draw the images -->

        <canvas id="myCanvas">
    </div>

    <div id ="gaze" style ='position: absolute;display:none;width: 100px;height: 100px;border-radius: 50%;border: solid 2px  rgba(255, 255,255, .2);	box-shadow: 0 0 100px 3px rgba(125, 125,125, .5);	pointer-events: none;	z-index: 999999'></div>

	<!-- division for buttons. -->
	<div id='buttonsDiv' class="content_paragraph" style="margin-top:20px; margin-bottom:20px; float:right;">
		<!-- Button to start eye calibration-->
		<button id="startCalibration" type="button" class="bttn">Start Eye Calibration</button>
		<!-- Button to start calibration refinement. Initially hidden -->
		<button id="startRefineCal" type="button" class="bttn" style="display:none;">Refine Calibration</button>
		<!-- Button to start tutorial. Initially hidden -->
		<button id="startTutorial" type="button" class="bttn" style="display:none;">Take Tutorial Test</button>
		<!-- Button to start real test. Initially hidden-->
		<button id="startReal" type="button" class="bttn" style="display:none;">Take Real Test</button>
		<br/>
	</div>

</body>
</html>
