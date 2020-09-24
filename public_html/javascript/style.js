/*This javascript file is for the website's style that requires Javascript functions */

"use strict"; //indicate the codes need to be executed in strict mode


/* Change the navigation bar background color if scrolled passed the background image*/
var navbar = document.getElementById("navibarID");

window.addEventListener('scroll', function (e) { //if the page is scrolled
    var navbar = document.getElementById('navibarID');

    //check if the current page is index page or not
    if (window.location.href == "https://humanastro.csproject.org/"){
         if (document.documentElement.scrollTop>550 || document.body.scrollTop >550) { //if scrolled passed this height
            navbar.classList.add('nav-colored');
            navbar.classList.remove('nav-transparent');
        } else {
            navbar.classList.add('nav-transparent');
            navbar.classList.remove('nav-colored');
        }

    } else {
        if (document.documentElement.scrollTop>10 || document.body.scrollTop >10) { //if scrolled passed this height
            navbar.classList.add('nav-colored');
            navbar.classList.remove('nav-transparent');
            console.log(window.location.href);
        } else {
            navbar.classList.add('nav-transparent');
            navbar.classList.remove('nav-colored');
        }

    }
});
