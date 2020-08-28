<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <title>Eye Calibration and Visualisation Tests</title>
   <!-- Previously is head-base.html from includes. Will use that again once finalized -->
   <meta charset="utf-8" />
   <meta name="description" content="SEPA Project G3" />
   <meta name="keywords" content="Swinburne, Astronomy, Astrophysics, Research, Survey" />
   <meta name="author" content="SWE40001_Group_3" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <link rel="stylesheet" type="text/css" href="styles/WebsiteStyle2.css">

   <!-- Javascript file for the images slide -->
   <script src="javascript/visualisation_test.js"></script>
</head>

<body>

    <!-- division part for consent statement -->
    <div id="explanationDiv" class="content_paragraph" style="margin-top:100px; margin-bottom:20px; display:block;">
        <h2 id="testHeading" class="heading_font"> Tutorial Test </h2>
        <hr class="heading">
        <p id="explanationPara" class="paragraph_font">
            There will be a tutorial test before the real test take place. Below is the instructions that need
            to be followed to complete the test successfully.
        </p><br/><br/>
        <div id="explanationBullet">
            <p>
              <!-- 7/5/2020: The instructions written below are not the real instructions. It is just purely for the prototype purpose only. -->
                <ul class="bullet_style paragraph_font">
                    <li>There will be a series of images that will be presented</li>
                    <li>Please stare at the images and find similar patterns</li>
                    <li>Each image will have its own timer</li>
                    <li>The timer wil start as soon as you click 'Take Test'</li>
                    <li>There will be 3 images for the tutorial test</li>
                    <li>There will be 6 images for the real test</li>
                </ul>
            </p>
        </div>
        <p class="paragraph_font">
            Click the button to proceed.
        </p>
    </div>

      <!-- division for images-->
      <div id="canvasDiv" style="display:none;">
          <!-- canvas to draw the images -->
          <canvas id="myCanvas">
      </div>

      <!-- division for buttons. -->
      <div id='buttonsDiv'class="content_paragraph" style="margin-top:20px; margin-bottom:20px; float:right;">
          <!-- Button to start tutorial-->
          <button id="startTutorial" type="button" class="bttn">Take Tutorial Test</button>
          <!-- Button to start real test. Initially hidden-->
          <button id="startReal" type="button" class="bttn" style="display:none;">Take Real Test</button>
          <br/>
      </div>

</body>
</html>
