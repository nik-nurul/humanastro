<?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbName = 'humanastro';		// database name
$collName = 'users';	// collection name

// output text of this PHP will be logged to the JS console
// just echo messages and they will be seen on the console log

// return true if the string is valid JSON
function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

// get the raw input data from the POST request to this page
$jsonStr = file_get_contents('php://input');

//echo $jsonStr;

// check if data is valid
// could do a session ID check here too
if (isJson($jsonStr)){ // is it JSON?
	$data = json_decode($jsonStr);
	if ( // check JSON schema
		   property_exists($data,"userIdStr")
		&& property_exists($data,"task_num")
		&& property_exists($data,"subtask_num")
		&& property_exists($data,"subtask_result")
	) {
		// data is valid
		
		// now we can upsert data!
		
		// create a BSON Object for the User ID
		// this uniquely identifies the document in the database
		$_id = new MongoDB\BSON\ObjectID( $data->userIdStr );
		
		$task_num = $data->task_num;
		$subtask_num = $data->subtask_num;
		
		$subtask_result = $data->subtask_result;
		
		// set up the MongoDB connection
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB
		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);

		// insert the result of the subtask
		$bulk->insert(
			[ "_id" => $_id ], // only operate on this particular user
			[ '$set' => [
				"task_data.".$task_num.".subtasks.".$subtask_num.".subtask_result" => $subtask_result
				]
			]
		);
		
		$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk); // set the result
		

	} else {
		echo 'missing properties in JSON!\n';
	}
} else {
	echo 'input is not JSON!\n';
}
?>
