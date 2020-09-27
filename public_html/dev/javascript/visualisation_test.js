"use strict";

// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

//{ **** GazeCloud global vars ****

// Gaze Calibration type: 0 is accurate calibration (much slower - default); 1 is fast calibration
GazeCloudAPI.CalibrationType = 1;
var maxGazaDataArraySize = 30; // save arrays of this size in browser memory before sending to MongoDB
var GazeFPS = 30; // Webcam FPS rate for GazeCloud

var gazeDebug = true; // if true, will always doPlotGaze, and will change gaze color when on target

var doPlotGaze = gazeDebug; // if true, plot the gaze on screen
var GazeDataArray = [];
var xhttp = new XMLHttpRequest();
var userIdStr;  // get user ID string from PHP
var startTime;  // used to anonymise the timestams on saved data
var mouseDocX, mouseDocY, mouseScreenX, mouseScreenY;

//}
// **** end GazeCloud global vars ****


//{ **** taskrunner global vars ***

// these image names could be gotten from the MongoDB
// or the images themselves could be stored there, in base64 text format

var task_data; // the whole task_data object from the MongoDB user record

var task_dir = "tasks";

// task metadata will eventually be held in the MongoDB

var c, ctx, img; // canvas, canvas-context, image vars
img = new Image(); // initialise image var with a blank image
var imgScaleRatio; // scale ratio of original image to displayed image in canvas
var hRatio, vRatio; // scale ratio of horizontal and vertical dimensions of image vs canvas
var current_task, current_subtask; // the current task_data elements

// the current task and subtask numbers - zero based
// these iterate on the array index numbers, not the actual task_num and subtask_num properties
// the design preference was to directly reference the task_num and subtask_num properties in the mongoDB
// but querying the MongoDB for both these properties and returning the correct subtask object didn't seem to be possible
var task_num = -1;
var subtask_num = -1; 

var timeGazeInsideTargetArea = null;

var targetFoundEvent = new CustomEvent("tgtFnd"); // this allows a subtask to end when the user meets success criteria
var handleTargetFound; // hoisting functions so they can be removed as eventHandlers globally


//}
// **** end taskrunner global vars

//{ **** Utility functions ****

// unselects text - useful when showing instructions after refine calibration
// from: https://stackoverflow.com/questions/6562727/is-there-a-function-to-deselect-all-text-using-javascript
function clearSelection()
{
	if (window.getSelection) {window.getSelection().removeAllRanges();}
	else if (document.selection) {document.selection.empty();}
}

// rounds a floating-point number to digits number of places
function roundTo(n, digits) {
	var negative = false;
	
	if (digits === undefined) {
		digits = 0;
	}
	if (n < 0){
		negative = true;
		n = n * -1;
	}

	var multiplicator = Math.pow(10, digits);
	n = parseFloat((n * multiplicator).toFixed(3));
	var test =(Math.round(n) / multiplicator);
	return +(test.toFixed(digits));
	
	if(negative){
		n = (n * -1).toFixed(digits);
	}
	
	return n;
}

//https://www.digitalocean.com/community/tutorials/js-fullscreen-api
function activateFullscreen(element) {
  if(element.requestFullscreen) {
    element.requestFullscreen();        // W3C spec
  }
  else if (element.mozRequestFullScreen) {
    element.mozRequestFullScreen();     // Firefox
  }
  else if (element.webkitRequestFullscreen) {
    element.webkitRequestFullscreen();  // Safari
  }
  else if(element.msRequestFullscreen) {
    element.msRequestFullscreen();      // IE/Edge
  }
  document.body.style.overflow = "hidden"; // prevent scrollbars from appearing
}

function deactivateFullscreen() {
  if(document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) {
    document.webkitExitFullscreen();
  }
  document.body.style.overflow = "auto"; // restore normal scrollbar behaviour
}

// records the mouse coordinates on a mousemove event
function setMouseCoords(event){
	mouseDocX = event.clientX;
	mouseDocY = event.clientY;
	mouseScreenX = event.screenX;
	mouseScreenY = event.screenY;
}

// return the distance between two points
// x1,y1 - the coordinates of the first point
// x2,y2 - the coordinates of the secpnd point
function dist2points(x1,y1,x2,y2){
	return Math.hypot(x2-x1, y2-y1);
}
//}
// **** end Utility functions

