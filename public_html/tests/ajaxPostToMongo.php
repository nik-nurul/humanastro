<?php
// based on example from: https://www.w3schools.com/xml/ajax_intro.asp
// and https://stackoverflow.com/questions/39341901/how-to-call-a-php-function-from-ajax

// trying to get CORS to work - it's not working yet!
// The idea is this page can only be called by the scripts from this site
// (humanastro.csproject.org)
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: https://humanastro.csproject.org/');
header('Access-Control-Allow-Methods: GET');

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// return true if the string is valid JSON
function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

// get the raw input data from the POST request to this page
$json = file_get_contents('php://input');

// display the raw input data for debug purposes
echo "</br>Data:</br>";
echo "<pre>";
print_r($json);
echo "</pre>";

// proceed with sending the JSON data to the MongoDB if the JSON is valid
if (isJson($json)){
	
	$receivedObj = json_decode($json);
	
	// only proceed if the JSON object contains key 'someData'
	if (property_exists($receivedObj,'someData')){

		// we only want the contents of the key 'someData' - throw away rest of JSON object from POST input
		$data = $receivedObj->someData;
		// might be a good idea to do some data sanitation here on the received data
		
		$dbName = 'test';
		$collName = 'ajaxTest';
		
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
		
		$bulk->insert($data);
		
		$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk);
		echo "<p>Inserted: ".$result->getInsertedCount()." documents</p>";
		
	} else {
		
		echo "<p>JSON does not contain someData</p>";
		
	}
} else {
	echo "<p>NOT JSON</p>";
}
?>
