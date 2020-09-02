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
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="styles/canvasImageTest.css">
	</head>
<body>
<label id="userId" hidden="true"><?php echo $userIdStr ?></label>
	<div id="popup_wrapper" class="popup_full popup_hide">
		<section class="popup_container">
			<div class="popup_header"><h1>Yep, it's a popup!</h1></div>
			<div class="popup_content"></div>
			<div class="popup_footer"></div>
		</section>
		<div class="popup_close"></div>
	</div>
</body>