//{ **** GazeCloud functions ****

// takes the GazeData object, adds userData and converts to JSON
// then sends the JSON by AJAX HTTP POST method to saveToDB.php
// where it is appended to the MongoDB object for userIdStr
function sendToDB(data) {
	// begin ajax request
	var jsonData = JSON.stringify(
		{
			"userIdStr": userIdStr,
			"task_num": task_num,
			"subtask_num": subtask_num,
			"GazeDataArray": data
		}
	);

	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText); // just log the output to JS console
//			console.log(""+this.responseText); // just log the output to JS console
		}
	};
	
	// post request to the PHP page
	xhttp.open("POST", "saveToDB.php", true);

	// the data type in the POST data to JSON
	xhttp.setRequestHeader("Content-type", "application/json");

	// convert javascript object to a JSON string and submit with the POST request
	xhttp.send(jsonData);
}

// saves a subtask result to the DB
function saveResult() {
	// begin ajax request
	var jsonData = JSON.stringify(
		{
			"userIdStr": userIdStr,
			"task_num": task_num,
			"subtask_num": subtask_num,
			"subtask_result": current_subtask.subtask_result
		}
	);

	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText); // just log the output to JS console
		}
	};
	
	// post request to the PHP page
	xhttp.open("POST", "saveResult.php", true);

	// the data type in the POST data to JSON
	xhttp.setRequestHeader("Content-type", "application/json");

	// convert javascript object to a JSON string and submit with the POST request
	xhttp.send(jsonData);
}

// pushes each GazeData point to an array
// if the array is >= 10 elements, copy that array and append it to the MongoDB
// then empty the GazeDataArray
function saveData(GazeData){
		
	GazeDataArray.push(GazeData); 
	if (GazeDataArray.length >= maxGazaDataArraySize){
		sendToDB(GazeDataArray.slice()); // send a copy of the current array to the DB
		
		// if JS supported concurrency, we could lose data points here!
		// because it isn't, these commands should be 'atomic'
		
		GazeDataArray = []; // empty the array 
	}
}

// show the gaze position in the browser window
function PlotGaze(GazeData) {

	// update gaze data on the page (after calibration)			
	var x = GazeData.docX;
	var y = GazeData.docY;
	
	var gaze = document.getElementById("gaze");
	x -= gaze .clientWidth/2;
	y -= gaze .clientHeight/2;

	// only update gaze position if doPlotGaze == true
	if (gazeDebug || doPlotGaze){
		gaze.style.left = x + "px";
		gaze.style.top = y + "px";
	}
	
	// only display gaze position if gaze is valid and doPlotGaze == true
	if(GazeData.state != 0 || !doPlotGaze || !gazeDebug ){
		// do not display gaze position
		if( gaze.style.display  == 'block')
			gaze.style.display   = 'none';
	} else {
		// display gaze position
		if( gaze.style.display  == 'none'){
			gaze.style.display   = 'block';
		}
		// if in gazeDebug mode, make gaze indicator red if on target
		if (gazeDebug){
			if (timeGazeInsideTargetArea != null) { // make gaze circle red
				if( gaze.style.border == '2px solid rgba(255, 255, 255, 0.2)')
					gaze.style.border  = '2px solid rgba(255,  50,  50, 1  )';
			} else { // restore gaze circle to grey
				if( gaze.style.border == '2px solid rgb(255, 50, 50)')
					gaze.style.border  = '2px solid rgba(255, 255, 255, 0.2)';
			}
		}
			
	}
	
	switch (GazeData.state){
		case -1:
			gaze.state="Face tracking lost";
			break;
		case 0:
			gaze.state="Valid gaze data";
			break;
		case 1:
			gaze.state="Gaze uncalibrated";
			break;
		default:
			gaze.state="undefined";
	}
}

