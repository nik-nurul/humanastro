"use strict";

function timer () {
	alert('Your time for tutorial test image test has run out. Good luck on the real test!');
  //after the time ends, it will redirect the user to imagetest.html
	window.location.assign("imagetest.php");
}

/* will show alert button that the timer will start. Start the timer*/
function timerStart(){
  alert('You have 5 seconds for the first test');
  /* to unhide the tutorial image*/
  var showTutorialImage = document.getElementById("tutorial_image").style.display = "block";
  setTimeout(timer, 5110);
}

// add a favourite
// https://rudrastyh.com/javascript/favorite-button.html
function rudr_favorite(a) {
	bookmarkButton = 
	pageTitle=document.title;
	pageURL=document.location;
	try {
		// Internet Explorer solution
		eval("window.external.AddFa-vorite(pageURL, pageTitle)".replace(/-/g,''));
	}
	catch (e) {
		try {
			// Mozilla Firefox solution
			window.sidebar.addPanel(pageTitle, pageURL, "");
		}
		catch (e) {
			// Opera solution
			if (typeof(opera)=="object") {
				a.rel="sidebar";
				a.title=pageTitle;
				a.url=pageURL;
				return true;
			} else {
				// The rest browsers (i.e Chrome, Safari)
				alert('Press ' + (navigator.userAgent.toLowerCase().indexOf('mac') != -1 ? 'Cmd' : 'Ctrl') + '+D to bookmark this page.');
			}
		}
	}
	return false;
}

function init(){
    /*If the user click the 'take test' button*/
    var taketest = document.getElementById("taketest"); 
    taketest.onclick = timerStart;
	// If the user clicks the "bookmark" button
	var bookmarkButton = document.getElementById("bookmark_to_resume");
	bookmarkButton.onclick = rudr_favorite;
}


window.onload = init;
