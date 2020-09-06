<?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// this file retrieves the task_data object from the given MongoDB userId record given as a GET parameter

require_once 'includes/functions.php';

$dbName = 'humanastro';		// database name
$collName = 'users';	// collection name

$userIdStr = sanitise_input($_GET["userId"]); // defend against malicious GET requests

$_id = new MongoDB\BSON\ObjectID( $userIdStr );

// set up the MongoDB connection

try {
	$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

	// set up a query to retrieve tasks from the user record in the database
	$filter = [ "_id" => $_id ]; // just get the user whole user record
	$query = new MongoDB\Driver\Query( $filter ); // create a query object with the above parameters
	$cursor = $manager->executeQuery( $dbName.'.'.$collName, $query ); // execute and return a cursor

	// retrieve user	
	// there should only be one result - this makes sure we get the only result
	$iterator = new IteratorIterator($cursor); // generate an iterator from the cursor
	$iterator->rewind(); // make sure the iterator is at the first element (head)
	$userDoc = $iterator->current(); // retrieve the user document from the iterator

	// output data
	header('Content-Type: application/json');
	echo json_encode($userDoc->task_data);
	
// exception handling for the database connection	
} catch (MongoDB\Driver\Exception\Exception $e) {

	$filename = basename(__FILE__);
	
	echo "<pre>The $filename script has experienced an error.\n";
	echo "It failed with the following exception:\n";
	
	echo "Exception:", $e->getMessage(), "\n";
	echo "In file:", $e->getFile(), "\n";
	echo "On line:", $e->getLine(), "\n</pre>";       
}
?>