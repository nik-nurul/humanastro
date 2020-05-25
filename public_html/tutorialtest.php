"use strict";

function tutorialtimer () {
	alert('Your time for tutorial test image test has run out. Good luck on the real test!');
  //after the time ends, it will redirect the user to imagetest.html
	window.location.assign("firsttest.php");
}


/* FOR TUTORIAL TEST - will show alert button that the timer will start. Start the timer*/
function tutorialTimerStart(){
  alert('You have 5 seconds for the tutorial test');
  /* to unhide the tutorial image*/
  var showTutorialImage = document.getElementById("tutorial_image").style.display = "block";
  setTimeout(tutorialtimer, 5110); /**counted in milisec*/
}



function init(){
    /*If the user click the 'take test' button*/
    var taketest = document.getElementById("taketest");
    taketest.onclick = tutorialTimerStart;

}


window.onload = init;
