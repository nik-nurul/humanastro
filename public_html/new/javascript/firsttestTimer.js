"use strict";

/* This function is used to call the timer and display timer function*/
/* This is done like this because the two functions need to be called together */
function callfunctions(){
    startfirsttimer();
    timeTick();
}

/* to alert the user that the time has passed and user will be directed to secondtest.php */
function timer (){
  alert('Your time for first image test has run out. Good luck on the next test!');
  //after the time ends for first test, it will redirect the user to secondtest.php
	window.location.assign("secondtest.php");
}

/* to start countdown */
function startfirsttimer (){
      alert('You have 10 seconds for the first image test');
      /* to hide the button "take first test" that's previously clicked */
      var hidecontent = document.getElementById("takefirsttest").style.display = "none";
      /*to show the space image that is previously hidden*/
			var firstTest = document.getElementById("firsttestsection").style.display = "block";
			setTimeout(timer, 10110);
}

/*To display the time ticking up*/
function timeTick () {
	var timeUpdate = document.getElementById("timetxt");
	setTimeout(function(){timeUpdate.value="1 seconds" }, 1000);
	setTimeout(function(){timeUpdate.value="2 seconds" }, 2000);
	setTimeout(function(){timeUpdate.value="3 seconds" }, 3000);
	setTimeout(function(){timeUpdate.value="4 seconds" }, 4000);
	setTimeout(function(){timeUpdate.value="5 seconds" }, 5000);
  setTimeout(function(){timeUpdate.value="6 seconds" }, 6000);
  setTimeout(function(){timeUpdate.value="7 seconds" }, 7000);
  setTimeout(function(){timeUpdate.value="8 seconds" }, 8000);
  setTimeout(function(){timeUpdate.value="9 seconds" }, 9000);
  setTimeout(function(){timeUpdate.value="10 seconds" }, 10000);
}

function init(){
			/*If the user click the 'take first test' button*/
			var firsttest = document.getElementById("takefirsttest");
			firsttest.onclick = callfunctions;
}

window.onload = init;
