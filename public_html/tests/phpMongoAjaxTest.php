<?php
// based on example from: https://www.w3schools.com/xml/ajax_intro.asp
// and https://stackoverflow.com/questions/39341901/how-to-call-a-php-function-from-ajax

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<html>
<script src="phpMongoAjaxTest.js"></script>
<body>
<p><h1>Test of Using Ajax to trigger PHP to send data to MongoDB</h1>
<p>

<div id="demo">
  <h2>Let AJAX do add something to MongoDB via PHP</h2>
  <button type="button" id="addToMongo">Add To Mongo</button>
</div>

</body>
</html>
