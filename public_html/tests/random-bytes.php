<?php
	session_start(); // used to pass userId from page to page
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


?>
<!DOCTYPE HTML >
<html lang="en">
<head>
</head>
<body >
<?php
echo "<p>Random Byte: ";
$randbytes = random_bytes(12);
echo $randbytes;
echo "</p>";

echo "<p>bin2hex: ";
echo bin2hex($randbytes);
echo "</p>";

$oid = new MongoDB\BSON\ObjectId(bin2hex($randbytes));
$oidStr = (string)$oid;
echo "<p>OID (random): ";
echo $oidStr;
echo "</p>";

$oid = new MongoDB\BSON\ObjectId();
$oidStr = (string)$oid;
echo "<p>OID (not random): ";
echo $oidStr;
echo "</p>";
?>
</body>
</html>

