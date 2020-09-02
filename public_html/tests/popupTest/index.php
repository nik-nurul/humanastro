<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// user ID - this would be the user ID already created in the demographic question process
$_id = new MongoDB\BSON\ObjectId(bin2hex(random_bytes(12)));
$userIdStr = (string)$_id;
	
require_once 'includes/functions.php';
//$userIdStr = sanitise_input($_GET["userId"]); // defend against malicious GET requests

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<script src="javascript/popupTest.js"></script>
	</head>
<body>
<label id="userId" hidden="true"><?php echo $userIdStr ?></label>
	<div id="demo">
		<h1>Testing of a Popup Window</h1>
		<button type="button" id="newPopup">New Popup</button>
	</div>
</body>
</html>