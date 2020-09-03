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
   <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
   <meta http-equiv="Pragma" content="no-cache">
   <meta http-equiv="Expires" content="0">
   <meta name="author" content="GazeRecorder">
   <meta name="description" content="Accurate & Robust WebCam Online Eye-Tracking.">
   <meta property="og:title" content="GazeCloudAPI | Cross-platform Real-Time Eye-Tracking">
   <meta property="og:description" content="Accurate & Robust WebCam Online Eye-Tracking.">
   <meta property="og:image" content="https://api.gazerecorder.com/gazecloudapi.png">
   <meta property="og:url" content="https://api.gazerecorder.com">
   <meta name="twitter:description" content="Accurate & Robust WebCam Online Eye-Tracking.">
   <meta name="twitter:title" content="GazeCloudAPI | Cross-platform Real-Time Eye-Tracking">

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

   <!-- Javascript file for the images slide -->
   <script src="javascript/visualisation_test2.js"></script>
   <!-- API Javascript for the eye gaze implementation -->
   <script src="https://api.gazerecorder.com/GazeCloudAPI.js" ></script>
   <script src="https://api.gazerecorder.com/heatmapLive.js" ></script>

   <script type = "text/javascript" >

        function PlotGaze(GazeData) {
           /*
            GazeData.state // 0: valid gaze data; -1 : face tracking lost, 1 : gaze uncalibrated
            GazeData.docX // gaze x in document coordinates
            GazeData.docY // gaze y in document cordinates
            GazeData.time // timestamp
           */
            document.getElementById("GazeData").innerHTML = "GazeX: " + GazeData.GazeX + " GazeY: " + GazeData.GazeY;
            document.getElementById("HeadPhoseData").innerHTML = " HeadX: " + GazeData.HeadX + " HeadY: " + GazeData.HeadY + " HeadZ: " + GazeData.HeadZ;
            document.getElementById("HeadRotData").innerHTML = " Yaw: " + GazeData.HeadYaw + " Pitch: " + GazeData.HeadPitch + " Roll: " + GazeData.HeadRoll;
           //
           if( !document.getElementById("ShowHeatMapId").checked) { // gaze plot
                var x = GazeData.docX;
                var y = GazeData.docY;

                var gaze = document.getElementById("gaze");
                x -= gaze .clientWidth/2;
                y -= gaze .clientHeight/2;

              gaze.style.left = x + "px";
              gaze.style.top = y + "px";


                if(GazeData.state != 0)
                {
                  if( gaze.style.display  == 'block')
                  gaze  .style.display = 'none';
                }
                else
                {
                  if( gaze.style.display  == 'none')
                  gaze  .style.display = 'block';
                }
           }
       }

            //////set callbacks/////////
            GazeCloudAPI.OnCalibrationComplete =function(){RemoveHeatMap();; console.log('gaze Calibration Complete')  }
            GazeCloudAPI.OnCamDenied =  function(){ console.log('camera  access denied')  }
            GazeCloudAPI.OnError =  function(msg){ console.log('err: ' + msg)  }
            GazeCloudAPI.UseClickRecalibration = true;
           GazeCloudAPI.OnResult = PlotGaze;

           function start() {
              // document.getElementById("startid").style.display = 'none';
               //document.getElementById("firstid").style.display = 'block';
               GazeCloudAPI.StartEyeTracking();
               GazeCloudAPI.SetFps(15);
               /* to change the paragraph below the heading */
              // var para = document.getElementById("para");
               //para.innerHTML = "Click the First Test button to start with the test.";
           }

           function stop() {
               document.getElementById("stopid").style.display = 'none';
               GazeCloudAPI.StopEyeTracking();
           }
   </script>

</head>

<body onload="start()">

    <!-- division part for consent statement -->
    <div id="explanationDiv" class="content_paragraph" style="padding-top:10px; margin-top:0px; margin-bottom:20px; display:block;">
        <hr style="margin-top:0px;">
        <h2 id="testHeading" class="heading_font" style="margin-top: 0px;"> Tutorial Test </h2>
        <hr class="heading" style="height:2px;">
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
		
		<!-- HTML5 canvas must have set width and height, default is 300x150-->
        <canvas id="myCanvas" width="1280" height="720">
    </div>

    <div  style='background-color: lightblue; display: none;'>
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
          <!-- Button to start tutorial-->
          <button id="startTutorial" type="button" class="bttn">Take Tutorial Test</button>
          <!-- Button to start real test. Initially hidden-->
          <button id="startReal" type="button" class="bttn" style="display:none;">Take Real Test</button>
          <br/>
      </div>

</body>
</html>