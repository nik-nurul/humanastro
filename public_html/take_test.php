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

  <!-- Style the website -->
  <style>
       @import url('https://fonts.googleapis.com/css?family=Russo One');

       body {
         background-color: rgb(22, 6, 19);
       }

      .bttn {
        background-color: rgb(47, 33, 48);
        color: rgba(250, 250, 250, 0.8);
        border: 3px solid rgba(250, 250, 250, 0.8);
        padding: 15px;
        font-size: 15px;
        margin: auto;
        margin-right: 100px;
        display: inline-block;
        text-align: right;
        text-decoration: none;
        float: right;
      }

      .bttn:hover {
        background-color: white;
        color: rgb(47, 33, 48);
      }

      .heading_font {
        font-family: 'Russo One';
        letter-spacing: 3.5px;
        color: rgba(250, 250, 250, 0.8);
        font-size: 39px;
      }
  </style>

  <!-- API Javascript for the eye gaze implementation -->
  <script src="https://api.gazerecorder.com/GazeCloudAPI.js" ></script>
  <script src="https://api.gazerecorder.com/heatmapLive.js" ></script>

  <!-- Javascript file for the images slide -->
  <script src="javascript/visualisation_test.js"></script>

  <!-- <script>
      /* Used only when 'calibration test' button is clicked */
      function changeToTutorial(){
        /* Change the text for the heading */
        var changeHead = document.getElementById("testHeading");
        changeHead.innerHTML = "Tutorial Test";

        /* Change the text for explanation paragraph*/
        var explainPara = document.getElementById("explanationPara");
        explainPara.innerHTML = "Congratulations on finishing the eye calibration! There will be a tutorial test before the real test take place <br/>"+
        "Below is the instructions that need to be followed to complete the test successfully:" +
        "<div id='explanationBullet'><p><ul class='bullet_style paragraph_font'><li>There will be a series of images that will be presented</li>"+
        "<li>Please stare at the images and find similar patterns</li><li>Each image will have its own timer</li><li>The timer wil start as soon as you click 'Take Test'</li>" +
        "<li>There will be 3 images for the tutorial test</li><li>There will be 6 images for the real test</li></ul></p></div>";

         /*	Hide 'Start Eye Calibration' button*/
         var caliBttn = document.getElementById("startCalibration");
         caliBttn.style.display = "none";

         /* Show "Take Tutorial Test" button */
         var tuteBttn = document.getElementById("startTutorial");
         tuteBttn.style.display = "block";
      }
  </script> -->
</head>

<body>

    <!-- division part for yhe explanation -->
    <div id="explanationDiv" class="content_paragraph" style="padding-top:10px; margin-top:0px; margin-bottom:20px; display:block;">
        <hr style="margin-top:0px;">
        <h2 id="testHeading" class="heading_font" style="margin-top: 0px;"> Eye Calibration </h2>
        <hr class="heading" style="height:2px;"/>
        <p id="explanationPara" class="paragraph_font">
            There will be an eye calibration test before the visualisation tests take place. <br/><br/><br/>
            <b>Do not resize the browser's window </b><br/>
        </p><br/><br/>
    </div>

    <!-- division for images-->
    <div id="canvasDiv" style="display:none;">
        <!-- canvas to draw the images -->

		<!-- HTML5 canvas must have set width and height, default is 300x150-->
        <canvas id="myCanvas" width="1280" height="720">
    </div>

    <div style='background-color: lightblue; display: none;'>
         <p >
            Real-Time Data:
         <p id = "GazeData" > </p>
         <p id = "HeadPhoseData" > </p>
         <p id = "HeadRotData" > </p>
         </p>
    </div>
    <div id ="gaze" style ='position: absolute;display:none;width: 100px;height: 100px;border-radius: 50%;border: solid 2px  rgba(255, 255,255, .2);	box-shadow: 0 0 100px 3px rgba(125, 125,125, .5);	pointer-events: none;	z-index: 999999'></div>

    <label for="ShowHeatMapId" >
        <input style="display:none;" id="ShowHeatMapId" type="checkbox"  onclick='handleClickHeatMap(this);'>
    </label>


      <!-- division for buttons. -->
      <div id='buttonsDiv'class="content_paragraph" style="margin-top:20px; margin-bottom:20px; float:right;">
          <!-- Button to start eye calibration-->
          <button id="startCalibration" type="button" class="bttn" onclick="start()">Start Eye Calibration</button>
          <!-- Button to start tutorial. Initially hidden -->
          <button id="startTutorial" type="button" class="bttn" style="display:none;">Take Tutorial Test</button>
          <!-- Button to start real test. Initially hidden-->
          <button id="startReal" type="button" class="bttn" style="display:none;">Take Real Test</button>
          <!-- Button to start real test. Initially hidden-->
          <button id="finishBttn" type="button" class="bttn" style="display:none;" onclick="stop()">Finish</button>
          <br/>
      </div>


</body>
</html>
