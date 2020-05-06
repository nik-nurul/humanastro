"use strict";

function timer () {
	alert('Your time for tutorisal test image test has run out. Good luck on the real test!');
  //after the time ends, it will redirect the user to imagetest.html
	window.location.assign("imagetest.html");
}

/* will show alert button that the timer will start. Start the timer*/
function timerStart(){
  alert('You have 5 seconds for the first test');
  /* to unhide the tutorial image*/
  var showTutorialImage = document.getElementById("tutorial_image").style.display = "block";
  setTimeout(timer, 5110);
}


function init(){
    /*If the user click the 'take test' button*/
    var taketest = document.getElementById("taketest"); 
    taketest.onclick = timerStart;
}


window.onload = init;