// To check if the user has found the target
function taskCompleteCheck(GazeData)
{
 	if (// save the distance from Gaze to Target if the subtask has a target
			   current_subtask.hasOwnProperty('targetX')
			&& current_subtask.hasOwnProperty('targetY')
			&& current_subtask.hasOwnProperty('targetRadius') // only check this if a subtask has a target defined
	) {
		var gazeTargetTime = 2; // default value for target gaze success time
		if (current_subtask.hasOwnProperty('targetGazeTime') ) 
			gazeTargetTime = current_subtask.targetGazeTime;

		// get the distance betweem the user gaze point to the subtask target point
		GazeData.astro.unscaledGazeTargetDist = roundTo(dist2points(
			GazeData.astro.unscaledDocX,
			GazeData.astro.unscaledDocY,
			current_subtask.targetX,
			current_subtask.targetY
		),3);

		// true if first time gaze is on target. stamps time
 		if (
			GazeData.astro.unscaledGazeTargetDist <= current_subtask.targetRadius 
 			&& timeGazeInsideTargetArea == null
		) {
	 		timeGazeInsideTargetArea = GazeData.astro.sessionTime;
			console.log('Gaze on target'); // debug
		}
	 	
	 	// true if gaze has remained on target longer than gaze target time
		// this means the user has met the success criteria for this task
	 	if (
			timeGazeInsideTargetArea != null 
	 		&& (GazeData.astro.sessionTime - timeGazeInsideTargetArea) >= (gazeTargetTime * 1000)
		) {
	 		timeGazeInsideTargetArea = null;
			window.dispatchEvent(targetFoundEvent); // fire the target found event. This will end the current subtask
			window.removeEventListener("tgtFnd", handleTargetFound); // remove listener after target found to prevent multiple dispaching for the same subtask
	 		console.log('Target Found'); // debug
	 	}

	 	// true if gaze moves off target after gaze has been on target
	 	if (
			timeGazeInsideTargetArea != null 
	 		&& GazeData.astro.unscaledGazeTargetDist > current_subtask.targetRadius
		) {
	 		timeGazeInsideTargetArea = null;
			console.log('Gaze off target'); // debug			
		}
	}
}
   
// this is called every time a GazaData message is received from the GazeCloud server
function HandleGazeData(GazeData){

	GazeData.astro = {};
	GazeData.astro.sessionTime = GazeData.time - startTime;
	GazeData.time = null; // anonymise time
	GazeData.astro.devicePixelRatio = window.devicePixelRatio;
	GazeData.astro.imageWidth  = img.width;
	GazeData.astro.imageHeight = img.height;
	GazeData.astro.canvasWidth  = c.width;
	GazeData.astro.canvasHeight = c.height;
	GazeData.astro.hRatio = hRatio;
	GazeData.astro.vRatio = vRatio;
	GazeData.astro.MouseDocX = mouseDocX;
	GazeData.astro.MouseDocY = mouseDocY;
	GazeData.astro.imgScaleRatio = roundTo((imgScaleRatio), 3);
	GazeData.astro.unscaledDocX = roundTo((GazeData.docX / imgScaleRatio), 3);
	GazeData.astro.unscaledDocY = roundTo((GazeData.docY / imgScaleRatio), 3);
	GazeData.astro.unscaledMouseDocX = roundTo((mouseDocX / imgScaleRatio), 3);
	GazeData.astro.unscaledMouseDocY = roundTo((mouseDocY / imgScaleRatio), 3);
	// cross-browser window size
	GazeData.astro.windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	GazeData.astro.windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	// screen resolution
	GazeData.astro.screenWidth = screen.width;
	GazeData.astro.screenHeight = screen.height;

	// we want to know if the Canvas is currently visible
	var canvasDiv = document.getElementById("canvasDiv");

	// only save GazeData if a task and subtask number are defined and the Canvas is visible
	// - this is only true when a task is active
	if (  
		   task_num > -1 
		&& subtask_num > -1
		&& canvasDiv.style.display == "block"
	){	
		taskCompleteCheck(GazeData);
		saveData(GazeData); // send each GazeData point to the MongoDB
	}
		
	PlotGaze(GazeData); // show the gaze position in the browser window
}

//}
// **** end GazeCloud functions


//{ **** taskrunner functions ***

// get the tasks from MongoDB via PHP using AJAX
function getTasks(){
	// begin ajax request
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			try {
				task_data = JSON.parse(this.responseText); // save the task data
			} catch (e) {
				console.log('error with getTasks():',e);
				console.log(this.responseText);
			}
			
		}
	};
	
	// post request to the PHP page, include userIdStr as parameter
	xhttp.open("GET", "getTasks.php?userId="+userIdStr, true);

	xhttp.send();
}

