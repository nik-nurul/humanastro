"use strict";

// sample data to send store on MongoDB
// - in the application, this should probably be a queue - send a bunch of data points once the queue receives a certain number
var data = {
	someData:{
		name:"Some random stuff",
		time:(Date.now()),
		randomNum:(Math.floor(Math.random() * 1000)+1)
	}
};

// run the ajax request
function loadDoc() {
	
  var xhttp = new XMLHttpRequest();
  
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("demo").innerHTML =
      this.responseText;
    }
  };
  // make a post request to the PHP page that sends data to MongoDB
  xhttp.open("POST", "ajaxPostToMongo.php", true);
  
  
  // the the data type in the POST data to JSON
  xhttp.setRequestHeader("Content-type", "application/json");

  xhttp.withCredentials = true; 


  // convert the javascript object to a JSON string and submit with the POST request
  xhttp.send(JSON.stringify(data));
}

function init(){
    /*If the user clicks the 'addToMongo' button*/
    var addToMongo = document.getElementById("addToMongo");
    addToMongo.onclick = loadDoc;
}

window.onload = init;
