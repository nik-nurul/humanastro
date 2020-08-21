<?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbName = 'test';		// database name
$collName = 'testGazeData';	// collection name

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
	if (property_exists($data,"userIdStr")) { // does it contain a userIdStr property?
		if (property_exists($data,"GazeDataArray")) { // does it contain a GazeDataArray property?
			// data is valid
			
			// now we can upsert data!
			
			// create a BSON Object for the User ID
			// this uniquely identifies the document in the database
			$_id = new MongoDB\BSON\ObjectID( $data->userIdStr );
			
			// get the GazeDataArray out of the data object
			$GazeDataArray = new \stdClass();
			$GazeDataArray = $data->GazeDataArray;
			
			// set up the MongoDB connection
			$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB
			$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
			
			// only operate on this user ID
			$filter = [ "_id" => $_id ];
			
			// upsert means insert the data if the document doesn't exist,
			// or update the document with new data if the document already exists
			$options = [ "upsert" => true ];
			
			// push each element of the GazeDataArray into the DB under the DB Array GazeData
			
			$bulk->update(
				$filter, // only operate on this particular user
				[ '$push' => [ // append this data to an array already in the DB document
					"GazeData" => [ 
						'$each' => $GazeDataArray // act on each element of the GazeDataArray separately
						]	// i.e. push each element to the DB separately,
					]		// don't push the $GazeDataArray as a single element to the DB
				],
				$options // upsert = true
			);
			
			
			$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk); // do the upsert
			
			echo "Modified: ".$result->getModifiedCount()." documents\n";			

		} else {
			echo 'no GazeDataArray in JSON!\n';
		}
	} else {
		echo 'no User ID in JSON!\n';
	}
} else {
	echo 'input is not JSON!\n';
}
?>

