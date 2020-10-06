/**
* Author: GROUP 03 Human Astro
* Target: demographic.php
* Purpose: This file is to control the behaviour of when 'other' is chosen from the selection in the demographic questions
* Created: 6th October 2020
* Last updated: 6th October 2020
*/

"use strict";

/* For region qurstion */
function reg_desc(select){
  if(select.value=="oth" ){
      document.getElementById('geo_describe').style.display = "block";
  } else{
      document.getElementById('geo_describe').style.display = "none";
  }
}

/* for primary research question */
function research_desc(select){
  if(select.value=="arOther" ){
      document.getElementById('research_describe').style.display = "block";
  } else{
      document.getElementById('research_describe').style.display = "none";
  }
}
