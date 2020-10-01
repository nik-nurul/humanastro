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
		<h2 class="heading_font">Not Eligible</h2>
		<hr class="heading"><br/>

		<!-- webpage content -->
		<p class="paragraph_font">Thank you for your interest to participate in this study. Howevever, you
    need to be over 18 to proceed.
		</p>
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

