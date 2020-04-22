<html>
<body>
MongoDB Test
<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$mongo = new MongoDB\Client('mongodb://127.0.0.1:27017');
	$dbs = $mongo->listDatabases();
?>
</body>
</html>