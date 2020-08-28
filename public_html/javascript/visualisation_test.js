"use strict";

// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

// these image names could be gotten from the MongoDB
// or the images themselves could be stored there, in base64 text format
var imgList = [
	"starfield1_1920x1080",
	"starfield2_1280x0649",
	"starfield3_504x284"
];

var imgList2 = [
	"space1_1280x720",
	"space2_1920x1080",
	"space3_1920x1080",
	"space4_1920x1080",
	"space5_4096x2160",
	"space6_1920x1108"
];

var i = 0; // global current pointer to image URL
var c, ctx, img; // canvas, canvas-context, image vars
img = new Image(); // initialise image var with a blank image
var realTest = "no";

/* Will close the test window and direct user to thankyou.php*/
function completeTest(){
	location.replace("https://humanastro.csproject.org/thankyou.php");
	//window.close();
}

function changeToRealTest(){
		/* Change the text for the heading */
		var testHead = document.getElementById("testHeading");
		testHead.innerHTML = "Real Test";

		/* Change the text for explaination paragraph*/
		var explainPara = document.getElementById("explainationPara");
		explainPara.innerHTML = "Congratulations on finishing the tutorial test! Now click the 'Take Real Test' button to proceed.";

		/* Hide the bullet section from previous*/
		var explainBullet = document.getElementById("explainationBullet");
		explainBullet.style.display = "none";

		/* Show Take real test button*/
		var realTestBttn = document.getElementById("startReal");
		realTestBttn.style.display = "block";
		realTestBttn.onclick = callFunctions2; //call function to slide through images and change content
}

function changeSection(){
	/* Show and hide explanation section*/
	 var explanationSect = document.getElementById("explanationDiv");
	 if(explanationSect.style.display == "block"){
			 explanationSect.style.display = "none";
	 } else if (explanationSect.style.display == "none"){
		 	explanationSect.style.display = "block";
	 }

  /* Show and hide the images within the canvas section */
	 var canvasSect = document.getElementById("canvasDiv")
	 if(canvasSect.style.display == 'none'){
			 canvasSect.style.display = 'block';
	 } else if(canvasSect.style.display == "block"){
		 		canvasSect.style.display = "none";
	 }

	 /*	Hide 'Take Tutorial Test' button*/
	 var tutorBttn = document.getElementById("startTutorial");
	 tutorBttn.style.display = "none";
}

//// cycle through the image URLs - call a new image each time this is called
// function getNextImgUrl(){
// 	if (i>=imgList.length) i=0; //will return back to i=0 if it reaches the end of array
// 	var imgUrl = '/javascript/spaceImages/Calibration-'+imgList[i++]+'.png';
// 	console.log(imgUrl); // debug
//
// 	return imgUrl;
// }


function getNextImgUrl(){
		 if (i<imgList.length ) {
			 console.log(i);
		   console.log(imgList[i]); // debug
			 	var imgUrl = "/javascript/spaceImages/Calibration-"+imgList[i++]+".png";
				console.log(imgUrl); // debug
				return imgUrl;
		} else if (i>=imgList.length) {
				i=0
				changeToRealTest();
				changeSection(); //will change section back to get ready for the real test
		};
}

/* used to get imgUrl from second array of images */
function getNextImgUrl2(){
		 if (i<imgList2.length ) {
			 console.log(i);
		   console.log(imgList2[i]); // debug
			 	var imgUrl = "/javascript/spaceImages/RealTest-"+imgList2[i++]+".png";
				console.log(imgUrl); // debug
				return imgUrl;
		} else if (i>=imgList2.length) {
				completeTest(); //call this function once completed
		};
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

/* To iterate through the second array of images */
function doIt2() {
		img = new Image();
		img.src = getNextImgUrl2(); // every time this is called, a new image is loaded -- no need for ajax (yet)!
		img.onload = function(){ // after the image is loaded, draw it in the canvas
			resizeCanvas(); // resize the image to fit the current browser window size
		}
};

function changeRealTestVariable(){
		realTest = "yes";
}

function callFunctions(){
		doIt();
		changeSection();
}

function callFunctions2(){
	changeRealTestVariable();
	doIt2();
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
// This will change the image if the spacebar is pressed
window.addEventListener("keydown", function(event){
		console.log('key pressed'); // debug
		if (event.defaultPrevented) {
			return; // Do nothing if the event was already processed
		}
		if (event.key === " " && realTest == "no"){
				doIt();
		} else if (event.key === " " && realTest == "yes") {
				doIt2();
				console.log(realTest); //for bug
		}
		// Cancel the default action to avoid it being handled twice
		event.preventDefault();
	}, true
);
