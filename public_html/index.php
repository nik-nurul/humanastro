<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Are Humans Smarter Than Machines (At Astronomy)?</title>
<?php
require_once 'includes/head-base.html';
?>

</head>

<body class="bg">

<?php
require_once 'includes/header.html';
?>

	<!-- division for website name -->
	<div>
	<h1 id="web_title" class="web_title_font">
		<span class="line">Are Humans Better Than Machines</span>
		<span class="line">(At Astronomy)?</span>
	</h1>
	</div>


	<!--division for paragraph about the test-->
	<section>
<?php
require_once 'includes/feeback-link.php';
?>
        <div id="content_paragraph">
            <h2 class="heading_font"> About the test </h2>
            <hr class="heading">
        		<p class="paragraph_font">
              The test is about Airlines is one of the biggest airways company in the world with its
              big success in aviation industry. a vision to lead Swinburne Airlines as one of the leading
              international airline. that it is today. From only seven aircraft when it was first
              launched in 1999. The greatest deal that can be offered. We work a variety of airbusses
              everyday so that you can have many choice of flight. One of our mission is to provide affordable
              price for people from all walk of lives so that everyone can fly! From the low-fare
              flight to the comfort of the passengers
            </p>

            <p class="paragraph_font">
              The test is about Airlines is one of the biggest airways company in the world with its
              big success in aviation industry. a vision to lead Swinburne Airlines as one of the leading
              international airline. that it is today. From only seven aircraft when it was first.
            </p>
        </div>


        <div id="content_paragraph">
            <h2 class="heading_font"> Purpose of the test </h2>
            <hr class="heading">
            <p class="paragraph_font">
              The test is about Airlines is one of the biggest airways company in the world with its
              big success in aviation industry. a vision to lead Swinburne Airlines as one of the leading
              international airline. that it is today. From only seven aircraft when it was first.
              unched in 1999. The greatest deal that can be offered. We work a variety of airbusses
              everyday so that you can have many choice of flight. One of our mission is to provide affordable
              price for people from all walk of lives so that everyone can fly! From the low-fare
              flight to the comfort of the passengers
            </p>
      </div>

      <div id="content_paragraph" >
          <h2 class="heading_font"> What is needed </h2>
          <hr class="heading">
          <p class="paragraph_font">
            The test is about Airlines is one of the biggest airways company in the world with its
            big success in aviation industry. a vision to lead Swinburne Airlines as one of the leading
            international airline. that it is today. From only seven aircraft when it was first.
            unched in 1999. The greatest deal that can be offered.
            e work a variety of airbusses
            everyday so that you can have many choice of flight. One of our mission is to provide affordable
            price for people from all walk of lives so that everyone can fly! From the low-fare
            flight to the comfort of the passengers
          </p><br/>

            <!-- button to take the test -->
          <a href="consent.php"><button id="proceedbutt" class="bttn paragraph_font" type="button">Take the test</button></a>
      </div>
    </section>

<?php
require_once 'includes/footer.html';
?>

</body>
</html>
