"use strict";

function validatedemo(){
	//initialize local variables
	var errMsg = "";			//stores the error message
	var result = true;			//assumes no errors
	
	//get variables from form and check rules here
	var under18 = document.getElementById("<18").checked;
	var to1825 = document.getElementById("18-25").checked;
	var to2635 = document.getElementById("26-35").checked;
	var to3645 = document.getElementById("36-45").checked;
	var over45 = document.getElementById(">45").checked;
	var nosay = document.getElementById("agenot").checked;

	var female = document.getElementById("female").checked;
	var male = document.getElementById("male").checked;
	var nonbinary = document.getElementById("nb").checked;
	var gennot = document.getElementById("gennot").checked;
	var selfdesc = document.getElementById("sb").checked;
	
	var udgm = document.getElementById("udm").selected;
	var phd = document.getElementById("phd").selected;
	var phd5 = document.getElementById("5phd").selected;
	var phd515 = document.getElementById("5phd15").selected;
	var phd15 = document.getElementById("phd15").selected;
	var csnot = document.getElementById("csnot").selected;
	
	var ooIRast = document.getElementById("ooIRast").selected;
	var ora = document.getElementById("ora").selected;
	var ooth = document.getElementById("ooth").selected;
	var tcast = document.getElementById("tcast").selected;
	var irs = document.getElementById("irs").selected;
	var areanot = document.getElementById("areanot").selected;

	//if something is wrong set result = false, and concatenate error message
	
	/*Must select age*/
	if (!(under18 || to1825 || to2635 || to3645 || over45 || nosay)) {
		errMsg += "Please select your age group. \n"
		result = false;
	}
	
	/*Must select gender*/
	if (!(female || male || nonbinary || gennot || selfdesc)) {
		errMsg += "Please select your gender. \n"
		result = false;
	}
	
	if (errMsg != ""){
		alert(errMsg);
	}
	
	return result;
}

function gohome(){
	window.location = "home.html";
}

function init(){
		var demoform = document.getElementById("demoform");// get ref to the HTML element
		demoform.onsubmit = validatedemo; //register the event listener*/
		
		var homebutt = document.getElementById("homebutt");
		homebutt.onclick = gohome;
}


window.onload = init;

	