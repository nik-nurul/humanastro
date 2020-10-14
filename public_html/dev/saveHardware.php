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
		&& property_exists($data,"hardware")
		&& property_exists($data->hardware,"screen_width")
		&& property_exists($data->hardware,"screen_height")
		&& property_exists($data->hardware,"has_webcam")
		&& property_exists($data->hardware,"os")
		&& property_exists($data->hardware,"browser")
		&& property_exists($data->hardware,"mobile")
	) {
		// data is valid
		
		// now we can upsert data!
		
		// create a BSON Object for the User ID
		// this uniquely identifies the document in the database
		$_id = new MongoDB\BSON\ObjectID( $data->userIdStr );
		
		// set up the MongoDB connection
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB
		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);

		// upsert means insert the data if the document doesn't exist,
		// or update the document with new data if the document already exists
		$options = [ "upsert" => true ];

		// push each element of the GazeDataArrphpay into the DB under the DB Array GazeData
		$bulk->update(
			[ "_id" => $_id ], // only operate on this particular user
			[ '$set' => $data->hardware ],
			$options // upsert = true
		);
		
		$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk); // set the result

	} else {
		echo 'missing properties in JSON!\n';
	}
} else {
	echo 'input is not JSON!\n';
}
?>
