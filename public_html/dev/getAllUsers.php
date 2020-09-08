<?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// this file retrieves the entire users collection

require_once 'includes/functions.php';

$dbName = 'humanastro';		// database name
$collName = 'users';	// collection name


// set up the MongoDB connection

try {
	$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB

	$query = new MongoDB\Driver\Query( [] ); // get all records
	$cursor = $manager->executeQuery( $dbName.'.'.$collName, $query ); // execute and return a cursor

	// retrieve users	
	$userColl = array();
	foreach ($cursor as $user) {
		array_push($userColl, $user);
	}
	
	// output data
//	header('Content-Type: application/json');
//	echo json_encode($userColl);
	
	$json_data = json_encode($userColl,JSON_PRETTY_PRINT);


	header('Content-type: text/json');
	header('Content-Disposition: attachment; filename="allUsers.json"');
	header("Pragma: no-cache"); 
	header("Expires: 0");
	echo $json_data;

/*
header('Content-Type: application/octet-stream');
header('Content-disposition: attachment; filename="file.zip"');

// use popen to execute a unix command pipeline
// and grab the stdout as a php stream
// (you can use proc_open instead if you need to 
// control the input of the pipeline too)
//
$fp = popen('zip -r - file1 file2 file3', 'r');

// pick a bufsize that makes you happy (8192 has been suggested).
$bufsize = 8192;
$buff = '';
while( !feof($fp) ) {
   $buff = fread($fp, $bufsize);
   echo $buff;
}
pclose($fp);
*/
	
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