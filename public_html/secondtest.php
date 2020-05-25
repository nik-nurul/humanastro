<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Visualisation Test Page - Official Test</title>
<?php
require 'includes/head-base.html';
?>
  <script src="javascript/secondtestTimer.js"></script>
</head>

<body>
<?php
require 'includes/header.html';
?>
  <section>
<?php
require 'includes/feeback-link.php';
?>
	<div id="content_paragraph">

	<!--Page header-->
		<h2 class="heading_font">Visualisation Test</h2>
		<hr class="heading"><br/>
    <p> Click the button whenever you are ready.</p>
    <!-- Button to take the real test -->
    <a href="#secondtestsection"><button id="takesecondtest" type="button" class="bttn">Take Second Test </button></a>


<!-- ___________________________________THIS WILL BE HIDDEN FIRST BEFORE THE 'TAKE SECOND TEST' BUTTON IS TAKEN ______________________________ -->
          <div id="secondtestsection" class="hide_tutorial">
                <br/>
                <!-- Part to display time ticking-->
                <p class="display_time">Time: <input type="text" id="timetxt" /> </p><br/><br/>

            		<!-- section for visualisation official test -->
            		<p class="paragraph_font" id="questionno">Question 2</p>
            		<p class="paragraph_font">Find this feature and stare at it for 15 seconds</p>
            		<div class="test">
            			<br/>
            			<img src="images/space3.PNG" height="322" width="322" id="spaceimg"/>
            		</div>
            		<br/>
            		<br/>

            		<!-- section for buttons -->
            		<div>
            			<!-- Button to continue to next test -->
                  <!-- In this situatuin (for now), will direct to placeholder for moretest webpage-->
            			<a href="moretest.php"><input class="bttn" id="nextBttn" type= "submit" value="Proceed"/></a>
            		</div>
        </div>
	</div>
  </section>

<?php
require 'includes/footer.html';
?>

</body>
</html>
