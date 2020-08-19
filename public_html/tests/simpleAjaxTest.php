<?php
// based on example from: https://www.w3schools.com/xml/ajax_intro.asp

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<html>
<script src="simpleAjaxTest.js"></script>
<body>
<p><h1>Test of Ajax</h1>
<p>

<div id="demo">
  <h2>Let AJAX change this text</h2>
  <button type="button" id="changeContent">Change Content</button>
</div>

</body>
</html>
