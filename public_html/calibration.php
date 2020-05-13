<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="SEPA Project G3" />
  <meta name="keywords" content="HTML5" />
  <meta name="author" content="Fakhirah Shamsul"  />
  <link rel="stylesheet" type="text/css" href="websitestyle.css">
  <!--page title -displayed on tab- -->
  <title>Webcam Calibration Page</title>
  <!-- Viewport set to scale 1.0-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>
	
	<!--header section-->
	<header>
		<!--main header-->
		<h1 id="maintitle">Are Machines Better Than Humans (at Astronomy)?</h1>
		<!--sub header-->
		<h2 id="subtitle">Webcam Calibration</h2>
	</header><hr><br/>
<?php
// debug
	echo '<p>DEBUG -- $_POST: <br><pre>';
	var_dump($_POST);
	echo '</pre>';
	echo '<p>DEBUG -- $_SESSION: <br><pre>';
	var_dump($_SESSION);
	echo '</pre>';

	// update user record with experience answers

	$dbName = 'humanastro';		// database name
	$Ucoll = 'users';			// user collection name

	$userId = $_SESSION["userId"]; // get userId from session
//	$userId = $_POST["userId"]; // extract userId string
	unset($_POST["userId"]); // remove userID from POST array
	  
try {
	$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

	// add the experience answers to the user record
	$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);

	// create user ID object with which to ID the user record
	$_id = new MongoDB\BSON\ObjectID($userId);

	// OVERVIEW - Algorithm:
	// * iterate through each experience question in user record
	// * find the question ID
	// * match that to the question ID in the $_POST array
	// * add the answer from the $_POST array to the experience question in the user record
	
	// set up a query to retrieve questions from the new user record in the database
	$q_filter = [ "_id" => $_id ]; // just get the user whole user record
	$q_options = ['sort' => ['question_num' => 1]]; // sort the results based on question_num
	$query = new MongoDB\Driver\Query( $q_filter, $q_options ); // create a query object with the above parameters
    $cursor = $manager->executeQuery( $dbName.'.'.$Ucoll, $query ); // execute and return a cursor
	
	// retrieve user	
	// there should only be one result - this makes sure we get the only result
	$iterator = new IteratorIterator($cursor); // generate an iterator from the cursor
	$iterator->rewind(); // make sure the iterator is at the first element (head)
	$userDoc = $iterator->current(); // retrieve the user document from the iterator
	
	// only write answers to database if consent was given
	if ( isset( $_SESSION["consent"] ) and $_SESSION["consent"] ) {
		
		// iterate through the experience questions to set their answers
		foreach ($userDoc->experience_data as $q){
			$q_id = $q->q_id;		// the abbreviated question identifier
			// set the answer, if it exists in the $_POST array
			if (isset($_POST[$q_id])) {
				$answer = $_POST[$q_id];
				// find specific question in this user document
				$u_filter = [
					"_id" => $_id, // this is the document or object (user, in this case) ID
					"experience_data" => [ '$elemMatch' => [ "q_id" => $q_id ] ] // experience_data contains questions and answers
				];
				// set the answer value for this question
				$bulk->update(
					$u_filter,
					[ '$set' => [ "experience_data.$.answer" => $answer ]]
				);
			}
		}

		// execute writing all the experience answers
		$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);
	}


	
// exception handling for the database connection	
} catch (MongoDB\Driver\Exception\Exception $e) {

    $filename = basename(__FILE__);
    
    echo "The $filename script has experienced an error.\n"; 
    echo "It failed with the following exception:\n";
    
    echo "Exception:", $e->getMessage(), "\n";
    echo "In file:", $e->getFile(), "\n";
    echo "On line:", $e->getLine(), "\n";       
}
	
?>
	
	<!--header for webcam calibration task-->
	<h3> Please stare at the red dot for 10 seconds then, click the dot. </h3>
	<p> *** will have a pop-up message asking the user to maximize the browser window. </p>
	<br/><br/>
	
	<!--division for webcam calibration task-->
	<div class="calibration">
		<br/>
		<br/>
		<p id="RedDot"> &#11093; </p>
		<br/>
		<br/>
		<p id="RedDot"> &#9940; </p>
		<br/>
		<br/>
		<p id="RedDot"> &#11093; </p>
		<br/>
		<br/>
		<!-- commnt -->
		<!-- comment 2 -->
		<br/>

	</div>
	
	<!-- section for buttons -->
	<div>
		<!-- -->
		<a href="home.html"><input id="quitBttn" type= "reset" value="Exit to Home"/></a>
		<!-- will redirect to tutorial for the visualisation test -->
		<a href="tutorialtest.html"><input id="submitBttn" type= "submit" value="Submit and continue"/></a>
		<br/><br/><br/><br/>
	</div>
	
	
	<!--footer section-->
	<footer>
		<p>Software Engineering Project A<p>
		<p>&#169; Swinburne University of Technology</p>
	</footer>

</body>
</html>