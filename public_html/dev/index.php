<?php
	session_start();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Are Machines Better than Humans (at Astronomy)?</title>
<?php
	require_once 'includes/head-base.html';
?>

<script src="javascript/style.js"></script>

</head>

<body class="bg">

<?php
	require_once 'includes/header.html';
?>

	<!-- division for website name -->
	<div>
	<h1 id="web_title" class="web_title_font">
		<span class="line">Are Machines Better than Humans</span>
		<span class="line">(at Astronomy)?</span>
	</h1>
	</div>

	<!--division for paragraph about the test-->
	<section>
<?php
	require_once 'includes/feeback-link.php';
?>
        <div id="content_paragraph">
            <h2 class="heading_font"> About the Test </h2>
            <hr class="heading">
        	<p class="paragraph_font">
				Modern astronomy faces many big data problems. It is expected that an ever-decreasing proportion
				of the data collected will be viewed by a human. Instead, automated techniques, such as
				artificial intelligence and machine learning, are expected to make discoveries for astronomers.
				However, the human visual system is regularly proposed as being the gold standard for novel discovery
				in astronomy, but with limited research to support that claim. <br/> <br/>
				This website has been created to test and measure the visual-discovery skills of
				astronomers, via a series of astronomic images, tested via an eye tracking solution.
			</p>

            <p class="paragraph_font">
				The test will require users to find certain features within the displayed astronomy images,
				or scan the astronomy image for areas of interest within a time limit.
			</p>
			<br/><br/>

			<h2 class="heading_font"> Purpose of the Test </h2>
            <hr class="heading">
            <p class="paragraph_font">
				This test is created to explore the discovery skills of astronomers, to analyse the rising capabilities
				of machines and to boldly compare the two, like no man has done before.
				The outcomes of the test will help answer the question: Are Machines Better than Humans (at Astronomy)?
            </p>
			<br/><br/>

			<h2 class="heading_font"> What is Required </h2>
			<hr class="heading">
			<p class="paragraph_font">
				To successfully carry on with the visualisation test,
				it is recommended that you have a desktop or laptop computer with minimum requirements of a 2GHz processor,
				4GB of RAM and a minimum display resolution of 1600x900px to be used in order to complete the test smoothly.
				A compatible webcam accessible through a browser is also required in order to track eye movement during the tests and
				above all a stable wi-fi connection.
				<br/><br/>
				<a href="hardware_reqs.php">Check Requirements</a>
			</p><br/>

            <!-- button to take the test -->
			<a href="consent.php"><button id="proceedbutt" class="bttn paragraph_font" type="button">Take the Test</button></a>
		</div>
    </section>

<?php
	require_once 'includes/footer.html';
?>

</body>
</html>