// Will close the test window and direct user to thankyou.php
function completeTest(){
	deactivateFullscreen();
	location.replace("thankyou.php"); // jump to next page
}

// to show the instructions and hide the canvas
function showInstructions(){
	var explanationDiv = document.getElementById("explanationDiv");
	var buttonsDiv = document.getElementById("buttonsDiv");
	var startTaskBtn = document.getElementById("startTask");
	explanationDiv.style.display = "block";
	buttonsDiv.style.display = "block";
	startTaskBtn.style.display = "block";
	
	// restore normal scrollbar behaviour
	document.body.style.overflow = "auto"; 

	clearSelection(); // clear selected text

	var canvasDiv = document.getElementById("canvasDiv");
	canvasDiv.style.display = "none";
}

// to hide the instructions and show the canvas
function hideInstructions(){
	var explanationDiv = document.getElementById("explanationDiv");
	var buttonsDiv = document.getElementById("buttonsDiv");
	var startTaskBtn = document.getElementById("startTask");
	
	explanationDiv.style.display = "none";
	buttonsDiv.style.display = "none";
	startTaskBtn.style.display = "none";
	
	// prevent scrollbars from appearing
	document.body.style.overflow = "hidden"; 

	var canvasDiv = document.getElementById("canvasDiv");
	canvasDiv.style.display = "block";
}

// resize and re-add the image if the browser window is resized
function resizeCanvas(){
	c.width  = window.innerWidth;
	c.height = window.innerHeight;

	// preserve the aspect ratio of the image
	// will fill the browser window width or side, leaving black (body background) in the unused space
	var hRatio = c.width / img.width    ;
	var vRatio = c.height / img.height  ;
	imgScaleRatio  = Math.min ( hRatio, vRatio );

	ctx.drawImage(img,	0, 0, img.width,	img.height,     // source rectangle
						0, 0, img.width*imgScaleRatio, img.height*imgScaleRatio); // destination rectangle
}

// changes the task image
function getImage() {
	img = new Image();
	img.src = task_dir+"/"+current_task.image;
	img.onload = function(){ // after the image is loaded, draw it in the canvas
		resizeCanvas(); // resize the image to fit the current browser window size
	}
}

// changes the heading and instructions html and shows those divs
function showNextSubtaskInstructions(){
	// get the master image for this task - will be hidden intially
	getImage();

	var testHeading = document.getElementById("testHeading");
	var explanationPara = document.getElementById("explanationPara");

	testHeading.innerHTML = current_subtask.heading;
	explanationPara.innerHTML = current_subtask.instructions;

	// show the subtask thumbnail image if it exists
	// otherwise hide img tag
	var subtaskImageTag = document.getElementById("subtask_image");
	if (current_subtask.hasOwnProperty('subtask_image')){
		subtaskImageTag.src = task_dir+'/'+current_subtask.subtask_image;
		subtaskImageTag.style.display = "block";
	} else {
		subtaskImageTag.src = "";
		subtaskImageTag.style.display = "none";
	}

	// show the exmplainDiv, buttons and hide the canvas
	showInstructions(); 
}

// hoisting function names so they can call each other
var tryGetNextTask, tryGetNextSubtask; 

// gets next task if it exists, otherwise exits
tryGetNextTask = function() {
	task_num++;
	if (task_num < task_data.length) {
		current_task = task_data[task_num]; // assign new current_task
		subtask_num = -1;
		tryGetNextSubtask();
	} else
		completeTest();
}

// gets next subtask if it exists, otherwise tryGetNextTask
tryGetNextSubtask = function() {
	subtask_num++;
	if (subtask_num < current_task.subtasks.length){
		current_subtask = current_task.subtasks[subtask_num];  // assign new current_subtask
		showNextSubtaskInstructions(); // subtask execution stops here - next subtask runs when start button is clicked again
	// redo testHeading and explanationPara Here, show explainDiv, show Button, overflow: auto
	} else
		tryGetNextTask();
}

/// BUTTON HANDLERS

