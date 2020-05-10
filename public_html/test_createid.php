<!DOCTYPE html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="SWE40001 Group 3" />
	<meta name="keywords" content="PHP " />
	<meta name="author" content="Group 3" />
	<title>Create User ID Page - Astronomy Image Test</title>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<!--References to external Javascript file-->
	<script src="javascript/validatedemographic.js"></script>	
</head>

<body id="demographicpage">
	
	<!--header section-->
	<header>
		<!--main header-->
		<h1 id="maintitle">Are Machines Better Than Humans (at Astronomy)?</h1>
		<!--sub header-->
		<h2 id="subtitle">Astronomy Image Test</h2>
	</header>
	
	<!--division for content-->
	<div id="content">
		<p>The questions provided on this page are intended to record minimal demographic information approved by ethics committee</p>

		<!--Form starts here-->
		<form id="demoform" action="experience.php"> 
		<!--The action should be change to the URL where we want to save the data from the form-->
<?php
	$dbName = 'humanastro';		// database name
	$Dcoll = 'demographic_Qs';	// question collection name
	$Ecoll = 'experience_Qs';	// experience collection name
	$Ucoll = 'users';			// user collection name

try {
	$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

	// create new user record

	$newUser = [
		"demographic_data" => [],
		"experience_data" => [],
		"task_data" => []
	];
	
	$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
	$userId = $bulk->insert($newUser);
	
	// create user ID object with which to ID the user record
	$_id = new MongoDB\BSON\ObjectID($userId);
	$filter = [ "_id" => $_id ]; // return only this user

// read all documents in $Dcoll
    $query = new MongoDB\Driver\Query([]); // [] means get all documents
    $rows = $manager->executeQuery($dbName.'.'.$Dcoll, $query);
    	
	// write each question document into the user document
    foreach ($rows as $row) {
		$bulk->update(
			$filter,
			[ '$push' => [ "demographic_data" => $row ]]
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
	$iterator = new IteratorIterator($cursor);
	$iterator->rewind();
	$userDoc = $iterator->current();
	$demographic_Qs = $userDoc->demographic_data;
	
//	echo '\n<pre>\n';
//	var_dump($demographic_Qs);
//	echo '\n</pre>\n';
	
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
							<input class="resize" type="text" name= "'.$optObj->freetext_name.'" id="'.$optObj->freetext_id.'" maxlength="'.$optObj->freetext_length.'" size="'.$optObj->freetext_length.'"/>';
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
					<select class="selectsize" name="'.$question.'" id="'.$q_id.'" required="required">
						<option value="">Please Select</option>';
				foreach ($q->options as $optObj){
					$o = $optObj->option;	// the full text of the question option
					$o_id = $optObj->opt_id; // the abbreviated option identifier
					echo'
						<option id="'.$o_id.'" value="'.$o.'">'.$o.'</option>';
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
				<input id="inpbutton2" type= "reset" value="Reset Form"/>
				
				<br/>
				<br/>
				<br/>
				
				<button id="homebutt" type="button">Exit to Home</button>
				<input id="inpbutton" type="submit" value="Submit and Continue"/>
		</form>
			
	</div>
	
	<br/>
	<!--footer section-->
	<footer>
		<p>Software Engineering Project A<p>
		<p>&#169; Swinburne University of Technology</p>
	</footer>

</body>
</html>
