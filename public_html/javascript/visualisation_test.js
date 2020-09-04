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

//var i = 0; // global current pointer to image URL
var c, ctx, img; // canvas, canvas-context, image vars
img = new Image(); // initialise image var with a blank image
var realTestStatus = "no";
//var timer_ms = 5000; //default global timeout value (in ms)
//var timer; // timeout object;
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
	console.log('completeTest') // debug
//	location.replace("https://humanastro.csproject.org/thankyou.php"); // jump to next page
}

// to control which section should be shown and which should be hidden
function changeSection(){
	console.log('changeSection') // debug
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
		console.log('showing canvas'); // debug
		canvasSect.style.display = 'block';
	 } else if(canvasSect.style.display == "block"){
		console.log('hiding canvas'); // debug
		canvasSect.style.display = "none";
	 }

	 //	Hide 'Take Tutorial Test' button
	 var tutorBttn = document.getElementById("startTutorial");
	 tutorBttn.style.display = "none";
}

// changes the page content to ask the user to take the real test
function changeToRealTest(){
	console.log('changeToRealTest'); // debug
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
	realTestStatus = "med";
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

// callback to setTimeout and to spacebarPressed event
function showNextTask(tasks, i, afterTasksFunction){
	spacebarPressed = false;
	if (i < tasks.length) {
		showEachTask(tasks, i, afterTasksFunction);          
	} else {
		endTasks();
		afterTasksFunction(); // execute the callback function
	}		
}

// tasks is the array of task data objects
// i is the index number in the array of task data objects
// afterTasksFunction is the function to execute after the last task is shown
function showEachTask(tasks, i, afterTasksFunction) {

	var task = tasks[i++];
	console.log('task:',task);
	getNextImage(task);
	timer = setTimeout(showNextTask, task.time*1000, tasks, i, afterTasksFunction);
	if (spacebarPressed){
		console.log('showEachTask() - spacebarPressed:',spacebarPressed);
		showNextTask(tasks, i, afterTasksFunction); // jump to the next task
	}
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
//	showTasks(calibrationTasks, changeToRealTest);
}

// for real test
function startRealTest(){
	// hide the "Take Real Test" button 
	var realTestBttn = document.getElementById("startReal");
	realTestBttn.style.display = "none";

	// call related functions
	realTestStatus = "yes";
	
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


// This will change the image if the spacebar is pressed
// It does this by clearing the timeout on the timer
window.addEventListener("keydown", function(event){ // change the image if the spacebar is pressed
		console.log('key pressed'); // debug
		if (event.defaultPrevented) {
			return; // Do nothing if the event was already processed
		}		
		if (event.key === " ") {
//			clearTimeout(timer);
			spacebarPressed = true;
		}
		// Cancel the default action to avoid it being handled twice
		event.preventDefault();
	}, true
);



/***** OLD TASKRUNNER VERSION
// used to get imgUrl from second array of images (for tutorial test)
function getNextTutorialImgUrl(){
		 if (i<tutorialTasks.length ) {
			 console.log(i);
		   console.log(tutorialTasks[i]); // debug
			 	var imgUrl = "/"+task_dir+"/"+tutorialTasks[i++];
				console.log(imgUrl); // debug
				return imgUrl;
		} else if (i>=tutorialTasks.length) {
				// the tutorial is finished - reset for real test
				i=0
				changeToRealTest();
				changeSection(); //will change section back to get ready for the real test
		};
}

// used to get imgUrl from second array of images (for real test)
function getNextRealTestImgUrl(){
		 if (i<realTasks.length ) {
			 console.log(i);
		   console.log(realTasks[i]); // debug
			 	var imgUrl = "/"+task_dir+"/"+realTasks[i++];
				console.log(imgUrl); // debug
				return imgUrl;
		} else if (i>=realTasks.length) {
				completeTest(); //call this function once completed
		};
}

// perform action when button is clicked
// this could be called when a timer expires or on other events
function getNextTutorialImage() {
//		img = new Image();
		img.src = getNextTutorialImgUrl(); // every time this is called, a new image is loaded -- no need for ajax (yet)!
		img.onload = function(){ // after the image is loaded, draw it in the canvas
			resizeCanvas(); // resize the image to fit the current browser window size
		}
};

// To iterate through the second array of images (for real test) 
function getNextRealTestImage() {
//		img = new Image();
		img.src = getNextRealTestImgUrl(); // every time this is called, a new image is loaded -- no need for ajax (yet)!
		img.onload = function(){ // after the image is loaded, draw it in the canvas
			resizeCanvas(); // resize the image to fit the current browser window size
		}
};


//function for controlling image changes using timer for tutorial test
function setTutorialTimer(){
	if (realTestStatus == "no" && i < tutorialTasks.length){
		getNextTutorialImage();
		console.log("5 seconds timer started for tutorial test");
		window.setTimeout(function(){
			//loop the function as long as the last image in the tutorialTasks(tutorial images) is not loaded
			setTutorialTimer();
		}, timer);
		//debug timer loop count
		console.log(realTestStatus+","+i);
	} 	
	else{
		//debug exit loop
		console.log("Timer loop exited")
		//stop loop and proceed
		getNextTutorialImage();
	} 
};

//function for controlling image changes using timer for tutorial test
function setRealTestTimer(){
	if (realTestStatus == "yes" && i < realTasks.length){
		getNextRealTestImage();
		console.log("5 seconds timer started for real test");
		window.setTimeout(function(){
			//loop the function as long as the last image in the realTasks(real images) is not loaded
			setRealTestTimer();
		}, timer);
		//debug timer loop count
		console.log(realTestStatus+","+i);
	} 	
	else{
		//debug exit loop
		console.log("RealTestTimer loop exited")
		//stop loop and proceed
		getNextRealTestImage();
	} 
};

window.onload = init;
window.onresize = resizeCanvas; // resize the canvas whenever the browser window is resized


//This will change the image if the spacebar is pressed
window.addEventListener("keydown", function(event){
		console.log('key pressed'); // debug
		if (event.defaultPrevented) {
			return; // Do nothing if the event was already processed
		}
		if (event.key === " " && realTestStatus == "no"){
				getNextTutorialImage();
				timer = 5000;//reset timer back to 5 seconds everytime spacebar is pressed
		} else if (event.key === " " && realTestStatus == "yes") {
				getNextRealTestImage();
				timer = 5000;//reset timer back to 5 seconds everytime spacebar is pressed
				console.log(realTestStatus); //for bug
		}
		// Cancel the default action to avoid it being handled twice
		event.preventDefault();
	}, true
);


***** END OLD VERSION */
