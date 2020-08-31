"use strict";

// based on: https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/createImageBitmap

// these image names could be gotten from the MongoDB
// or the images themselves could be stored there, in base64 text format
var myWindow;
var cali_width = screen.width;
var cali_height = screen.height;

var i = 0; // global current pointer to image URL
var c, ctx, img; // canvas, canvas-context, image vars
img = new Image(); // initialise image var with a blank image

function openNewWindow(){
	myWindow = window.open("", "", "width="+cali_width+", height=" +cali_height);
  myWindow.location.href = "https://humanastro.csproject.org/test_new_window2.php";
  myWindow.focus();
}



function init(){
		/* if user clicks the 'calibration' button*/
		var startCali = document.getElementById("startCalibration");
		startCali.onclick = openNewWindow;

}

window.onload = init;
