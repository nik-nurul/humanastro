"use strict";

var w; // the popup window instance

function newPopup(){
	w = window.open(
		'popup.php',
		'popup test!',
		'left=0,top=0,width='+screen.width+',height='+screen.height+',resizeable=off,scrollbars=off'
	);
}

function init(){
	var newPopup = document.getElementById("newPopup");
	newPopup.onclick = newPopup;
}

window.onload = init;
