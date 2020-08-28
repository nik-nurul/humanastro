
<html>
<head>
   <title>Eye-tracking Experiments</title>

   <!-- Uses the GazeCloudAPI | WebCam Online Eye-Tracking -->
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

<style>
body {
   overflow: hidden;
}
</style>


<!-- Add the GazeCloud API Javascript -->
<script src="https://api.gazerecorder.com/GazeCloudAPI.js"></script>
<script src="https://api.gazerecorder.com/heatmapLive.js"></script>


<script type = "text/javascript">

   var myvar = "";	// A global variable that will be passed to the next php page for processing
   var trackflag = 0;	// Has tracking started?

   var tflag = 0; 	// Has reference time been set?
   var tstart = 0;

function PlotGaze(GazeData) {

/*
   GazeData.state // 0: valid gaze data; -1 : face tracking lost, 1 : gaze uncalibrated
   GazeData.docX // gaze x in document coordinates
   GazeData.docY // gaze y in document cordinates
   GazeData.time // timestamp
*/
   var gaze = document.getElementById("gaze");
   gx = gaze.clientWidth/2;
   gy = gaze.clientHeight/2;

   if (tflag == 0) {
      document.getElementById("image").src="xlarge_web.jpg";
      tstart = GazeData.time;
      var sys_string = "gx="+gx+"&gy="+gy+"&tstart="+tstart+"&data=";
      myvar = sys_string;
      tflag = 1;
   }

   console.log("gx" + gx + "gy" + gy);

	// Should it be docX, docY or GazeX,GazeY

   var string = Math.round(GazeData.docX - gx) + ","
          + Math.round(GazeData.docY - gy) + ","
	  + Math.round((GazeData.time-tstart)/10.0) + ";";

   if (trackflag == 1) {
      myvar = myvar + string;
   }
   console.log("String: " + string);	// Write to Console while debugging
   console.log("Myvar: " + myvar);


// Output to the screen: this is from the example
/*
   document.getElementById("GazeData").innerHTML = "GazeX: " + GazeData.GazeX + " GazeY: " + GazeData.GazeY;
   document.getElementById("HeadPhoseData").innerHTML = " HeadX: " + GazeData.HeadX + " HeadY: " + GazeData.HeadY + " HeadZ: " + GazeData.HeadZ;
   document.getElementById("HeadRotData").innerHTML = " Yaw: " + GazeData.HeadYaw + " Pitch: " + GazeData.HeadPitch + " Roll: " + GazeData.HeadRoll;
*/

   /*if( !document.getElementById("ShowHeatMapId").checked) // gaze plot*/
    if (showHeatMap == 1)
   {
      var x = GazeData.docX;
      var y = GazeData.docY;

      var gaze = document.getElementById("gaze");
      x -= gaze.clientWidth/2;
      y -= gaze.clientHeight/2;

      gaze.style.left = x + "px";
      gaze.style.top  = y + "px";

      if(GazeData.state != 0) {
         if( gaze.style.display  == 'block')
            gaze  .style.display = 'none';
      } else {
        if( gaze.style.display  == 'none')
          gaze  .style.display = 'block';
      }

   }
}

   //////set callbacks/////////

   GazeCloudAPI.OnCalibrationComplete = function() {
	   ShowHeatMap();
	   console.log('gaze Calibration Complete');
	   foo();
   }
   GazeCloudAPI.OnCamDenied =  function(){ console.log('camera  access denied')  }
   GazeCloudAPI.OnError     =  function(msg){ console.log('err: ' + msg)  }
   GazeCloudAPI.UseClickRecalibration = true;
   GazeCloudAPI.OnResult    = PlotGaze;

   function handleClickHeatMap(cb) {

   if( cb.checked)
   {
      ShowHeatMap();
      document.getElementById("gaze").style.display = 'none';
   } else {
      RemoveHeatMap()
   }

}



function start()
{
   GazeCloudAPI.StartEyeTracking();
   GazeCloudAPI.SetFps(15);
}

function stop()
{
   GazeCloudAPI.StopEyeTracking();
}

</script>
</head>

<body onload="start()">
<div id="gaze" width=900 height=720>
	<img id="image" width=900 height=720 style="border: 1px solid #ddd">
</div>

<div id="extras" onload='handleClickMap(this);'>
	<!--<input id="ShowHeatMapId" type="checkbox" checked="" onclick='handleClickHeatMap(this);'>-->
</div>

	<script>
var showHeatMap = 1;
	</script>

   <script>
	function foo(obj) {
	   trackflag = 1;
	   setTimeout(function() {
              stop();		// Stop eye tracking
	      var string = "http://astronomy.swin.edu.au/~cfluke/eyegaze/gotit.php?" + myvar;
	      window.location.href = string;
	   }, 10000);
	}
   </script>


   </body>
</html>