// remove the instructions, show the image and start the timer
function startNextSubtask() {
	var timer; // a separate timer per subtask element
	var endSubtask, handleSpacebar, handleTimeout; // hoist function definition so endSubtask can see it
	console.log('start task:',task_num,'subtask:',subtask_num); // debug

	// callback to setTimeout, to spacebar pressed event, and to target found event
	endSubtask = function(){
		console.log('end task:',task_num,'subtask:',subtask_num); // debug
		clearTimeout(timer); // end the timeout for this task
		window.removeEventListener("tgtFnd", handleTargetFound);
		window.removeEventListener("keydown", handleSpacebar);
		timeGazeInsideTargetArea = null;
		if (!gazeDebug && doPlotGaze) doPlotGaze = false; // stop plotting the gaze on screen
		img = new Image();
		resizeCanvas();
		tryGetNextSubtask();
	}

	// change the image if the spacebar is pressed
	handleSpacebar = function(event){
		if (event.defaultPrevented) {
			return; // Do nothing if the event was already processed
		}		
		if (event.key === " ") {
			current_subtask.subtask_result = "skip";
			endSubtask();
		}
		// Cancel the default action to avoid it being handled twice
		event.preventDefault();
	}
	
	// record that the subtask timed out end the task
	handleTimeout = function(){
		current_subtask.subtask_result = "timeout";
		endSubtask();
	}
	
	// record that the user found the target
	handleTargetFound = function(){
		current_subtask.subtask_result = "target_found";
		endSubtask();
	}

	if (!gazeDebug)
		doPlotGaze = current_subtask.doPlotGaze; // set doPlotGaze mode for this subtask
	else
		doPlotGaze = true;
	hideInstructions(); // transition to image showing mode
		
	if (current_subtask.allow_skip) // only add the event listener for the spacebar if the task allows it
		window.addEventListener("keydown", handleSpacebar, false); // false = execute handleSpacebar in bubbling phase

 	if ( // register a listener for the targetFoundEvent if this subtask has a target
			   current_subtask.hasOwnProperty('targetX')
			&& current_subtask.hasOwnProperty('targetY')
			&& current_subtask.hasOwnProperty('targetRadius')
	) window.addEventListener("tgtFnd", handleTargetFound, false);			
		
	timer = setTimeout(handleTimeout, current_subtask.time_limit*1000);
}

function changeToTasks(){
	//	Hide 'Start Eye Calibration' button
	var startCalibrationBtn = document.getElementById("startCalibration");
	startCalibrationBtn.style.display = "none";

	// Show "Start Task" button
	var startTaskBtn = document.getElementById("startTask");
	startTaskBtn.style.display = "block";

	task_num = -1;
	subtask_num = -1;
	tryGetNextTask();
}

// to begin the eye tracking calibration process
function startCalibration() {
	activateFullscreen(document.documentElement); // do this after eye tracking starts - check this on safari
	if (!gazeDebug)
		doPlotGaze = false; // turn off gaze plotting on screen
	else
		doPlotGaze = true;
	startTime = Date.now(); // 

	GazeCloudAPI.StartEyeTracking();
	GazeCloudAPI.SetFps(GazeFPS);
}

//}
//**** end taskrunner functions

function init(){
	// get the user Id embedded in the HTML by PHP.
	userIdStr = document.getElementById("userId").innerHTML;
	getTasks(); // get the task_data from the user record in MongoDB via PHP
	
	c = document.getElementById("myCanvas");
	ctx = c.getContext("2d");

	var startCalibrationBtn = document.getElementById("startCalibration");
	startCalibrationBtn.onclick = startCalibration;
	
	var startTaskBtn = document.getElementById("startTask");
	startTaskBtn.onclick = startNextSubtask;
	
	//////set callbacks/////////
	GazeCloudAPI.OnCalibrationComplete = function(){ console.log('gaze Calibration Complete'); changeToTasks();};
	GazeCloudAPI.OnCamDenied = function(){ console.log('camera  access denied')  }
	GazeCloudAPI.OnError =  function(msg){ console.log('err: ' + msg)  }
	GazeCloudAPI.UseClickRecalibration = true;
	GazeCloudAPI.OnResult = HandleGazeData;
}

window.onresize = resizeCanvas; // resize the canvas whenever the browser window is resized
window.onmousemove = setMouseCoords; // record mouse coordinates

window.onload = init;
