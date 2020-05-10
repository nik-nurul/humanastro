<html>
<body>
<p><h1>MongoDB Sample program</h1>
<p>
<?php

	// this library isn't mandatory and the below example doesn't use it
	// You might find it easier to use this library instead of the low-level API
	// https://docs.mongodb.com/php-library/current/tutorial/
	require_once "../vendor/autoload.php";

	
	// a simple function to generate random strings
	include './readable_random_string.php';

	// display debug messages
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	$dbName = 'test';
	$collName = 'testColl';
	
try {

    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

	// menu of actions
	
	echo "<p><h2>Actions</h2>";
    echo '<form method="post" action="./sample-mongo.php">';
    echo '<input type="submit" name="action" value="Read" />';
    echo '<input type="submit" name="action" value="CreateCollection" />';
    echo '<input type="submit" name="action" value="BulkWriteRandom" />';
    echo '<input type="submit" name="action" value="DeleteOne" />';
    echo '<input type="submit" name="action" value="DeleteAll" />';
    echo '<input type="submit" name="action" value="DropColl" />';
	echo '<input type="text"   name="CollToDrop" id="CollToDrop" size="10"/>';
    echo '</form><p>';
	

	// execute selected actions

	if (isset($_POST["action"])){
		$limit = 1; // for the delete document - 1 = only delete 1, 0 = delete all
		switch ($_POST["action"]) {
			
			// create a new collection with a random name in the db test 
  			case "CreateCollection":
				$newCollName = readable_random_string();
				$cmd = new MongoDB\Driver\Command(["create" => $newCollName]);
				$result = $manager->executeCommand($dbName, $cmd);
				echo "<p> created new collection <pre>$newCollName</pre>";
  			    break;
				
			// write between 1 to 6 documents to the db test in collection testColl
  			case "BulkWriteRandom":
				$numNewDocs = mt_rand(1,6);
				$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
				echo "<br>Just Inserted: <pre>";
				for ( $i = 0; $i < $numNewDocs; $i++ ){
					$randomWord = readable_random_string();
					$randomNumber = mt_rand(100000,999999); 
					// create a new document to be inserted
					$newObj = [
						"randomWord" => $randomWord,
						"randomNumber" => $randomNumber
						];
					$bulk->insert($newObj); // insert the document into the BulkWrite queue
					echo json_encode($newObj)."<br>";
				}
				echo "</pre>";
				// execute the BulkWrite queue
				$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk);
				echo "<p><p>Inserted: ".$result->getInsertedCount()." documents<p>";
			//	var_dump($result->getUpsertedIds());
				
  			    break;
				
			// delete a document that contains the property "randomWord"
  			case "DeleteAll":
				$limit = 0;
  			case "DeleteOne":
				$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
				$bulk->delete(
					["randomWord"=>['$exists'=>1]],
					["limit" => $limit] // limit = 1 means only delete one document
					); 
				$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk);
				echo "<p><p>Deleted: ".$result->getDeletedCount()." documents<p>";
				break;
			
			// delete a document from the list of documents by _id
			case "DeleteDoc":
				if (isset($_POST["DocToDelete"])){
					// create MongoDB BSON ID Object from ID given
					$_id = new MongoDB\BSON\ObjectID($_POST["DocToDelete"]);
					$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
					$bulk->delete(
						["_id"=> $_id ],
						["limit" => 1] // limit = 1 means only delete one document
					); 

					$result = $manager->executeBulkWrite($dbName.'.'.$collName, $bulk);
					echo "<p><p>Deleted: ".$result->getDeletedCount()." documents<p>";
				} else {
					echo "<p><p>Nothing to delete!<p>";
				}
				break;
			
			// drop a collection given in the textbox
			case "DropColl":
			
				if (isset($_POST["CollToDrop"]) && $_POST["CollToDrop"] != "" ) {
					$collToDrop = $_POST["CollToDrop"];
					
					if ($collToDrop != "testColl"){
						$cmd = new MongoDB\Driver\Command(["drop" => $collToDrop]);
						$success = true;
						try {
							$result = $manager->executeCommand($dbName, $cmd);
						} catch (MongoDB\Driver\Exception\Exception $e) {
							echo "<p>Error trying to delete collection: $collToDrop";
							echo "<br>Message: ".$e->getMessage()."<p>";
							$success = false;
						}
						if ($success) echo "<p> Deleted collection: <pre>$collToDrop</pre>";
						
					} else {
						// cannot delete the collection testColl
						echo "<p>I'm sorry, Dave. I'm afraid I can't do that";					
					}
				} else {
					echo "<p>Nothing to delete!";					
				}
				break;
				
  			case "Read":
			default:
  		}
    }


// list all databases 
    $cmd = new MongoDB\Driver\Command(["listDatabases" => 1]);
    $result = $manager->executeCommand("admin", $cmd);

    $resultArray = current($result->toArray());

	echo '<p><h2>Database Names</h2>';
	echo '<p><pre>';
    foreach ($resultArray->databases as $el) {
        echo $el->name . "\n";
    }
	echo '</pre>';

// list collections in test
    $cmd = new MongoDB\Driver\Command(["listCollections" => 1, "nameOnly" => 1]);
    $result = $manager->executeCommand($dbName, $cmd);

    $resultArray = $result->toArray();

	echo "<h2>Collection Names in db $dbName</h2>";
	echo '<p><pre><table border=1>';
    foreach ($resultArray as $el) {
		$name = $el->name;
        echo '<tr><td style="text-align:left">';
		if ($name != "testColl"){
			echo '<form method="post" action="./sample-mongo.php">';
			echo "<input type=\"hidden\" name=\"action\" value=\"DropColl\"/>";	
			echo "<button type=\"submit\" name=\"CollToDrop\" value=\"$name\"/>Drop</button>";	
			echo "</form>";
		}
		echo "</td><td style=\"text-align:left\">$name</td></tr>";
    }
	echo '</table></pre>';



// read all documents in test.testColl collection
    $query = new MongoDB\Driver\Query([]); // [] means get all documents
    $rows = $manager->executeQuery($dbName.'.'.$collName, $query);
    
	echo "<p><h2>read_all on $dbName.$collName</h2>";
    echo '<p><pre><table border=1>';
    foreach ($rows as $row) {
		$json = json_encode($row);
		echo "<tr>";

		// first column is for delete button
		echo "<td style=\"text-align:left\">";
		
		$_id=$row->_id->__toString();
		$rowObj = json_decode(json_encode($row));
		// if the document has the property "myproperty", don't show a delete button
  		if ( ! property_exists($rowObj, "myproperty") ){
  			echo '<form method="post" action="./sample-mongo.php">';
  			echo "<input type=\"hidden\" name=\"action\" value=\"DeleteDoc\"/>";	
			echo "<button type=\"submit\" name=\"DocToDelete\" value=\"$_id\"/>Delete</button>";	
			echo "</form>";
		}
		echo "</td>";
        echo "<td style=\"text-align:left\">$json</td></tr>";
		
//        print_r(json_encode($row).'<br>');
    }
	echo '</table></pre>';
	
	
	

// print database statistics
    $cmd = new MongoDB\Driver\Command(["dbstats" => 1]);
    $result = $manager->executeCommand($dbName, $cmd);
    
    $stats = current($result->toArray());

	echo '<p><h2>Database Statistics</h2>';
	echo '<pre>'; var_dump($stats); echo '</pre>';



	
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
