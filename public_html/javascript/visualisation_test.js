"use strict";

// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

// these image names could be gotten from the MongoDB
// or the images themselves could be stored there, in base64 text format
var tutorialImages = [
	"starfield1_1920x1080",
	"starfield2_1280x0649",
	"starfield3_504x284"
];

var realTestImages = [
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
var realTestStatus = "no";
var timer = 5000; //default timer value everytime it is called

/* Will close the test window and direct user to thankyou.php*/
function completeTest(){
	location.replace("https://humanastro.csproject.org/thankyou.php");

}

function changeToRealTest(){
		/* Change the text for the heading */
		var testHead = document.getElementById("testHeading");
		testHead.innerHTML = "Real Test";

		/* Change the text for explanation paragraph*/
		var explainPara = document.getElementById("explanationPara");
		explainPara.innerHTML = "Congratulations on finishing the tutorial test! Now click the 'Take Real Test' button to proceed.";

		/* Hide the bullet section from previous*/
		//var explainBullet = document.getElementById("explanationBullet");
		//explainBullet.style.display = "none";
		
		
		/* Show Take Real Test button*/
		var realTestBttn = document.getElementById("startReal");
		realTestBttn.style.display = "block";
		/*Change the status of real test variable(to med until start real test bttn is clicked) to stop timer from continue looping when spacebar is pressed*/
		realTestStatus = "med";
		realTestBttn.onclick = startRealTest; //call function to slide through images and change content
}

/* to control which section should be shown and which should be hidden*/
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


/* used to get imgUrl from second array of images (for tutorial test)*/
function getNextTutorialImgUrl(){
		 if (i<tutorialImages.length ) {
			 console.log(i);
		   console.log(tutorialImages[i]); // debug
			 	var imgUrl = "/javascript/spaceImages/Calibration-"+tutorialImages[i++]+".png";
				console.log(imgUrl); // debug
				return imgUrl;
		} else if (i>=tutorialImages.length) {
				// the tutorial is finished - reset for real test
				i=0
				changeToRealTest();
				changeSection(); //will change section back to get ready for the real test
		};
}

/* used to get imgUrl from second array of images (for real test)*/
function getNextRealTestImgUrl(){
		 if (i<realTestImages.length ) {
			 console.log(i);
		   console.log(realTestImages[i]); // debug
			 	var imgUrl = "/javascript/spaceImages/RealTest-"+realTestImages[i++]+".png";
				console.log(imgUrl); // debug
				return imgUrl;
		} else if (i>=realTestImages.length) {
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

		ctx.drawImage(img,	0, 0, img.width,		img.height,     // source rectangle
							0, 0, img.width*ratio,	img.height*ratio); // destination rectangle

		console.log('new Canvas width:', c.width); // debug
		console.log('new Canvas height:', c.height); // debug
		console.log('Canvas scale ratio %:', Math.floor(parseFloat(imgScaleRatio*100))); // debug
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

/* To iterate through the second array of images (for real test) */
function getNextRealTestImage() {
//		img = new Image();
		img.src = getNextRealTestImgUrl(); // every time this is called, a new image is loaded -- no need for ajax (yet)!
		img.onload = function(){ // after the image is loaded, draw it in the canvas
			resizeCanvas(); // resize the image to fit the current browser window size
		}
};

/* for tutorial test*/
function startTutorial(){
		changeSection();
		setTutorialTimer();
}

/* for real test*/
function startRealTest(){
	/* hide the "Take Real Test" button */
	var realTestBttn = document.getElementById("startReal");
	realTestBttn.style.display = "none";

	/* call related functions*/
	realTestStatus = "yes";
	changeSection();
	setRealTestTimer();
}

//function for controlling image changes using timer for tutorial test
function setTutorialTimer(){
	if (realTestStatus == "no" && i < tutorialImages.length){
		getNextTutorialImage();
		console.log("5 seconds timer started for tutorial test");
		window.setTimeout(function(){
			//loop the function as long as the last image in the tutorialImages(tutorial images) is not loaded
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
	if (realTestStatus == "yes" && i < realTestImages.length){
		getNextRealTestImage();
		console.log("5 seconds timer started for real test");
		window.setTimeout(function(){
			//loop the function as long as the last image in the realTestImages(real images) is not loaded
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

function init(){
	 c = document.getElementById("myCanvas");
	 ctx = c.getContext("2d");
	 //resizeCanvas();

    /*If the user clicks the 'changeContent' button*/
     var changeContent = document.getElementById("startTutorial");
     changeContent.onclick = startTutorial;
}

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
