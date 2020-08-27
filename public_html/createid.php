<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/functions.php';

// declare variables to page scope
$dbName = $Dcoll = $Ecoll = $Ucoll = '';
$userId = $_id = $manager = $bulk = '';

//require('includes/page-init.php');

// declare globals here until we get page-init.php working
	$dbName = 'humanastro';		// database name
	$Dcoll = 'demographic_Qs';	// question collection name
	$Ecoll = 'experience_Qs';	// experience collection name
	$Ucoll = 'users';			// user collection name

/* TO DO
	GET user ID to resume sessions - but!
		do not allow user to see what was previously entered
		i.e. do not populate questions with previous responses
			(this would be a privacy breach)
			if user resumes a demographic page, they have to re-enter responses
	
	php pages know if a session is being resumed if
		$_SESSION["userId"] is unset
		and
		$_GET["userId"] is set
		
	user should be able to bookmark any page with the GET userID variable set in the URL
		- php header in every page will test if $_SESSION["userId"]is set and if $_GET["userId"] is set
		-- check "consent" and "current_page" properties of user document in website
	
	If userId is not found, go to error page explaining the error then give link back to home page

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
		
*/


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
/*		
		// write the current page the user is on so the user can
		// resume an interrupted session
		$bulk->update(
			$filter,
			[ '$set' => [ "current_page" => basename(__FILE__) ] ]
		);
*/
		set_current_page($bulk, $_id, basename(__FILE__)); // write the name of the current page to the user record
		
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
		
		// now the user has been created, redirect to the demographic.php page
		// with the userID as a GET variable (in the URL)
		header('Location: demographic.php?userId='.$userId);
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
