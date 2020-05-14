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
include 'includes/head-base.html';
?>
  <script src="javascript/vistest.js"></script>
</head>

<body>
<?php
include 'includes/header.html';
?>
  <section> 
<?php
include 'includes/feeback-link.php';
?>
	<div id="content_paragraph">
	
	<!--Page header-->
		<h2 class="heading_font">Visualisation Test</h2>
		<hr class="heading"><br/>
	
		<div class="timer">
			<label id="minutes">00</label> min <label id="seconds">00</label> sec
		</div>
		
		<p class="paragraph_font" id="timercomm">The timer above will record the time taken for you to complete the test</p>
		<br/><br/>
		
		<!-- section for visualisation official test -->
		<p class="paragraph_font" id="questionno">Question 1</p>
		<p class="paragraph_font">Find this feature and stare at it for 10 seconds</p>
		<div class="test">
			<br/>
			<img src="images/space.jpg" height="142" width="222" id="spaceimg"/>
		</div>
		<br/>
		<br/>
		<p class="paragraph_font" id="questionno">Question 2</p>
		<p class="paragraph_font">Find this feature and stare at it for 10 seconds</p>
		<div class="test">
			<br/>
			<img src="images/space.jpg" height="142" width="222" id="spaceimg"/>
		</div>
		<br/>
		<br/>
		<p class="paragraph_font" id="questionno">Question 3</p>
		<p class="paragraph_font">Find this feature and stare at it for 10 seconds</p>
		<div class="test">
			<br/>
			<img src="images/space.jpg" height="142" width="222" id="spaceimg"/>
		</div>
		<br/>
		<br/>

		<!-- section for buttons -->
		<div>
			<!-- Buttons to continue or quit -->
			<a href="moretest.php"><input class="bttn" id="nextBttn" type= "submit" value="Proceed"/></a>
		</div>
	</div>
  </section>
	
<?php
include 'includes/footer.html';
?>

</body>
</html>