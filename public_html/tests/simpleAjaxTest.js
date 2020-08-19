"use strict";

function loadDoc() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("demo").innerHTML =
      this.responseText;
    }
  };
  xhttp.open("GET", "ajax_info.txt", true);
  xhttp.send();
}

function init(){
    /*If the user clicks the 'changeContent' button*/
    var changeContent = document.getElementById("changeContent");
    changeContent.onclick = loadDoc;

}



window.onload = init;
