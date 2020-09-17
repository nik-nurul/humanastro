<?php

session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dataname = 'mydata.txt';
$zipname = 'allUsers.zip';
$tempname = tempnam(sys_get_temp_dir(), "zipastro");
	
//$myfile = file_get_contents('foo.txt');

// https://www.php.net/manual/en/function.proc-open.php

$cmd = 'mongoexport --quiet --jsonArray -d humanastro -c users | zip '.$tempname.' -';

$proc=proc_open($cmd,
  [
    ["pipe","r"],
    ["pipe","w"],
    ["pipe","w"]
  ],
  $pipes);

fwrite($pipes[0]), "";
fclose($pipes[0]);

header("Content-Type: application/zip");
header("Content-Transfer-Encoding: Binary");
header("Content-Disposition: attachment; filename=".$zipname);

//print stream_get_contents($pipes[1]);

$contents = file($filename);

foreach($contents as $line) {
    echo $line . "\n";
}
echo file_get_contents($tempname);
?>
