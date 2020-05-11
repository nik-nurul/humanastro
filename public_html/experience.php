<!DOCTYPE html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<html lang="en">
<head>
  <title>Experience Question</title>
  <meta charset="utf-8" />
  <meta name="description" content="SEPA Project G3" />
  <meta name="keywords" content="HTML5" />
  <meta name="author" content="Fakhirah Shamsul, Michael Choi"  />
  
    <!--References to external CSS file-->
  <link rel="stylesheet" type="text/css" href="expsliders.css">
  
</head>

<body>

	<!--header section-->
	<header>
		<!--main header-->
		<h1 id="maintitle">Are Machines Better Than Humans (at Astronomy)?</h1>
		<!--sub header-->
		<h2 id="subtitle">About yourself</h2>
	</header><hr><br/>

	<!-- Section for questions -->
	<div class="container">
	  <h2>Questions regarding Visualisation Experience</h2>
	  <!-- description about the questions-->
	  <p>The following questions will help us understand more about your current experience with visual 
	  inspection of astronomical images.  For each question, please select the 
	  option that is the closest match.  When entering text information, 
	  please try to avoid including details that might allow yourself to be 
	  identified.  </p>
	</div>
	
	<div class="sliders">	 
	  <!-- qustion section. Used radio button-->
	  <p> Please select your answer </p><br/>
	  <form>
	  
<?php
	echo '<p>$_POST: <br><pre>';
	var_dump($_POST);
	echo '</pre>';

	$dbName = 'humanastro';		// database name
	$Qcoll = 'experience_Qs';	// experience collection name
	$Ucoll = 'users';			// user collection name

	$userId = $_POST["userId"]; // extract userID string
	unset($_POST["userId"]); // remove userID from POST array
	  
try {
	$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

	// add the demographic answers to the user record
	$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);

	// create user ID object with which to ID the user record
	$_id = new MongoDB\BSON\ObjectID($userId);


// mongo shell - working example of how to set the "answer" property inside the question element of the "demographic_data" array
// db.users.updateOne({"_id": ObjectId("5eb8b54323e858761f1ca452"), "demographic_data": {$elemMatch: {"q_id":"astarea"}}},{$set: {"demographic_data.$.answer":"foo"}})

	// iterate through each demographic answer, add answer to question element in user record
    foreach ($_POST as $q_id => $value) {
		// find specific question in this user document
		$filter = [
			"_id" => $_id, // this is the document or object (user, in this case) ID
			"demographic_data" => [ '$elemMatch' => [ "q_id" => $q_id ] ] // demographic_data contains questions and answers
		];
		// set the answer value for this question
		$bulk->update(
			$filter,
			[ '$set' => [ "demographic_data.$.answer" => $value ]]
		);
    }	
	// execute writing all the demographic answers
	$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);
	
	// set up a query to retrieve questions from the database
	$filter = []; // return all documents (filter is empty)
	$options = ['sort' => ['question_num' => 1]]; // sort the results based on question_num; 1 means ascending and -1 means descending
	$query = new MongoDB\Driver\Query($filter,$options); // create a query object with the above parameters
	
	// the cursor allows us to iterate over the query results
	$cursor = $manager->executeQuery($dbName.'.'.$Qcoll, $query); // execute and return a cursor
	
	// iterate over each question document
	foreach ($cursor as $q){
		$question = $q->question; // the full text of the question
		$q_id = $q->q_id;		// the abbreviated question identifier
		$q_min = $q->q_min;		// mininum value allowed
		$q_max = $q->q_max;		// maximum value allowed
		$q_steps = $q->q_steps;	// steps in likert scale
		$q_value = $q->q_value;	// specifies default value
		
		echo '<p>'.$question.'  
			<div class="range">
				<input type="range" min="'.$q_min.'" max="'.$q_max.'" steps="'.$q_steps.'" value="'.$q_value.'">
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
	echo '</form>
	</div>
	</br>
	</br>';
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
	  <!-- section for buttons  -->
	  <div>
		<!-- this button will redirect to homepage -->
		<a href="home.html"><input id="quitBttn" type= "reset" value="Exit to Home"/></a>
		<!-- this button will redirect to webcam calibration page-->
		<a href="calibration.html"><input id="submitBttn" type= "submit" value="Submit and continue"/></a>
	  </div>
	  <br/>
	

</body>
</html>
