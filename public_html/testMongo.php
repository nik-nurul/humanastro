<html>
<body>
MongoDB Test
<?php
	$mongo = new MongoDB\Client('mongodb://127.0.0.1:27017');
	$dbs = $mongo->listDatabases();
?>
</body>
</html>