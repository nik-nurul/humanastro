<<<<<<< HEAD
<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Tutorial Test</title>
  <!-- For testing, I remove the includes/head-base first because different stylesheet is used here.
  The original includes will be added again once finalized-->
  <meta charset="utf-8" />
  <meta name="description" content="SEPA Project G3" />
  <meta name="keywords" content="Swinburne, Astronomy, Astrophysics, Research, Survey" />
  <meta name="author" content="SWE40001_Group_3" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" type="text/css" href="styles/WebsiteStyle2.css">

  <!-- To include GazeCloud API funcitions -->
  <script src="https://api.gazerecorder.com/GazeCloudAPI.js" ></script>
	<!-- javascript file for the timer-->
	<script src="javascript/tutorialtest.js"></script>
  <!-- Javascript file for the images slide -->
  <script src="javascript/calibration_test.js"></script>
</head>

<body>
<?php
require 'includes/header.html';
?>
	<!-- division for content-->
    <section>
        <!-- There will be no feedback button in tutorial test page and test pages -->

          <!-- division part for consent statement -->
          <div id="explanationDiv" class="content_paragraph" style="margin-top:100px; margin-bottom:20px; display: block;">
              <h2 class="heading_font"> Tutorial Test </h2>
              <hr class="heading">
              <p class="paragraph_font">
                  To be able to proceed with the visualisation tests, there will be an eye calibration test first.<br/><br/>
                  Click the "Start Calibration" button to begin. A new window will pop up and the rest of the visualisation tests will happen there. <br/><br/><br/>
                  Good luck!
              </p><br/><br/>
          </div>

          <!-- division for calibration button -->
          <div id='buttonsDiv'class="content_paragraph" style="margin-top:20px; margin-bottom:20px; float:right;">
              <!-- Button to start tutorial-->
              <button id="startCalibration" type="button" class="bttn">Start Calibration</button>
              <br/>
              <br/>
          </div>
      </section>

<?php
require 'includes/footer.html';
?>

</body>
</html>
=======
<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Tutorial Test</title>
  <!-- For testing, I remove the includes/head-base first because different stylesheet is used here.
  The original includes will be added again once finalized-->
  <meta charset="utf-8" />
  <meta name="description" content="SEPA Project G3" />
  <meta name="keywords" content="Swinburne, Astronomy, Astrophysics, Research, Survey" />
  <meta name="author" content="SWE40001_Group_3" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" type="text/css" href="styles/WebsiteStyle2.css">

	<!-- javascript file for the timer-->
	<script src="javascript/tutorialtest.js"></script>
  <!-- Javascript file for the images slide -->
  <script src="javascript/calibration_test.js"></script>
</head>

<body>
<?php
require 'includes/header.html';
?>
	<!-- division for content-->
    <section>
        <!-- There will be no feedback button in tutorial test page and test pages -->

          <!-- division part for consent statement -->
          <div id="explanationDiv" class="content_paragraph" style="margin-top:100px; margin-bottom:20px; display: block;">
              <h2 class="heading_font"> Tutorial Test </h2>
              <hr class="heading">
              <p class="paragraph_font">
                  To be able to proceed with the visualisation tests, there will be an eye calibration test first.<br/><br/>
                  Click the "Start Calibration" button to begin. A new window will pop up and the rest of the visualisation tests will happen there. <br/><br/><br/>
                  Good luck!
              </p><br/><br/>
          </div>

          <!-- division for calibration button -->
          <div id='buttonsDiv'class="content_paragraph" style="margin-top:20px; margin-bottom:20px; float:right;">
              <!-- Button to start tutorial-->
              <button id="startCalibration" type="button" class="bttn" onclick="GazeCloudAPI.StartEyeTracking()">Start Calibration</button>
              <br/>
              <br/>
          </div>
      </section>

<?php
require 'includes/footer.html';
?>

</body>
</html>
>>>>>>> 005fb17db3e971b701a43b1031253fda613daf90
