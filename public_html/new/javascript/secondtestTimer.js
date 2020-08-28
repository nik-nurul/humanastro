"use strict";

/* This function is used to call the timer and display timer function*/
/* This is done like this because the two functions need to be called together */
function callfunctions (){
    startsecondtimer();
    timeTick();
}

/* to alert the user that the time has passed and user will be directed to moretest.php page*/
function timer (){
    alert('Your time for second image test has run out. Good luck on the next test!');
    //after the time ends for second test, it will redirect the user to thankyou.php
  	window.location.assign("moretest.php");
}

/* to start countdown */
function startsecondtimer (){
      alert('You have 15 seconds for the second test');
      /*to hide the 'take second test' button when the test started*/
      var hidecontent = document.getElementById("takesecondtest").style.display = "none";
      /*to show the space image that is previously hidden*/
			var secondTest = document.getElementById("secondtestsection").style.display = "block";
			setTimeout(timer, 15210);
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
    setTimeout(function(){timeUpdate.value="11 seconds" }, 11000);
    setTimeout(function(){timeUpdate.value="12 seconds" }, 12000);
    setTimeout(function(){timeUpdate.value="13 seconds" }, 13000);
    setTimeout(function(){timeUpdate.value="14 seconds" }, 14000);
    setTimeout(function(){timeUpdate.value="15 seconds" }, 15000);
}



function init(){
			/*If the user click the 'take second test' button*/
			var secondtest = document.getElementById("takesecondtest");
			secondtest.onclick = callfunctions;
}

window.onload = init;
