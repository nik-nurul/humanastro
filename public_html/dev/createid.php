<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Creating ID</title>
    <script src="script.js"></script>
  </head>

<?php

require_once 'includes/functions.php';

// declare variables to page scope
$dbName = $Dcoll = $Ecoll = $Ucoll = '';
$userId = $_id = $manager = $bulk = '';

// declare globals here until we get page-init.php working
	$dbName = 'humanastro';		// database name
	$Dcoll = 'demographic_Qs';	// question collection name
	$Ecoll = 'experience_Qs';	// experience collection name
	$Tcoll = 'task_data';		// task_data collection name
	$Ucoll = 'users';			// user collection name

try {
	// Consent - "Yes" button was clicked - flag consent as true in session 
	// the "consent" propery in the user document in the database
	// is the canonical record of consent
	// - this must override other variables
	if ( isset($_POST["consent"]) and sanitise_input($_POST["consent"]) == "true"){

		$_SESSION["consent"] = true;
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
		
		// create randomised user ID
		$_id = new MongoDB\BSON\ObjectId(bin2hex(random_bytes(12)));

		// create new user record
		$newUser = [
			"_id" => $_id,
			"consent" => true,
			"demographic_data" => [],
			"experience_data" => [],
			"task_data" => []
		];

		$userId = $bulk->insert($newUser);
		
		// pass the user ID on to the next page
		// via a session variable
		$_SESSION["userId"] = (string)$userId;
		
		// create user ID object with which to ID the user record
		$_id = new MongoDB\BSON\ObjectID($userId);
		$filter = [ "_id" => $_id ]; // return only this user
		
		set_current_page($bulk, $_id, basename(__FILE__)); // write the name of the current page to the user record
		
	// read all documents in $Dcoll, $Ecoll
		$q_options = ['sort' => ['question_num' => 1]]; // sort the results based on question_num
		$query = new MongoDB\Driver\Query([],$q_options); // [] means get all documents

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
		
		// write data for each task document into the user document
		$t_options = ['sort' => ['task_num' => 1]]; // sort the results based task number
		$query = new MongoDB\Driver\Query([],$t_options); // [] means get all documents
		 $rows = $manager->executeQuery($dbName.'.'.$Tcoll, $query);    	
		foreach ($rows as $row) {
			$bulk->update(
				$filter,
				[ '$push' => [ "task_data" => $row ]]
			);
		}
		
		$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);
		
		// now the user has been created, redirect to the demographic.php page
		// with the userID as a GET variable (in the URL)
//		header('Location: demographic.php?userId='.$userId);
		header('Location: demographic.php');
		exit();

		
 // end if consent == true	
	} else {
		// TO DO - redirect to an error page explaining no consent
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
</html>