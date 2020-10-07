<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Thank You</title>
<?php
require_once 'includes/head-base.html';
?>
    <!-- for navigation bar -->
    <script src="javascript/style.js"></script>
</head>

<body>
	
<?php
require_once 'includes/header.html';
?>
  <!-- division for content-->
  <section> 
<?php
require_once 'includes/feeback-link.php';
?>
	<div id="content_paragraph">
	
		<!--Page header-->
		<h2 class="heading_font">Thank You</h2>
		<hr class="heading"><br/>
	
		<!-- webpage content -->
		<p class="paragraph_font"> Thank you for your time and participation. The data captured will be sent to the Centre for Astrophysics
		and Supercomputing, at the Swinburne University of Technology.<br/><br/>
		
		For any questions or concerns, please get in touch with the team <a href="contact.php">here</a>.</p>
		<br/>
		<br/>
	
	
	
		<!-- section for buttons -->
		<div>
			<!-- Buttons to return to home-->
			<a href="./"><input class="bttn" id="homeBttn" type= "submit" value="Exit to Home"/></a>
			<br/>
		</div>
	
	</div>
  </section>
	
<?php
require_once 'includes/footer.html';
?>

</body>

</html>
