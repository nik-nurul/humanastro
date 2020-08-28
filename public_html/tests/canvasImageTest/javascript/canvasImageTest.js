"use strict";

//
//{ **** GazeCloud global vars ****

// Init GazeData Array
var GazeDataArray = [];
//console.log(GazeDataArray);

// new AJAX object
var xhttp = new XMLHttpRequest();

var userIdStr;  // get user ID string from PHP

var startTime = Date.now();

var mouseDocX, mouseDocY, mouseScreenX, mouseScreenY;

//} **** end GazeCloud global vars ****

//{ **** Canvas test global vars ***

// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

// these image names could be gotten from the MongoDB
// or the images themselves could be stored there, in base64 text format
var imgList = [
	"0000-000",
	"0480-270",
	"0480-540",
	"0480-810",
	"0960-270",
	"0960-540",
	"0960-810",
	"1440-270",
	"1440-540",
	"1440-810",
	"1920-1080",
	"starfield1_1920x1080",
	"starfield2_1280x0649"
];

var i = 7; // global current pointer to image URL
var c, ctx, img; // canvas, canvas-context, image vars
var imgScaleRatio; // scale ratio of original image to displayed image in canvas
img = new Image(); // initialise image var with a blank image

//} **** end Canvas Test Global Vars


//{ **** GazeCloud functions ****

function setMouseCoords(event){
	mouseDocX = event.clientX;
	mouseDocY = event.clientY;
	mouseScreenX = event.screenX;
	mouseScreenY = event.screenY;
//	console.log(event); // debug
	document.getElementById("imageName").innerHTML = "image name: " + imgList[i-1];
	document.getElementById("windowPixelRatio").innerHTML = "window Pixel Ratio %: " + Math.floor(parseFloat(window.devicePixelRatio*100));
	document.getElementById("CanvasScale").innerHTML = "Canvas Scale %: " + Math.floor(parseFloat(imgScaleRatio*100));
	document.getElementById("innerWidth").innerHTML = "innerWidth: " + Math.floor(parseFloat(window.innerWidth));
	document.getElementById("innerHeight").innerHTML = "innerHeight: " + Math.floor(parseFloat(window.innerHeight));
	document.getElementById("AbsInnerWidth").innerHTML = "Absolute innerWidth: " + Math.floor(parseFloat(window.innerWidth*window.devicePixelRatio));
	document.getElementById("AbsInnerHeight").innerHTML = "Absolute innerHeight: " + Math.floor(parseFloat(window.innerHeight*window.devicePixelRatio));
	document.getElementById("MouseDocX").innerHTML = "Mouse docX: " + Math.floor(parseFloat(mouseDocX));
	document.getElementById("MouseDocY").innerHTML = "Mouse docY: " + Math.floor(parseFloat(mouseDocY));
	document.getElementById("MouseAbsDocX").innerHTML = "Mouse absolute docX: " + Math.floor(parseFloat(mouseDocX*window.devicePixelRatio));
	document.getElementById("MouseAbsDocY").innerHTML = "Mouse absolute docY: " + Math.floor(parseFloat(mouseDocY*window.devicePixelRatio));
	document.getElementById("MouseScaledDocX").innerHTML = "Mouse scaled docX: " + Math.floor(parseFloat(mouseDocX/imgScaleRatio));
	document.getElementById("MouseScaledDocY").innerHTML = "Mouse scaled docY: " + Math.floor(parseFloat(mouseDocY/imgScaleRatio));
	//document.getElementById("MouseScreenX").innerHTML = "Mouse screenX: " + Math.floor(parseFloat(mouseScreenX));
	//document.getElementById("MouseScreenY").innerHTML = "Mouse screenY: " + Math.floor(parseFloat(mouseScreenY));
}

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

	var sessionTime = (parseInt(GazeData.time) - parseInt(startTime));

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
	
	document.getElementById("StartTime").innerHTML = "Start Time: " + startTime;
	document.getElementById("TimeData").innerHTML = "Time: " + GazeData.time;
	document.getElementById("SessionTime").innerHTML = "Session Time: " + GazeData.time;
	document.getElementById("Frame").innerHTML = "Frame: " + GazeData.FrameNr;
	document.getElementById("PagePlotX").innerHTML = "Plot X: " + Math.floor(parseFloat(gaze.style.left));
	document.getElementById("PagePlotY").innerHTML = "Plot Y: " + Math.floor(parseFloat(gaze.style.top));
	document.getElementById("Calibration").innerHTML = "Calibration:  " + gaze.state;
	document.getElementById("GazeDataDocX").innerHTML = "Gaze docX: " + Math.floor(parseFloat(GazeData.docX));
	document.getElementById("GazeDataDocY").innerHTML = "Gaze docY: " + Math.floor(parseFloat(GazeData.docY));
	document.getElementById("GazeDataScaledDocX").innerHTML = "Gaze scaled docX: " + Math.floor(parseFloat(GazeData.docX/imgScaleRatio));
	document.getElementById("GazeDataScaledDocY").innerHTML = "Gaze scaled docY: " + Math.floor(parseFloat(GazeData.docY/imgScaleRatio));
