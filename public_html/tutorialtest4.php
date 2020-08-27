gk<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

  <link rel="stylesheet" type="text/css" href="styles/WebsiteStyle2.css">

	<!-- javascript file for the timer-->
	<script src="javascript/tutorialtest.js"></script>
  <!-- Javascript file for the images slide -->
  <script src="javascript/testImages2.js"></script>
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
                  This is a tutorial test before the real test take place. Below is the instructions that need
                  to be followed to complete the test successfully.
              </p><br/><br/>
              <p>
                <!-- 7/5/2020: The instructions written below are not the real instructions. It is just purely for the prototype purpose only. -->
                  <ul class="bullet_style paragraph_font">
                      <li>There will be a series of images that will be presented</li>
                      <li>Please stare at the images and find similar patterns</li>
                      <li>Each image will have its own timer</li>
                      <li>The timer wil start as soon as you click 'Take Test'</li>
                      <li>There will be 1 image for the tutorial test</li>
                      <li>There will be 6 images for the real test</li>
                  </ul>
              </p>
          </div>
          <!-- division for images-->
          <div id='canvasDiv' style="display: none;">
              <!-- canvas to draw the images -->
              <canvas id="myCanvas">
          </div>
          <!-- division for buttons. might be removed buttons are not needed -->
          <div id='buttonsDiv'class="content_paragraph" style="margin-top:20px; margin-bottom:20px; float:right;">
              <!-- Button to start tutorial-->
              <button id="startCalibration" type="button" class="bttn">Start Calibration</button>
              <!-- This button will appear when the calibration is completed -->
              <button  id="startTutorial"  style="display:none;" class="bttn" type="button">Take Tutorial Test</button>
              <br/>
              <br/>
          </div>
      </section>

<?php
require 'includes/footer.html';
?>

</body>
</html>
