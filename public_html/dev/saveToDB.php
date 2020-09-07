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
		&& property_exists($data,"GazeDataArray")
	) {
		// data is valid
		
		// now we can upsert data!
		
		// create a BSON Object for the User ID
		// this uniquely identifies the document in the database
		$_id = new MongoDB\BSON\ObjectID( $data->userIdStr );
		
		$task_num = $data->task_num;
		$subtask_num = $data->subtask_num;
		
		// get the GazeDataArray out of the data object
		$GazeDataArray = new \stdClass();
		$GazeDataArray = $data->GazeDataArray;
		
		// set up the MongoDB connection
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB
		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
		
		// only operate on this user ID
//			$filter = [ "_id" => $_id ];

		// mongo command:
		// { "_id": ObjectId("adea2d97600941c7d3580e2e"), "task_data.task_num":1, "task_data.subtasks": {$elemMatch:{ "subtask_num": 1 }}}
		// find specific question in this user document
		$filter = [
			"_id" => $_id, // this is the document or object (user, in this case) ID
							// task_data contains tasks and results
			"task_data.task_num" => $task_num,
			"task_data.subtasks" => [ '$elemMatch' => [ "subtask_num" => $subtask_num ] ] 
		];
		
		// upsert means insert the data if the document doesn't exist,
		// or update the document with new data if the document already exists
		$options = [ "upsert" => true ];
		
		// push each element of the GazeDataArray into the DB under the DB Array GazeData
		$bulk->update(
			$filter, // only operate on this particular user
			[ '$push' => [ // append this data to an array already in the DB document
				"task_data.$.subtasks.".($subtask_num-1).".GazeData" => [ 
					'$each' => $GazeDataArray // act on each element of the GazeDataArray separately
					]	// i.e. push each element to the DB separately,
				]		// don't push the $GazeDataArray as a single element to the DB
			],
			$options // upsert = true
		);
		
		$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk); // do the upsert
		

	} else {
		echo 'missing properties in JSON!\n';
//		echo $jsonStr;
	}
} else {
	echo 'input is not JSON!\n';
}
?>

