<?php

session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// this page requires a username and password to enter
require_once 'includes/admin_auth.php';

// successful authentication

$dbName = 'humanastro';		// database name
$collName = 'users';	// collection name
//$collName = 'task_data';	// collection name

$dataname = 'allUsers.json';
$zipname = 'allUsers.zip';

$tempname = tempnam(sys_get_temp_dir(), "zipastro").'.zip';
// https://www.php.net/manual/en/function.proc-open.php
$cmd = 'nice -n 19 sh -c "mongoexport --quiet --jsonArray -d '.$dbName.' -c '.$collName.' | zip '.$tempname.' -"';
exec($cmd); // run mongo export command
exec('printf "@ -\n@='.$dataname.'\n" | zipnote -w '.$tempname); // rename internal file

// download zip file
header("Content-Type: application/zip");
header("Content-Transfer-Encoding: Binary");
header("Content-Disposition: attachment; filename=".$zipname);
readfile($tempname);
?>
