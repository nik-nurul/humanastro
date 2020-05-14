<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Webcam Calibration</title>
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
<?php
include 'includes/feeback-link.html';
?>
	<div id="content_paragraph">

		<!--Page header-->
		<h2 class="heading_font">Webcam Calibration</h2>
		<hr class="heading"><br/>
	
		<!--Content paragraph-->
		<p class="paragraph_font"> Please stare at the red dot for 10 seconds then, click the dot. <br/>
		*** will have a pop-up message asking the user to maximize the browser window. </p>
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
			<!-- comment -->
			<!-- comment 2 -->
			<br/>
	
		</div>

<?php
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
	

	
		<!-- section for buttons -->
		<div>
			<!-- will redirect to tutorial for the visualisation test -->
			<a href="tutorialtest.php"><input class="bttn" id="submitBttn" type= "submit" value="Submit and continue"/></a>

			<!-- -->
			<a href="index.php"><input class="bttn" id="quitBttn" type= "reset" value="Exit to Home"/></a>
			
			<br/>
		</div>
	</div>
  </section>
	
	<!--footer section-->
	<footer>
		<p>Software Engineering Project A<p>
		<p>&#169; Swinburne University of Technology</p>
	</footer>

</body>
</html>