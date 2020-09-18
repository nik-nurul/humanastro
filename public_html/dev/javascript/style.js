/*This javascript file is for the website's style that requires Javascript functions */

"use strict"; //indicate the codes need to be executed in strict mode


/* Change the navigation bar background color if scrolled passed the background image*/
var navbar = document.getElementById("navibarID");
window.onscroll = function () {
    if (document.body.scrollTop >= 200 ) {
        navbar.classList.add("navibar-scroll");
        navbar.classList.remove("navibar-no-color");
    }
    else {
        navbar.classList.add("navibar-no-color");
        navbar.classList.remove("navibar-scroll");
    }
};
