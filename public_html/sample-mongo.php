<html>
<body>
<p>MongoDB Sample program
<p>
<?php
/*
used Mongo shell to insert a record in database 'test':
 > db.testColl.insertOne({"myproperty":{"facts":["sky is blue", "pope is catholic", "bear shits in the woods"]}})
 
 verified record was created:
 > db.testColl.find()
{ "_id" : ObjectId("5e9fb269790596da5d7784a1"), "myproperty" : { "facts" : [ "sky is blue", "pope is catholic", "bear shits in the woods" ] } }
 
*/

	// display debug messages
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	$dbName = 'test';
	$collName = 'testColl';
	
try {

    $mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// print database statistics
    $stats = new MongoDB\Driver\Command(["dbstats" => 1]);
    $res = $mng->executeCommand($dbName, $stats);
    
    $stats = current($res->toArray());

	echo '<p>Database Statistics';
	echo '<pre>'; var_dump($stats); echo '</pre>';

// list all databases 
    $listdatabases = new MongoDB\Driver\Command(["listDatabases" => 1]);
    $res = $mng->executeCommand("admin", $listdatabases);

    $databases = current($res->toArray());

	echo '<p>Database Names';
	echo '<p><pre>';
    foreach ($databases->databases as $el) {
        echo $el->name . "\n";
    }
	echo '</pre>';

// real_all in test.testColl collection
    $query = new MongoDB\Driver\Query([]); 
     
    $rows = $mng->executeQuery($dbName.'.'.$collName, $query);
    
	echo "<p>read_all on $dbName.$collName";
    echo '<p><pre>';
    foreach ($rows as $row) {
//        var_dump($row);
        print_r(json_encode($row).'<br>');
    }
    echo '</pre>';


} catch (MongoDB\Driver\Exception\Exception $e) {

    $filename = basename(__FILE__);
    
    echo "The $filename script has experienced an error.\n"; 
    echo "It failed with the following exception:\n";
    
    echo "Exception:", $e->getMessage(), "\n";
    echo "In file:", $e->getFile(), "\n";
    echo "On line:", $e->getLine(), "\n";       
}
   
?>
</body>
</html>
