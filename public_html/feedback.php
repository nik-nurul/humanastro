<?php
/*
	TO DO
	* return POST variables or session variables to the return page 
		via the redirect so the user session isn't interrupted
*/

session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/functions.php';

// if the comment is set, add it to the database
if ( isset($_POST["feedbackcomment"]) ) {
	if ( ! isset($_POST["feedbackreturn"]) )
		$_POST["feedbackreturn"] = "no data";
		$newFeedback = [
			"date" => date("Y-m-d H:i:s\Z"), // return date (but not time because it is too identifying)
			"from_page" => sanitise_input($_POST["feedbackreturn"]),
			"comment" => sanitise_input($_POST["feedbackcomment"])
		];

	$dbName = 'humanastro';		// database name
	$Fcoll = 'userfeedback';	// feedback collection name

	// store feedback in database
	try {
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB
		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
		$bulk->insert($newFeedback);
		$result = $manager->executeBulkWrite($dbName.'.'.$Fcoll, $bulk);

	// exception handling for the database connection	
	} catch (MongoDB\Driver\Exception\Exception $e) {

		$filename = basename(__FILE__);
		
		echo "The $filename script has experienced an error.\n"; 
		echo "It failed with the following exception:\n";
		
		echo "Exception:", $e->getMessage(), "\n";
		echo "In file:", $e->getFile(), "\n";
		echo "On line:", $e->getLine(), "\n";       
	}

	if ( $_POST["feedbackreturn"] == "no data" )
		$_POST["feedbackreturn"] = "index.php";
	
	header("Location: ".$_POST["feedbackreturn"]);
	exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Feedback</title>
<?php
require_once 'includes/head-base.html';
?>
</head>

<body>
	
<?php
require_once 'includes/header.html';
?>
<!-- division for user feedback form-->
  <section>  
  
	<div id="content_paragraph">

		<!--Page header-->
		<h2 class="heading_font">User Feedback</h2>
		<hr class="heading"><br/>
		
		<!--Content paragraph-->
		<p class="paragraph_font">Please describe your feedback here. Bug reports, feature requests, design issues and any other feedback are welcome.</p>

		<p class="paragraph_font">This feedback form stores your comment, the date (but not the time), the comment was made and the page you were on when before you came to this feedback page.</p>

		<p class="paragraph_font">For you to remain anonymous it is your responsibility not to input any identifying details here. Do not include your name, email address or any other personally-identifying data.</p>
		
		<br/>
 
		<form action="feedback.php" method="post" >
			<p><label class="paragraph_font" for="feedbackcomment">Feedback:</label>
				<br />
				<textarea id="feedbackcomment" name="feedbackcomment" placeholder="Please enter your feedback comments here." rows="8" cols="60"></textarea>	
			</p>
			<!-- section for buttons  -->
<?php
	$feedbackreturn = "index.php";
	if ( isset($_POST["feedbackreturn"]) )
		$feedbackreturn = $_POST["feedbackreturn"];
	echo '			<input id="feedbackreturn" type="hidden" name="feedbackreturn" value="'.$feedbackreturn.'" />
';
?>
		<div class="container">
			<div class="center">
				<input class="bttnsubmit" id="submitBttn" type= "submit" value="Submit and continue"/>
			</div>
		</div>
			
		</form>
		
		<br/>
        <br/>
        <br/>
		<br/>
        
		
		<!-- this button will redirect to homepage -->
		<a href="index.php"><input class="bttn" id="quitBttn" type= "reset" value="Exit to Home"/></a>
		
		<br/>
		
	</div>
  </section>
  
<?php
require_once 'includes/footer.html';
?>

</body>
</html>
