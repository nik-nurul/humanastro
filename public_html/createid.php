<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Create User Account</title>
<?php
include 'includes/head-base.html';
?>
    <script src="javascript/validatedemographic.js"></script>
</head>

<body id="demographicpage">

<?php
include 'includes/header.php';
?>

  <!-- division for user information form-->
  <section>

<?php
include 'includes/feeback-link.html';
?>


      <div id="content_paragraph">
        <h2 class="heading_font"> Create Account </h2>
        <hr class="heading"><br/>
          	<p class="paragraph_font">The questions provided on this page are intended to record minimal demographic
              information approved by ethics committee</p><br/><br/>

              <!--Form starts here-->
		<form class="paragraph_font" id="demoform" action="experience.php" method="post"> 
		<!--The action should be change to the URL where we want to save the data from the form-->
<?php

/* TO DO
	use GET user ID to resume sessions - but!
		do not allow user to see what was previously entered
		i.e. do not populate questions with previous responses
			(this would be a privacy breach)
			if user resumes a demographic page, they have to re-enter responses
	
	maybe create a resume.php page - enter the user ID as a GET variable
		the user record keeps a track of the page the user is up to
		- redirect to that page

	attempting to resume directly via other pages will cause user to jump to home page

	set conditions for creation of new User ID or re-use existing ID:
		from consent.php -
			if consent given = yes
				pass "consent" =true POST var to createid.php
		
		on createid.php
		if
			SESSION user ID is blank
			and
			consent=yes
				create new userid
				store questions
				store consent
				serve demographic questions
		
*/

	$dbName = 'humanastro';		// database name
	$Dcoll = 'demographic_Qs';	// question collection name
	$Ecoll = 'experience_Qs';	// experience collection name
	$Ucoll = 'users';			// user collection name

try {
	// Consent - "Yes" button was clicked - flag consent as true in session 
	// the "consent" propery in the user document in the database
	// is the canonical record of consent
	// - this must override other variables
	if ( isset($_POST["consent"]) and $_POST["consent"] == "true"){

		$_SESSION["consent"] = true;
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

		// create new user record
		$newUser = [
			"consent" => true,
			"demographic_data" => [],
			"experience_data" => [],
			"task_data" => []
		];

		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
		$userId = $bulk->insert($newUser);
		
		// pass the user ID on to the next page
		// via a session variable
		$_SESSION["userId"] = (string)$userId;
		// also via the HTML form
		echo '
				<input type="hidden" name="userId" value="'.(string)$userId.'"/>';	
		
		// create user ID object with which to ID the user record
		$_id = new MongoDB\BSON\ObjectID($userId);
		$filter = [ "_id" => $_id ]; // return only this user

	// read all documents in $Dcoll and $Ecoll
		$query = new MongoDB\Driver\Query([]); // [] means get all documents

		// write each demographic question document into the user document
		$rows = $manager->executeQuery($dbName.'.'.$Dcoll, $query);    	
		foreach ($rows as $row) {
			$bulk->update(
				$filter,
				[ '$push' => [ "demographic_data" => $row ]]
			);
		}

		// write each experience question document into the user document
		 $rows = $manager->executeQuery($dbName.'.'.$Ecoll, $query);    	
		foreach ($rows as $row) {
			$bulk->update(
				$filter,
				[ '$push' => [ "experience_data" => $row ]]
			);
		}
		
		$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);

	////////////
		
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
					<p><label for="'.$q_id.'">'.$question.'</label>
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

              				<br/>
              				<input class="bttn" id="inpbutton2" type= "reset" value="Reset Form"/>

              				<br/>
              				<br/>
              				<br/>

              				<input class="bttn" id="inpbutton" type="submit" value="Submit and Continue"/>
              				<button class="bttn" id="homebutt" type="button">Exit to Home</button>
              		</form>

      </div>
  </section>

<?php
include 'includes/footer.html';
?>

</body>
</html>
