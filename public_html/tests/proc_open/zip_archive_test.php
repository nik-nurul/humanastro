<?php

session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mydata = file_get_contents('foo.txt');

$dataname = 'mydata.txt';
$zipname = 'allUsers.zip';

https://www.php.net/manual/en/ziparchive.open.php
$zip = new ZipArchive;
$tempzipname = tempnam(sys_get_temp_dir(), "zipastro");
$zip = new ZipArchive;
$res = $zip->open($zipname, ZipArchive::CREATE); // truncate as empty file is not valid
if ($res === TRUE) {
    $zip->addFromString($dataname, $mydata);
    $zip->close();
	header("Content-Type: application/zip");
	header("Content-Disposition: attachment; filename=".$zipname);
	header('Content-Length: ' . filesize($tempzipname));
	echo file_get_contents($tempzipname);
} else {
	echo '<br/>'.$tempzipname;
    echo '<br/>failed<br/>';
	echo $mydata;
}
?>
