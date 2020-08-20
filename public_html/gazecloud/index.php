<?php
	session_start(); // used to pass userId from page to page
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
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
		//Init arrays
		timeArray = [];
		docXArray = [];
		docYArray = [];
		gazeXArray = [];
		gazeYArray = [];
		headXArray = [];
		headYArray = [];
		headZArray = [];
		headYawArray = [];
		headPitchArray = [];
		headRollArray = [];
		//Counter for the index of arrays
		indexCounter = -1;
	
		function PlotGaze(GazeData) {
			//Update counter
			indexCounter += 1;
			
			/*
			GazeData.state // 0: valid gaze data; -1 : face tracking lost, 1 : gaze uncalibrated
			GazeData.docX // gaze x in document coordinates
			GazeData.docY // gaze y in document cordinates
			GazeData.time // timestamp
			*/

			document.getElementById("TimeData").innerHTML = "Time: " + GazeData.time;
			document.getElementById("GazeData").innerHTML = "GazeX: " + GazeData.GazeX + " GazeY: " + GazeData.GazeY;
			document.getElementById("HeadPhoseData").innerHTML = " HeadX: " + GazeData.HeadX + " HeadY: " + GazeData.HeadY + " HeadZ: " + GazeData.HeadZ;
			document.getElementById("HeadRotData").innerHTML = " Yaw: " + GazeData.HeadYaw + " Pitch: " + GazeData.HeadPitch + " Roll: " + GazeData.HeadRoll;
			
			//Push gaze data into array
			//timeArray.push("timestamp => " + GazeData.time);
			timeArray.push(GazeData.time);
			docXArray.push(GazeData.docX);
			docYArray.push(GazeData.docY);
			gazeXArray.push(GazeData.GazeX);
			gazeYArray.push(GazeData.GazeY);
			headXArray.push(GazeData.HeadX);
			headYArray.push(GazeData.HeadY);
			headZArray.push(GazeData.HeadZ);
			headYawArray.push(GazeData.HeadYaw);
			headPitchArray.push(GazeData.HeadPitch);
			headRollArray.push(GazeData.Roll);
			
			//console.log(docXArray);
			
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
		
		//How will be run this function to submit data? Button to begin next question and submit data?
		function ajax() {
			// begin ajax request
			var xhttp = new XMLHttpRequest();
  
			/*xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("demo").innerHTML =
					this.responseText;
				}
			};*/
			
			for (i = indexCounter; i < timeStamp.length; i += 1) {
				//do something with the data
				
				
				// post request to the PHP page
				xhttp.open("POST", "xxxx.php", true);
  
				// the data type in the POST data to JSON
				xhttp.setRequestHeader("Content-type", "application/json");
				xhttp.withCredentials = true; 

				// convert javascript object to a JSON string and submit with the POST request
				xhttp.send(JSON.stringify(data));
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
<?php
	require_once '../includes/functions.php';
	
	// don't do anything if consent is not true
	if ( isset( $_SESSION["consent"] ) and $_SESSION["consent"] ) {

		$dbName = 'humanastro';		// database name
		$Ucoll = 'users';			// user collection name

		$userId = sanitise_input($_GET["userId"]); // defend against malicious GET requests
		
		try {
			$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB
			$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
			
			// pass the user ID on to the next page
			// via a session variable
			$_SESSION["userId"] = (string)$userId;
			
			// create user ID object with which to ID the user record
			$_id = new MongoDB\BSON\ObjectID($userId);
			$filter = [ "_id" => $_id ]; // return only this user
			
			//Get data from JS to PHP
			//$eyeX = $_GET["eyeX"];
			//echo("GazeX value is: " . $eyeX);
			//alert("GazeX value is: " . $eyex);
			
		} catch (MongoDB\Driver\Exception\Exception $e) {
			$filename = basename(__FILE__);
			
			echo "The $filename script has experienced an error.\n";
			echo "It failed with the following exception:\n";
			echo "Exception: ", $e->getMessage(), "\n";
			echo "In file: ", $e->getFile(), "\n";
			echo "On line: ", $e->getLine(), "\n";
		}
	}
?>
   </body>
</html>