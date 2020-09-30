<?php
	session_start(); // used to pass userId from page to page
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Astronomy Test - Demographic Questions</title>
<?php
	require_once 'includes/head-base.html';
?>
    <!-- check form requirements/validation -->
    <script src="javascript/validatedemographic.js"></script>
    <!-- for navigation bar -->
    <script src="javascript/style.js"></script>
</head>

<body id="demographicpage">

<?php
	require_once 'includes/header.html';
?>

  <!-- division for user information form-->
  <section>

<?php
	require_once 'includes/feeback-link.php';
?>

	<div id="content_paragraph">
		<h2 class="heading_font"> Demographic Information </h2>
        <hr class="heading"><br/>
			<p class="paragraph_font">The questions provided on this page are intended to record minimal demographic
				information approved by ethics committee.</p>

			<p class="paragraph_font">For you to remain anonymous it is your responsibility not to input any identifying details here. Do not include your name, email address or any other personally-identifying data.</p>

			<br/><br/>
<?php
	require_once 'includes/functions.php';

	$dbName = 'humanastro';		// database name
	$Ucoll = 'users';			// user collection name

try {
	// Consent - "Yes" button was clicked - flag consent as true in session
	// the "consent" propery in the user document in the database
	// is the canonical record of consent
	// - this must override other variables
	if ( isset($_SESSION["consent"]) and $_SESSION["consent"]){

		$_SESSION["consent"] = true;
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);

		$userId = sanitise_input($_GET["userId"]); // defend against malicious GET requests

		// pass the user ID on to the next page
		// via a session variable
		$_SESSION["userId"] = (string)$userId;

		// create user ID object with which to ID the user record
		$_id = new MongoDB\BSON\ObjectID($userId);
		$filter = [ "_id" => $_id ]; // return only this user

/*
		// write the current page the user is on so the user can
		// resume an interrupted session
		$bulk->update(
			$filter,
			[ '$set' => [ "current_page" => basename(__FILE__) ] ]
		);
*/
		set_current_page($bulk, $_id, basename(__FILE__)); // write the name of the current page to the user record

		//delete answerdate
		unset($bulk->{"answerdate"});
		// write the current_page data to the user record
		$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);

	////////////
	// start the demographic form - insert the User ID as a GET variable to the next page
		echo '
			  <form class="paragraph_font" id="demoform" action="experience.php?userId='.$userId.'" method="post">
';

		// set up a query to retrieve questions from the new user record in the database
		$options = ['sort' => ['question_num' => 1]]; // sort the results based on question_num
		$query = new MongoDB\Driver\Query($filter,$options); // create a query object with the above parameters

		$cursor = $manager->executeQuery($dbName.'.'.$Ucoll, $query); // execute and return a cursor
		// the cursor allows us to iterate over the query results

	// retrieve user
		// there should only be one result - this makes sure we get the only result
		$iterator = new IteratorIterator($cursor); // generate an iterator from the cursor
		$iterator->rewind(); // make sure the iterator is at the first element (head)
		$userDoc = $iterator->current(); // retrieve the user document from the iterator
		$demographic_Qs = $userDoc->demographic_data;

		foreach ($demographic_Qs as $q){
			$question = $q->question; // the full text of the question
			$q_id = $q->q_id;		// the abbreviated question identifier
			echo '
					<!--'.$question.'-->';

			// handle the different question types - radio or dropdown
			// Chris has requested for all of the questions to be in dropdown format.
			//Thus, case:radio actually shall be removed, but as for now will just keep it here in case there is any future changes
			switch ($q->q_type){

				case "radio":
					echo '
					<p>'.$question.':
					<br/>';
					foreach ($q->options as $optObj){
						$o = $optObj->option;	// the full text of the question option
						$o_id = $optObj->opt_id; // the abbreviated option identifier
						echo'
						<input type="radio" id="'.$o_id.'" name="'.$q_id.'" value="'.$o_id.'" required="required"/>
							<label for="'.$o_id.'">'.$o.'</label>
						<br/>';
						// handle questions that allow a freetext answer
						if ( isset($optObj->freetext_answer) && $optObj->freetext_answer ){
							echo'
							<label for="'.$optObj->freetext_id.'">'.$optObj->freetext_desc.'</label>
								<input class="resize" type="text" name= "'.$optObj->freetext_id.'" id="'.$optObj->freetext_id.'" maxlength="'.$optObj->freetext_length.'" size="'.$optObj->freetext_length.'"/>';
						}
					}
					echo '
					<p>
';
					break;

				case "dropdown":
					echo '
					<br/><p><label for="'.$q_id.'">'.$question.'</label>
					<br/>
						<select class="selectsize" name="'.$q_id.'" id="'.$q_id.'" required="required">
							<option value="">Please Select</option>';
					foreach ($q->options as $optObj){
						$o = $optObj->option;	// the full text of the question option
						$o_id = $optObj->opt_id; // the abbreviated option identifier
						echo'
							<option id="'.$o_id.'" value="'.$o_id.'">'.$o.'</option>';
					}
					echo '
						</select>
					<p>
';
					break;

				default:
					echo '\n				<p>Error! Question Type not specified\n';
			}
		}

 // end if consent == true
	} else {
		$_SESSION["consent"] = false;

		echo '
		<p>Consent was not given</p>
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
?>


							<div class="container">
								<div class="center">
									<input class="bttnsubmit" id="inpbutton1" type="submit" value="Submit and Continue"/>
								</div>
							</div>
              				<br/>
							<button class="bttn" id="homebutt" type="button">Exit to Home</button>
              				<input class="bttn" id="inpbutton2" type= "reset" value="Reset Form"/>


              		</form>

      </div>
  </section>

<?php
	require_once 'includes/footer.html';
?>

</body>
</html>
