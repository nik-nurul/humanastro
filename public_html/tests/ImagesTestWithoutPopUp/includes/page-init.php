<?php
/*

	Non-user-pages (pass through userId if given, no redirects)
		index.php - 
				-- no user ID = "take test" buttons
				-- user ID = "resume test" and "start new test" buttons
				---- "start new test" == "take the test"
				----- "resume test" redirects to 
		consent.php - redirects to current_page if consent = true
		contact, about - pass-through, have resume buttons at bottom if userId active
						- resume buttons direct user to current_page
			
		
	Is URL index.php or consent.php

	If userId not in DB -- go to error page


	IF
								|	GET userId unset		|	GET  userId set
									
		SESSION userId set*		|	carry on with page		|	carry on with page
		
		SESSION userId unset	|	present "Take The Test"	|	present "Take new test" >> consent.php
				(on index.php)	|							|	connectDB, if user exists, get current_page, present "Resume test" >> "current_page"
																
			

		SESSION userId unset	|	redirect to index.php	|	connect DB,
										(if url isn't		|	set SESSION userId,
										index.php)			|	retrieve "current_page" from DB for that user
															|	redirect to that page
		
		* if SESSION userId set then $userId in page derives from SESSION
		

	IF carry on:
		connect DB
		write current_page
	
		
*/

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// init variables
$dbName = 'humanastro';		// database name
$Dcoll = 'demographic_Qs';	// question collection name
$Ecoll = 'experience_Qs';	// experience collection name
$Ucoll = 'users';			// user collection name

// what to do on page here:

// try catch here

$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); // connect to the Mongo DB
$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);

$userId = '';
$_id = new MongoDB\BSON\ObjectID($userId);
$filter = [ "_id" => $_id ]; // return only this user

// write the current page the user is on so the user can
// resume an interrupted session
$bulk->update(
	$filter,
	[ '$set' => [ "current_page" => basename($_SERVER['SCRIPT_FILENAME']) ] ]
);
// write the current_page data to the user record
$result = $manager->executeBulkWrite($dbName.'.'.$Ucoll, $bulk);


?>