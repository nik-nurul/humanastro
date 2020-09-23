<?php

session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// this page requires a username and password to enter
require_once 'includes/admin_auth.php';

// successful authentication

?>
<html lang="en">
<head>
  <title>Admin page</title>
</head>
<body id="adminpage">
	<p><a href="getAllUsers.php">Download all user records from database</a></p>
</body>


