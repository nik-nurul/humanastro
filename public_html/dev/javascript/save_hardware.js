var xhttp = new XMLHttpRequest();

function init(){
	// wait until webcam is detected (or not)
	// then send hardware parameters to DB
	detectWebcam(
		(hasWebcam)=>{
			var jsonData = JSON.stringify({
				"userIdStr": document.getElementById("userId").innerHTML,
				"hardware": {
					"screen_width": (screen.width*window.devicePixelRatio),
					"screen_height": (screen.height*window.devicePixelRatio),
					"has_webcam": hasWebcam,
					"os": jscd.os +' '+ jscd.osVersion,
					"browser": jscd.browser +' '+ jscd.browserMajorVersion +' (' + jscd.browserVersion + ')',
					"mobile": jscd.mobile
				}
			});
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					console.log(this.responseText); // debug -- just log the output to JS console 
				}
			};
			xhttp.open("POST", "saveHardware.php", true);
			xhttp.setRequestHeader("Content-type", "application/json");
			xhttp.send(jsonData);
	});
}

window.onload = init;