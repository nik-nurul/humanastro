<?php
	session_start(); // used to pass userId from page to page
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	// user ID - this would be the user ID already created in the demographic question process
	## $_id = new MongoDB\BSON\ObjectId(bin2hex(random_bytes(12)));
	## $userIdStr = (string)$_id;
	
	require_once '../includes/functions.php';
	$userIdStr = sanitise_input($_GET["userId"]); // defend against malicious GET requests
?>

<!DOCTYPE HTML >
<html>
	<head>
		<title>GazeCloudAPI</title>		
		<script src="https://api.gazerecorder.com/GazeCloudAPI.js" ></script>
		<style type="text/css">
			body {
			overflow: hidden;
        }
		</style>

		<script type = "text/javascript" >

		// Init GazeData Array
		var GazeDataArray = [];
		//console.log(GazeDataArray);
		
		// new AJAX object
		var xhttp = new XMLHttpRequest();
		
		var userIdStr = "<?php echo $userIdStr ?>"; // get user ID string from PHP
		
		// takes the GazeData object, adds userData and converts to JSON
		// then sends the JSON by AJAX HTTP POST method to saveToDB.php
		// where it is appended to the MongoDB object for userIdStr
		function sendToDB(data) {
			// begin ajax request
			var jsonData = JSON.stringify(
				{
					"userIdStr": userIdStr,
					"GazeDataArray": data
				}
			);
  
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					console.log(""+this.responseText); // just log the output to JS console
				}
			};
			
			// post request to the PHP page
			xhttp.open("POST", "saveToDB.php", true);

			// the data type in the POST data to JSON
			xhttp.setRequestHeader("Content-type", "application/json");

			// convert javascript object to a JSON string and submit with the POST request
			xhttp.send(jsonData);
		}

		// pushes each GazeData point to an array
		// if the array is >= 10 elements, copy that array and append it to the MongoDB
		// then empty the GazeDataArray
		function saveData(GazeData){
			console.log('.'); // debug
			GazeDataArray.push(GazeData);
			if (GazeDataArray.length >= 10){
				sendToDB(GazeDataArray.slice()); // send a copy of the current array to the DB
				
				// if JS supported concurrency, we could lose data points here!
				// because it isn't, these commands should be 'atomic'
				
				GazeDataArray = []; // empty the array 
			}
		}

		// this is called everytime a GazaData message is received from the GazeCloud server
		function PlotGaze(GazeData) {
			saveData(GazeData); // send each GazeData point to the MongoDB
			
			// update gaze data on the page (after calibration)			
			var x = GazeData.docX;
			var y = GazeData.docY;
			
			var gaze = document.getElementById("gaze");
			x -= gaze .clientWidth/2;
			y -= gaze .clientHeight/2;
	
			gaze.style.left = x + "px";
			gaze.style.top = y + "px";
				
			if(GazeData.state != 0){
				if( gaze.style.display  == 'block')
					gaze  .style.display = 'none';
			} else {
				if( gaze.style.display  == 'none')
					gaze  .style.display = 'block';
			}
		}
		
        //////set callbacks/////////
		GazeCloudAPI.OnCalibrationComplete =function(){ console.log('gaze Calibration Complete')  }
		GazeCloudAPI.OnCamDenied =  function(){ console.log('camera  access denied')  }
		GazeCloudAPI.OnError =  function(msg){ console.log('err: ' + msg)  }
		GazeCloudAPI.UseClickRecalibration = true;
		GazeCloudAPI.OnResult = PlotGaze; 
		
	</script>
	</head>
	<body >
      <h1>GazeCloudAPI integration example</h1>
      <button  type="button" onclick="GazeCloudAPI.StartEyeTracking();">Start</button>
      <button  type="button" onclick="GazeCloudAPI.StopEyeTracking();">Stop</button>
      <div >
         <p >  
            Real-Time Result:
		 <p id = "TimeData" > </p>
         <p id = "GazeData" > </p>
         <p id = "HeadPhoseData" > </p>
         <p id = "HeadRotData" > </p>
         </p>
      </div>
      <div id ="gaze" style ='position: absolute;display:none;width: 100px;height: 100px;border-radius: 50%;border: solid 2px  rgba(255, 255,255, .2);	box-shadow: 0 0 100px 3px rgba(125, 125,125, .5);	pointer-events: none;	z-index: 999999'></div>
	  <div id ="ajax"></div>
   </body>
</html>