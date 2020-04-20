<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="SWE40001 Group 3" />
	<meta name="keywords" content="PHP " />
	<meta name="author" content="Group 3" />
	<title>Updating the Feedback Text File</title>
</head>

<body>

	<h1>Feedback Save Page</h1>
	<hr class="hrstyle">

	<?php

		//checking the file directory if exist or not
		if (!is_dir("../../data/test")){
			//if it doesn't exist, create a new directory
			mkdir("../../data/test");
		}
		
		//check if there is any input
		if(isset($_POST["feedback"]) && ($_POST["feedback"] != "")){     // check if the feedback input has been entered and it is not empty
			
			$feedback = $_POST["feedback"];    //assign feedback to a variable called 'feedback'
			
			
			//create a new .txt file and save into a directory specified above
			$filename = "../../data/test/TestFeedback.txt"; 
			//open the text file usisng $handle variable in append mode (a)
			$handle = fopen($filename, "a");   //open the file in append mode
				$feedbackData = "$feedback\n\n";   //write the feedback with a double lines seperator at the end
				fwrite($handle, $feedbackData);   //write the feedback string to text file
				fclose($handle);   //close the text file

		} else {
			//if there is no feedback inputs from user
			echo "Use the Browser's 'Go Back' button to return to the feedbacck. </p>";
		}
			
	?>
	
	<hr>
	<a href="TestFeedbackForm.php">Add Another Feedback</a><br/>
	

</body>

</html>