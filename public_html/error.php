<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Error!</title>
<?php
require 'includes/head-base.html';
?>

</head>

<body class="bg">

<?php
require 'includes/header.html';
?>
  <section>
<?php
require 'includes/feeback-link.php';
?>  
	<div>
	<h1 id="web_title" class="web_title_font">
		<span class="line">Error</span>
	</h1>
	</div>

    <div id="content_paragraph">
		<p class="paragraph_font">That didn't work!</p>
		<a href="index.php"><button class="bttn" id="nobutt" type="button">Go Home</button></a>
	</div>

  </section>

<?php
require 'includes/footer.html';
?>

</body>
</html>