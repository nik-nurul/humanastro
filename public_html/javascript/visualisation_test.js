"use strict";

// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

// these image names could be gotten from the MongoDB
// or the images themselves could be stored there, in base64 text format
var imgList = [
	"starfield1_1920x1080",
	"starfield2_1280x0649",
	"starfield3_504x284"
];

var i = 0; // global current pointer to image URL
var c, ctx, img; // canvas, canvas-context, image vars
img = new Image(); // initialise image var with a blank image


// cycle through the image URLs - call a new image each time this is called
function getNextImgUrl(){
	if (i>=imgList.length) i=0; //will return back to i=0 if it reaches the end of array
	var imgUrl = '/javascript/spaceImages/Calibration-'+imgList[i++]+'.png';
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
	var ratio  = Math.min ( hRatio, vRatio );

	ctx.drawImage(img,	0, 0, img.width,	img.height,     // source rectangle
						0, 0, img.width*ratio, img.height*ratio); // destination rectangle

	console.log('new Canvas width:', c.width); // debug
	console.log('new Canvas height:', c.height); // debug
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

function callFunctions(){
		doIt();
		changeSection();
}


function init(){
		 c = document.getElementById("myCanvas");
		 ctx = c.getContext("2d");
		 resizeCanvas();


    /*If the user clicks the 'changeContent' button*/
     var changeContent = document.getElementById("startTutorial");
     changeContent.onclick = callFunctions;
}

window.onload = init;
window.onresize = resizeCanvas; // resize the canvas whenever the browser window is resized
window.addEventListener("keydown", function(event){ // change the image if the spacebar is pressed
		console.log('key pressed'); // debug
		if (event.defaultPrevented) {
			return; // Do nothing if the event was already processed
		}
		if (event.key === " ") doIt();
		// Cancel the default action to avoid it being handled twice
		event.preventDefault();
	}, true
);
