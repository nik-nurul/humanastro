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
<?php
require_once 'includes/functions.php';

// taken from the old calibration.php page
// update user record with experience answers

$dbName = 'humanastro';		// database name
$Ucoll = 'users';			// user collection name

$userId = $_SESSION["userId"]; // get userId from session
	  
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
		
		//$date = date("Y-m-d H:i:s\Z"); // date/time string to store with answer -- (last updated: 13 Sept 2020) - For now, it is commented a we dont want to show the date/time to ensure anonymity)
		// iterate through the experience questions to set their answers
		foreach ($userDoc->experience_data as $q){
			$q_id = $q->q_id;		// the abbreviated question identifier
			// set the answer, if it exists in the $_POST array
			if (isset($_POST[$q_id])) {
				$answer = sanitise_input($_POST[$q_id]);
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
				// record the date of the answer -- (last updated: 13 Sept 2020) - For now, it is commented a we dont want to show the date/time to ensure anonymity)
				//$bulk->update(
				//	$u_filter,
				//	[ '$set' => [ "experience_data.$.answerdate" => null ]] //supposed to be $date here but null is placed instead. To ensure anonymity
				//);
			}
		}

		set_current_page($bulk, $_id, basename(__FILE__)); // write the name of the current page to the user record

		// execute writing all the experience answers
		$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);
		
		// experience and demographic data have now been written,
		// redirect to take_test.php
		header('Location: take_test.php?userId='.$userId);
		exit();
		
	} else {
		// TO DO - redirect to an error page explaining no consent
		$_SESSION["consent"] = false;

		echo 
		'		
		<h2 class="heading_font">Thank You</h2>
		<hr class="heading"><br/>
	
		<!-- webpage content -->
		<p class="paragraph_font">We appreciate your interest in participating in this study. However, we require you to give your consent before we proceed to
		collect your data and store them in our database. The stored data will strictly be used for analysis purpose related to this study. <br/><br/></p>
		
		<p class="paragraph_font">You can return to our home page in case you changed your mind.</p>
';
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
