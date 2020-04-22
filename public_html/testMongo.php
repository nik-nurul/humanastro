<html>
<body>
MongoDB Test
<?php
	// display debug messages
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

//	$mongo = new MongoDB\Client('mongodb://127.0.0.1:27017');
//	$dbs = $mongo->listDatabases();


	// https://stackoverflow.com/questions/40971613/class-mongodb-client-not-found-mongodb-extension-installed
	
	// https://www.php.net/manual/en/class.mongodb-driver-manager.php
	
	// Manager Class
	$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");


	// Query Class
	$query = new MongoDB\Driver\Query(array('age' => 30));

	// Output of the executeQuery will be object of MongoDB\Driver\Cursor class
	$cursor = $manager->executeQuery('testDb.testColl', $query);

	// Convert cursor to Array and print result
//	print_r($cursor->toArray());

	
	echo '<pre>'; var_dump($manager); echo '</pre>'

?>
</body>
</html>