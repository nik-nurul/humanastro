/*This javascript file is for the website's style that requires Javascript functions */

"use strict"; //indicate the codes need to be executed in strict mode


/* Change the navigation bar background color if scrolled passed the background image*/
var navbar = document.getElementById("navibarID");

window.addEventListener('scroll', function (e) {
    var navbar = document.getElementById('navibarID');
    if (document.documentElement.scrollTop>550 || document.body.scrollTop >550) {
            navbar.classList.add('nav-colored');
            navbar.classList.remove('nav-transparent');
        } else {
            navbar.classList.add('nav-transparent');
            navbar.classList.remove('nav-colored');
        }
});
