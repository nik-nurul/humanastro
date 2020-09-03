<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Experience Questions</title>
<?php
require_once 'includes/head-base.html';
?>
	<link rel="stylesheet" type="text/css" href="styles/expsliders.css">
</head>

<body id="experiencepage">
	
<?php
require_once 'includes/header.html';
?>

  <!-- division for user experience form-->
  <section>  
<?php
require_once 'includes/feeback-link.php';
?>
	<div id="content_paragraph">
	
		<!--Page header-->
		<h2 class="heading_font">About Yourself</h2>
		<hr class="heading"><br/>
		<!-- Content paragraph-->
		<p class="paragraph_font">The following questions will help us understand more about your current experience with visual inspection of astronomical images. For each question, please select the option that is the closest match.</p>
		
		<p class="paragraph_font">This survey page stores your responses, the date and time of your response.</p>
		
		<br/>
		<br/>
<?php
	require_once 'includes/functions.php';

	// don't do anything if consent is not true
	if ( isset( $_SESSION["consent"] ) and $_SESSION["consent"] ) {

		// update user record with demographic answers

		$dbName = 'humanastro';		// database name
		$Ucoll = 'users';			// user collection name

		$userId = sanitise_input($_GET["userId"]); // defend against malicious GET requests
		  
		try {
			$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

			// **** add the demographic answers to the user record ****
			$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);

			// create user ID object with which to ID the user record
			$_id = new MongoDB\BSON\ObjectID($userId);


			// mongo shell - working example of how to set the "answer" property inside the question element of the "demographic_data" array
			// db.users.updateOne({"_id": ObjectId("5eb8b54323e858761f1ca452"), "demographic_data": {$elemMatch: {"q_id":"astarea"}}},{$set: {"demographic_data.$.answer":"foo"}})

			// OVERVIEW - Algorithm:
			// * iterate through each demographic question in user record
			// * get the question ID
			// * match that to the question ID in the $_POST array
			// * add the answer from the $_POST array to the demographic question in the user record
			// -- this method is used instead of iterating over the $_POST array as that is less secure
			// -- iterating over submitted data risks writing corrupted or malicious user input to the DB

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

			// special handling for the "age" question
			// - under 18s have consent to record data revoked
			$isAdult = false;

			// is the user an adult?
			if ( isset($_POST["Age"] ) and (
					(in_array($_POST["Age"], array("18-25", "26-35", "36-45", ">45", "agenot") ) )
				)
			) {
				$date = date("Y-m-d H:i:s\Z"); // date/time string to store with answer
				// iterate through the demographic questions to set their answers
				foreach ($userDoc->demographic_data as $q){
					$q_id = $q->q_id;		// the abbreviated question identifier
					
					// set the answer, if it exists in the $_POST array
					if (isset($_POST[$q_id])) {	
						
						$answer = sanitise_input($_POST[$q_id]); // sanitise all form data, just in case

						// find specific question in this user document
						$u_filter = [
							"_id" => $_id, // this is the document or object (user, in this case) ID
							"demographic_data" => [ '$elemMatch' => [ "q_id" => $q_id ] ] // demographic_data contains questions and answers
						];
// TO DO - make this more generic
// currently only works for the specific case of gender description
// sould actually act on "freetext_id" or "freetext_name"
// -- work out how to set up the value "key" for freetext items
						// handle self-described Gender
						//	- change answer to freetext gender description
						if ( $q_id == "Gender" and $answer == "sd" and isset($_POST["gendesc"]) )
							$answer = sanitise_input($_POST["gendesc"]); // sanitise the freetext gender answer
						// set the answer value for this question
						$bulk->update(
							$u_filter,
							[ '$set' => [ "demographic_data.$.answer" => $answer ]]
						);
						// record the date of the answer
						$bulk->update(
							$u_filter,
							[ '$set' => [ "demographic_data.$.answerdate" => $date ]]
						);
					}
				}
		/*		
				// write the current page the user is on so the user can
				// resume an interrupted session
				$bulk->update(
					$filter,
					[ '$set' => [ "current_page" => basename(__FILE__) ] ]
				);
		*/
				set_current_page($bulk, $_id, basename(__FILE__)); // write the name of the current page to the user record

				// execute writing all the demographic answers (if the user is an adult)
				$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);

				// ****** output experience questions ***********
				// we already have the user document from the above -- $userDoc
				// which contains the experience questions

				// start form here
				echo '
			  <form class="paragraph_font" id="expform" action="calibration.php?userId='.$userId.'" method="post"> 
';
				// iterate over each experience question
				foreach ($userDoc->experience_data as $q){
					$question = $q->question; // the full text of the question
					$q_id = $q->q_id;		// the abbreviated question identifier
					$q_min = $q->q_min;		// mininum value allowed
					$q_max = $q->q_max;		// maximum value allowed
					$q_steps = $q->q_steps;	// steps in likert scale
					$q_value = $q->q_value;	// specifies default value
					
					echo '<p>'.$question.'  
						<div class="range">
							<input type="range" name="'.$q_id.'" id="'.$q_id.'" min="'.$q_min.'" max="'.$q_max.'" steps="'.$q_steps.'" value="'.$q_value.'">
						</div>
							<ul class="range-labels">';

					foreach ($q->options as $optObj){
						$o = $optObj->option;	// the full text of the question option
						echo '<li>'.$o.'</li>';
					}
					echo '</ul>
					</p>
					<br/>
					<br/>';
				}
				echo '
				
				</br>
				</br>';
			
			// user is not an adult
			} else {
				$_SESSION["consent"] = false;
				// set the user consent to false in the DB
				$bulk->update(
					[ "_id" => $_id ],
					[ '$set' => [ "consent" => false ]]
				);
				$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);
				
				echo '
				<p>Your cannot participate in this research if you are not an adult</p>
				<p>Your consent to have your details recorded has been withdrawn</p>
				<p>Do something!</p>
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
	// end of if consent is true
	} else {
		// consent was false
		echo '
		<p>Consent was not given</p>
		<p>Do something!</p>
';
	}		
?>
		  <!-- section for buttons  -->
		  <div>
			<div class="container">
				<div class="center">
				<!-- this button will redirect to webcam calibration page-->
					<a href="take_test2.php"><input class="bttnsubmit" id="inpbutton" type="submit" value="Submit and Continue"/>
				</div>
			</div>
			
			<br/>
            
			<!-- this button will redirect to homepage -->
			<a href="index.php"><input class="bttn" id="quitBttn" type= "reset" value="Exit to Home"/></a>
		  </div>
		  <br/>
		</form>
		
	    <br/>
	</div>
  </section>

<?php
require_once 'includes/footer.html';
?>

</body>
</html>
