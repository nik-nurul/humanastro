"use strict";

// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

//{ **** GazeCloud global vars ****

// Init GazeData Array
var GazeDataArray = [];

// new AJAX object
var xhttp = new XMLHttpRequest();

var userIdStr;  // get user ID string from PHP

var startTime = Date.now();

var mouseDocX, mouseDocY, mouseScreenX, mouseScreenY;

//}
// **** end GazeCloud global vars ****


//{ **** taskrunner global vars ***

// these image names could be gotten from the MongoDB
// or the images themselves could be stored there, in base64 text format

var task_dir = "tasks";
//var tasks = []; // the current set of tasks - tutorialTasks or realTasks

// task metadata will eventually be held in the MongoDB
var calibrationTasks = [
	{
		"image": "Calibration-0000-000.png",
		"time": 1.001,
		"allow_skip": true
	},
	{
		"image": "Calibration-0480-270.png",
		"time": 1.002,
		"allow_skip": true
	},
	{
		"image": "Calibration-0480-540.png",
		"time": 1.003,
		"allow_skip": true
	},
	{
		"image": "Calibration-0480-810.png",
		"time": 1.004,
		"allow_skip": true
	},
	{
		"image": "Calibration-0960-270.png",
		"time": 1.005,
		"allow_skip": true
	},
	{
		"image": "Calibration-0960-540.png",
		"time": 1.006,
		"allow_skip": true
	},
	{
		"image": "Calibration-0960-810.png",
		"time": 1.007,
		"allow_skip": true
	},
	{
		"image": "Calibration-1440-270.png",
		"time": 1.008,
		"allow_skip": true
	},
	{
		"image": "Calibration-1440-540.png",
		"time": 1.009,
		"allow_skip": true
	},
	{
		"image": "Calibration-1440-810.png",
		"time": 1.010,
		"allow_skip": true
	},
	{
		"image": "Calibration-1920-1080.png",
		"time": 2,
		"allow_skip": true
	}
];

var tutorialTasks = [
	{
		"image": "Tutorial-starfield1_1920x1080.png",
		"time": 5,
		"allow_skip": true
	},
	{
		"image": "Tutorial-starfield2_1280x0649.png",
		"time": 5,
		"allow_skip": true
	},
	{
		"image": "Tutorial-starfield3_504x284.png",
		"time": 5,
		"allow_skip": true
	}
];

var realTasks = [
	{
		"image": "RealTest-space1_1280x720.png",
		"time": 5,
		"allow_skip": true
	},
	{
		"image": "RealTest-space2_1920x1080.png",
		"time": 5,
		"allow_skip": true
	},
	{
		"image": "RealTest-space3_1920x1080.png",
		"time": 5,
		"allow_skip": true
	},
	{
		"image": "RealTest-space4_1920x1080.png",
		"time": 5,
		"allow_skip": true
	},
	{
		"image": "RealTest-space5_4096x2160.png",
		"time": 5,
		"allow_skip": true
	},
	{
		"image": "RealTest-space6_1920x1108.png",
		"time": 5,
		"allow_skip": true
	}
];

var c, ctx, img; // canvas, canvas-context, image vars
img = new Image(); // initialise image var with a blank image
var imgScaleRatio; // scale ratio of original image to displayed image in canvas
var spacebarPressed = false;

//}
// **** end taskrunner global vars


//{ **** GazeCloud functions ****
//}
// **** end GazeCloud functions


//{ **** taskrunner functions ***

// Will close the test window and direct user to thankyou.php
function completeTest(){
	location.replace("https://humanastro.csproject.org/thankyou.php"); // jump to next page
}

// to control which section should be shown and which should be hidden
function changeSection(){
	// Show and hide explanation section
	 var explanationSect = document.getElementById("explanationDiv");
	 if(explanationSect.style.display == "block"){
			 explanationSect.style.display = "none";
	 } else if (explanationSect.style.display == "none"){
		 	explanationSect.style.display = "block";
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

// changes the page content to ask the user to take the real test
function changeToRealTest(){
	// Change the text for the heading
	var testHead = document.getElementById("testHeading");
	testHead.innerHTML = "Real Test";

	// Change the text for explanation paragraph
	var explainPara = document.getElementById("explanationPara");
	explainPara.innerHTML = "Congratulations on finishing the tutorial test! Now click the 'Take Real Test' button to proceed.";

	// Hide the bullet section from previous
	//var explainBullet = document.getElementById("explanationBullet");
	//explainBullet.style.display = "none";
	
	// Show Take Real Test button
	var realTestBttn = document.getElementById("startReal");
	realTestBttn.style.display = "block";
	//Change the status of real test variable(to med until start real test bttn is clicked) to stop timer from continue looping when spacebar is pressed
	realTestBttn.onclick = startRealTest; //call function to slide through images and change content
	
	changeSection();
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

	console.log('new Canvas width:', c.width); // debug
	console.log('new Canvas height:', c.height); // debug
	console.log('Canvas scale ratio %:', Math.floor(parseFloat(imgScaleRatio*100))); // debug
}

// changes the task image
function getNextImage(task) {
	img = new Image();
	img.src = task_dir+"/"+task.image; 
	console.log("img.src:",img.src);
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
	var timer; // a separate timer per task element
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

	var task = tasks[i++]; // assign task element and increment counter
	getNextImage(task);		// display the image for this task
	window.addEventListener("keydown", handleSpacebar, false); // false = execute handleSpacebar in bubbling phase
	timer = setTimeout(showNextTask, task.time*1000, tasks, i, afterTasksFunction);
}

// tasks is the array of task data objects
// afterTasksFunction is the function to execute after the last task is shown
function showTasks(tasks, afterTasksFunction){
	// iterate through tasks array
	// this will eventually iterate through user / task_data in MongoDB
	var i = 0;
	showEachTask(tasks, i, afterTasksFunction);
}

// for tutorial test
function startTutorial(){
	changeSection();
	// shows the tutorial tasks then runs changeToRealTest
	showTasks(tutorialTasks, changeToRealTest); 
}

// for real test
function startRealTest(){
	// hide the "Take Real Test" button 
	var realTestBttn = document.getElementById("startReal");
	realTestBttn.style.display = "none";

	changeSection();
	showTasks(realTasks, completeTest); // shows the real tasks then runs completeTest
}


function init(){
	c = document.getElementById("myCanvas");
	ctx = c.getContext("2d");

// for when we add userId to take_test.php	
//	userIdStr = document.getElementById("userId").innerHTML;
//	console.log('userIdStr:',userIdStr); // debug

	var changeContent = document.getElementById("startTutorial");
	changeContent.onclick = startTutorial;
	
}

//}
//**** end taskrunner functions

window.onload = init;
window.onresize = resizeCanvas; // resize the canvas whenever the browser window is resized
