<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Visualisation Test Page - Other Test</title>
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
	<div id="content_paragraph">
	
		<!--Page header-->
		<h2 class="heading_font">Other Test</h2>
		<hr class="heading"><br/>
	
		<div class="timer">
			<label id="minutes">00</label> min <label id="seconds">00</label> sec
		</div>
		
		<p class="paragraph_font" id="timercomm">The timer above will record the time taken for you to complete the test</p>
		<br/><br/>
		<p class="paragraph_font"> Other test will go here.. </p>
		<br/>
		<br/>
		<br/>
		
		<!-- section for buttons -->
		<div>
			<!-- Finish button -->
			<a href="thankyou.php"><input class="bttn" id="finishBttn" type= "submit" value="Finish"/></a>
		</div>
		
	</div>
  </section>
	
<?php
include 'includes/footer.html';
?>

</body>
</html>
