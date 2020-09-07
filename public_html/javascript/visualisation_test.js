"use strict";

// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

//{ **** GazeCloud global vars ****

// Gaze Calibration type: 0 is accurate calibration (much slower - default); 1 is fast calibration
GazeCloudAPI.CalibrationType = 1;
var maxGazaDataArraySize = 30; // save arrays of this size in browser memory before sending to MongoDB
var GazeFPS = 30; // Webcam FPS rate for GazeCloud

var doPlotGaze = false; // if true, plot the gaze on screen
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
//var tasks = []; // the current set of tasks - tutorialTasks or realTasks

// task metadata will eventually be held in the MongoDB

var calibrationTasks;
var tutorialTasks;
var realTasks;

var c, ctx, img; // canvas, canvas-context, image vars
img = new Image(); // initialise image var with a blank image
var imgScaleRatio; // scale ratio of original image to displayed image in canvas
var hRatio, vRatio; // scale ratio of horizontal and vertical dimensions of image vs canvas
var task; // the current task_data element
var task_num = -1;
var subtask_num = -1; // the current task and subtask numbers

//}
// **** end taskrunner global vars


//{ **** GazeCloud functions ****

function setMouseCoords(event){
	mouseDocX = event.clientX;
	mouseDocY = event.clientY;
	mouseScreenX = event.screenX;
	mouseScreenY = event.screenY;
}

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
//	console.log('task_num',task_num);

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
//	console.log('.'); // debug
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

	if (doPlotGaze){
		gaze.style.left = x + "px";
		gaze.style.top = y + "px";
	}
	
	if(GazeData.state != 0 || !doPlotGaze ){
		if( gaze.style.display  == 'block')
			gaze.style.display   = 'none';
	} else {
		if( gaze.style.display  == 'none'){
			gaze.style.display   = 'block';
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

function dist2points(x1,y1,x2,y2){
	return Math.hypot(x2-x1, y2-y1);
}

// this is called every time a GazaData message is received from the GazeCloud server
function HandleGazeData(GazeData){
	
	GazeData.astro = {};
	GazeData.astro.sessionTime = GazeData.time - startTime; // anonymise time
	GazeData.astro.devicePixelRatio = window.devicePixelRatio;
	GazeData.astro.imgWidth  = img.width;
	console.log(img.width);
	GazeData.astro.imgHeight = img.height;
	GazeData.astro.canvasWidth  = c.width;
	GazeData.astro.canvasHeight = c.height;
	GazeData.astro.hRatio = hRatio;
	GazeData.astro.vRatio = vRatio;
	GazeData.astro.MouseDocX = mouseDocX;
	GazeData.astro.MouseDocY = mouseDocY;
	GazeData.astro.imgScaleRatio = imgScaleRatio;
	GazeData.astro.unscaledDocX = GazeData.docX/imgScaleRatio;
	GazeData.astro.unscaledDocY = GazeData.docY/imgScaleRatio;
	GazeData.astro.unscaledMouseDocX = mouseDocX/imgScaleRatio;
	GazeData.astro.unscaledMouseDocY = mouseDocY/imgScaleRatio;

	if (task_num > 0 && subtask_num > 0){ // only save GazaData of a task and subtask number are defined
		if (// save the distance from Gaze to Target if the subtask has a target
			   task.subtasks[subtask_num-1].hasOwnProperty('targetX') // change subtask ref
			&& task.subtasks[subtask_num-1].hasOwnProperty('targetY')
		){
			GazeData.astro.unscaledGazeTargetDist = dist2points(
				GazeData.astro.unscaledDocX,
				GazeData.astro.unscaledDocY,
				task.subtasks[subtask_num-1].targetX,
				task.subtasks[subtask_num-1].targetY
			);
			if (GazeData.astro.unscaledGazeTargetDist <  task.subtasks[subtask_num-1].targetRadius)
				console.log('GazeData.astro.unscaledGazeTargetDist:',GazeData.astro.unscaledGazeTargetDist); // debug
		}
		saveData(GazeData); // send each GazeData point to the MongoDB
	}
	PlotGaze(GazeData); // show the gaze position in the browser window

}

//////set callbacks/////////
GazeCloudAPI.OnCalibrationComplete = function(){RemoveHeatMap();; console.log('gaze Calibration Complete')  }
GazeCloudAPI.OnCamDenied = function(){ console.log('camera  access denied')  }
GazeCloudAPI.OnError =  function(msg){ console.log('err: ' + msg)  }
GazeCloudAPI.UseClickRecalibration = true;
GazeCloudAPI.OnResult = HandleGazeData;
window.onmousemove = setMouseCoords;

//}
// **** end GazeCloud functions


//{ **** taskrunner functions ***

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

// Will close the test window and direct user to thankyou.php
function completeTest(){
	deactivateFullscreen();
	location.replace("thankyou.php"); // jump to next page
}

// to control which section should be shown and which should be hidden
function changeSection(){
	// Show and hide explanation and buttons section
	 var explanationSect = document.getElementById("explanationDiv");
	 var buttonsDiv = document.getElementById("buttonsDiv");
	 if(explanationSect.style.display == "block"){
			 explanationSect.style.display = "none";
			 buttonsDiv.style.display = "none";
	 } else if (explanationSect.style.display == "none"){
		 	explanationSect.style.display = "block";
		 	buttonsDiv.style.display = "block";
	 }

  // Show and hide the images within the canvas section 
	 var canvasSect = document.getElementById("canvasDiv")
	 if(canvasSect.style.display == 'none'){
		canvasSect.style.display = 'block';
	 } else if(canvasSect.style.display == "block"){
		canvasSect.style.display = "none";
	 }

	 //	Hide 'Take Tutorial Test' button
	 var tutorBttn = document.getElementById("startTutorial");
	 tutorBttn.style.display = "none";
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
function getNextImage(task) {
	img = new Image();
	img.src = task_dir+"/"+task.image;
//	console.log("img.src:",img.src); // debug
	img.onload = function(){ // after the image is loaded, draw it in the canvas
		resizeCanvas(); // resize the image to fit the current browser window size
	}
};

// clears the image in the canvas
function endTasks() {
	img = new Image();
	resizeCanvas();
}

// tasks is the array of task data objects
// i is the index number in the array of task data objects
// afterTasksFunction is the function to execute after the last task is shown
function showEachTask(tasks, i, afterTasksFunction) {
	var timer; // a separate timer per subtask element
	var handleSpacebar; // hoist function definition so showNextTask can see it

	// callback to setTimeout and to spacebar pressed event
	var showNextTask = function(tasks, i, afterTasksFunction){
		window.removeEventListener("keydown", handleSpacebar);
		if (i < tasks.length) {
			showEachTask(tasks, i, afterTasksFunction);          
		} else {
			endTasks();
			afterTasksFunction(); // execute the callback function
		}		
	}

	// change the image if the spacebar is pressed
	handleSpacebar = function(event){
		if (event.defaultPrevented) {
			return; // Do nothing if the event was already processed
		}		
		if (event.key === " ") {
			window.removeEventListener("keydown", handleSpacebar); // remove the event listener for this task
	 		clearTimeout(timer); // end the timeout for this task
			showNextTask(tasks, i, afterTasksFunction); // jump to the next task
		}
		// Cancel the default action to avoid it being handled twice
		event.preventDefault();
	}

	task = tasks[i++];	// assign task element and increment counter
	task_num = task.task_num;
	subtask_num = 1;		// eventually will iterate through subtasks here (need to implement separate timers per subtask)
	getNextImage(task);		// display the image for this task
	if (task.subtasks[subtask_num-1].allow_skip) // only add the event listener for the spacebar if the task allows it
		window.addEventListener("keydown", handleSpacebar, false); // false = execute handleSpacebar in bubbling phase
	timer = setTimeout(showNextTask, task.subtasks[subtask_num-1].time_limit*1000, tasks, i, afterTasksFunction);
}

// tasks is the array of task data objects
// afterTasksFunction is the function to execute after the last task is shown
function showTasks(tasks, afterTasksFunction){
	// iterate through tasks array
	// this will eventually iterate through user / task_data in MongoDB
//	console.log('showTasks() - tasks:',tasks); // debug

	var i = 0;
	showEachTask(tasks, i, afterTasksFunction);
}

// get the tasks from MongoDB via PHP using AJAX
function getTasks(){
	// begin ajax request
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			task_data = JSON.parse(this.responseText); // save the task data
//			console.log('task_data:',task_data); // debug
			// split the task_data into separate arrays
			calibrationTasks	= task_data.filter(task => (task.phase === '1-refCal'));
			tutorialTasks		= task_data.filter(task => (task.phase === '2-tutorial'));
			realTasks			= task_data.filter(task => (task.phase === '3-task'));
//			console.log('realTasks:',realTasks); // debug
			
		}
	};
	
	// post request to the PHP page, include userIdStr as parameter
	xhttp.open("GET", "getTasks.php?userId="+userIdStr, true);

//	// the data type in the POST data to JSON
//	xhttp.setRequestHeader("Content-type", "application/json");

	xhttp.send();
}

/// BUTTON HANDLERS

// for real test
function startRealTest(){
	doPlotGaze = false; // turn of gaze plotting on screen
	// hide the "Take Real Test" button 
	var realTestBttn = document.getElementById("startReal");
	realTestBttn.style.display = "none";

	changeSection();
	showTasks(realTasks, completeTest); // shows the real tasks then runs completeTest
}

// changes the page content to ask the user to take the real test
function changeToRealTest(){
	doPlotGaze = false; // turn off gaze plotting on screen

	// Change the text for the heading
	var testHead = document.getElementById("testHeading");
	testHead.innerHTML = "Real Test";

	// Change the text for explanation paragraph
	var explainPara = document.getElementById("explanationPara");
	explainPara.innerHTML = "Congratulations on finishing the tutorial test! Now click the 'Take Real Test' button to proceed.";

	// Show Take Real Test button
	var realTestBttn = document.getElementById("startReal");
	realTestBttn.style.display = "block";
	//Change the status of real test variable(to med until start real test bttn is clicked) to stop timer from continue looping when spacebar is pressed
	realTestBttn.onclick = startRealTest; //call function to slide through images and change content
	
	changeSection();
}

// for tutorial test
function startTutorial(){
	changeSection();
	// shows the tutorial tasks then runs changeToRealTest
	showTasks(tutorialTasks, changeToRealTest); 
}

/* Used only when 'Refine Calibration' button is clicked */
function changeToTutorial(){
	doPlotGaze = false; // turn off gaze plotting on screen
	changeSection();
	/* Change the text for the heading */
	var changeHead = document.getElementById("testHeading");
	changeHead.innerHTML = "Tutorial Test";

	/* Change the text for explanation paragraph*/
	var explainPara = document.getElementById("explanationPara");
	explainPara.innerHTML = "Congratulations on finishing the eye calibration! There will be a tutorial test before the real test take place <br/>"+
		"Below is the instructions that need to be followed to complete the test successfully:" +
		"<div id='explanationBullet'><p><ul class='bullet_style paragraph_font'>"+
		"<li>There will be a series of images that will be presented</li>"+
		"<li>Please stare at the images and find similar patterns</li>"+
		"<li>Each image will have its own timer</li>"+
		"<li>The timer wil start as soon as you click 'Take Test'</li>" +
		"<li>There will be 3 images for the tutorial test</li>"+
		"<li>There will be 6 images for the real test</li></ul></p></div>";

	/*	Hide 'Refine Calibration' button*/
	var caliBttn = document.getElementById("startRefineCal");
	caliBttn.style.display = "none";

	/* Show "Take Tutorial Test" button */
	var tuteBttn = document.getElementById("startTutorial");
	tuteBttn.style.display = "block";
}

// for calibration refinement
function startRefineCal() {
	doPlotGaze = true; // turn on gaze plotting on screen
	changeSection();
	showTasks(calibrationTasks, changeToTutorial); 
}

/* Used only when 'calibration test' button is clicked */
function changeToRefineCal(){
	/* Change the text for the heading */
	var changeHead = document.getElementById("testHeading");
	changeHead.innerHTML = "Refine Calibration";

	/* Change the text for explanation paragraph*/
	var explainPara = document.getElementById("explanationPara");
	explainPara.innerHTML = "Basic calibration is complete. Click Refine Calibration to continue<br/>"+
		"<div id='explanationBullet'><p><ul class='bullet_style paragraph_font'>"+
		"<li>Your 'gaze' will be shown on the screen as a cirle</li>"+
		"<li>Place the mouse cursor on any of the dots and stare at that location on screen</li>"+
		"<li>Click that location with the mouse multiple times to update the position of the gaze circle</li>"+
		"<li>Repeat this process at different points across the screen</li>" +
		"<li>When you are satisfied the gaze indicator is accurate, press the space bar to continue</li></ul></p></div>";

	/*	Hide 'Start Eye Calibration' button*/
	var caliBttn = document.getElementById("startCalibration");
	caliBttn.style.display = "none";

	/* Show "Refine Calibration" button */
	var tuteBttn = document.getElementById("startRefineCal");
	tuteBttn.style.display = "block";
}

// to begin the eye tracking calibration process
function startCalibration() {
	doPlotGaze = false; // turn off gaze plotting on screen
	startTime = Date.now();
	activateFullscreen(document.documentElement);

	GazeCloudAPI.StartEyeTracking();
	GazeCloudAPI.SetFps(GazeFPS);
	changeToRefineCal();
}

//}
//**** end taskrunner functions

function init(){
	c = document.getElementById("myCanvas");
	ctx = c.getContext("2d");

	userIdStr = document.getElementById("userId").innerHTML;
	getTasks(); // get the task_data from the user record in MongoDB via PHP

	var startCalibrationBtn = document.getElementById("startCalibration");
	startCalibrationBtn.onclick = startCalibration;
	
	var startRefineCalBtn = document.getElementById("startRefineCal");
	startRefineCalBtn.onclick = startRefineCal;
	
	var startTutorialBtn = document.getElementById("startTutorial");
	startTutorialBtn.onclick = startTutorial;
	
}

window.onload = init;
window.onresize = resizeCanvas; // resize the canvas whenever the browser window is resized
window.onmousemove = setMouseCoords; // record mouse coordinates
