<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Tutorial Test</title>
<?php
include 'includes/head-base.html';
?>
</head>

<body>
<?php
include 'includes/header.html';
?>
	<!-- division for content-->
    <section>
        <!-- There will be no feedback button in tutorial test page and test pages -->

        <!-- division part for consent statement -->
        <div id="content_paragraph">
            <h2 class="heading_font"> Tutorial Test </h2>
            <hr class="heading">
            <p class="paragraph_font">
                This is a tutorial test before the real test take place. Below is the instructions that need
                to be followed to complete the test successfully.
            </p><br/><br/>
            <p>
              <!-- 7/5/2020: The instructions written below are not the real instructions. It is just purely for the prototype purpose only. -->
                <ul class="bullet_style paragraph_font">
                    <li>There will be a series of images that will be presented</li>
                    <li>Please stare at the images and find similar patterns</li>
                    <li>Each image will have its own timer</li>
                    <li>The timer wil start as soon as you click 'Take Test'</li>
                    <li>There will be 1 image for the tutorial test</li>
                    <li>There will be 6 images for the real test</li>
                </ul>
            </p>
            <br/>
            <br/>
            <br/>
            <br/>

            <!-- button to take the test-->
            <a href="#tutorial_image"><button id="taketest" type="button" class="bttn">Take Test</button></a>
            <br/>
            <br/>
            <br/>
            <br/>
      <div>


      <!-- This section will initially be hidden. If the user click the 'take test' button, it'll be shown -->
      <div id="tutorial_image" class="hide_tutorial">
          <br/>
          <br/>
          <br/>
          <br/>
          <img src="images/space.jpg" alt="space" height="442" width="572"/>
          <br/>
          <br/>
          <!-- Button for user to click if they finish it-->
          <a href="imagetest.php"><button id="finishtutorial" type="button" class="bttn">Finish</button></a>
      </div>


      </section>


    	<!--footer section-->
    	<footer>
    		<p>Software Engineering Project A<p>
    		<p>&#169; Swinburne University of Technology</p>
    	</footer>
</body>

</html>