//	document.getElementById("GazeDataX").innerHTML = "Gaze Screen X: " + Math.floor(parseFloat(GazeData.GazeX));
//	document.getElementById("GazeDataY").innerHTML = "Gaze Screen Y: " + Math.floor(parseFloat(GazeData.GazeY));
//	document.getElementById("HeadPhoseData").innerHTML = " HeadX: " + GazeData.HeadX + " HeadY: " + GazeData.HeadY + " HeadZ: " + GazeData.HeadZ;
//	document.getElementById("HeadRotData").innerHTML = " Yaw: " + GazeData.HeadYaw + " Pitch: " + GazeData.HeadPitch + " Roll: " + GazeData.HeadRoll;

	
}

//////set GazeCloud callbacks/////////
GazeCloudAPI.OnCalibrationComplete =function(){ console.log('gaze Calibration Complete')  }
GazeCloudAPI.OnCamDenied =  function(){ console.log('camera  access denied')  }
GazeCloudAPI.OnError =  function(msg){ console.log('err: ' + msg)  }
GazeCloudAPI.UseClickRecalibration = true;
GazeCloudAPI.OnResult = PlotGaze; 

window.onmousemove = setMouseCoords;
console.log('setMouseCoords here');

//} **** end GazeCloud code

//{ **** Canvas Test Functions

// cycle through the image URLs - call a new image each time this is called
function getNextImgUrl(){
	if (i>=imgList.length) i=0;
	var imgUrl = 'images/Calibration-'+imgList[i++]+'.png';
	console.log(imgUrl); // debug
	return imgUrl;
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


// perform action when button is clicked
// this could be called when a timer expires or on other events
function doIt() {
	img = new Image();
	img.src = getNextImgUrl(); // every time this is called, a new image is loaded -- no need for ajax (yet)!
	img.onload = function(){ // after the image is loaded, draw it in the canvas
		resizeCanvas(); // resize the image to fit the current browser window size
	}
};

function init(){
	c = document.getElementById("myCanvas");
	ctx = c.getContext("2d");
	resizeCanvas();
	
	userIdStr = document.getElementById("userId").innerHTML;
	console.log('userIdStr:',userIdStr); // debug

    /*If the user clicks the 'changeContent' button*/
    var changeContent = document.getElementById("changeContent");
    changeContent.onclick = doIt;
	
	// start and stop eye tracking
	var startEyeTracking = document.getElementById("startEyeTracking");
	var stopEyeTracking = document.getElementById("stopEyeTracking");
	startEyeTracking.onclick = GazeCloudAPI.StartEyeTracking;
	stopEyeTracking.onclick = GazeCloudAPI.stopEyeTracking;

}

window.onload = init;
window.onresize = resizeCanvas; // resize the canvas whenever the browser window is resized
/*ow.addEventListener("keydown", function(event){ // change the image if the spacebar is pressed
		console.log('spacebar pressed'); // debug
		if (event.defaultPrevented) {
			return; // Do nothing if the event was already processed
		}		
		if (event.key === " ") doIt();
		// Cancel the default action to avoid it being handled twice
		event.preventDefault();
	}, true
);
*/
//} **** end Canvas Test code