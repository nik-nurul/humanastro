<!DOCTYPE html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="SWE40001 Group 3" />
	<meta name="keywords" content="PHP " />
	<meta name="author" content="Group 3" />
	<title>Trying to save feedback to text file</title>
</head>

<body>
	<h1>Feedback on Website</h1>
	</br>
	<hr>
	
	<!-- form examaple for feedback-->
	<!-- form will be sent to another php page for the process of saving to textfile -->
	<form method="post" action="TestFeedbackSave.php">
	<fieldset>
	<legend><strong>Leave your feedback below:</strong></legend>
		<p>				
			<!-- asking for the feedback -->
			<label for="feedback">Feedback: </label> 
			<input type="text" name="feedback" id="feedback" size="50"/> 
			</br>
			</br>
		
			<input type= "submit" value="Submit"/>		
			<input type= "reset" value="Reset"/>
		</p>
	</fieldset>
	</form>
	</br>
</body>

</html>