<!DOCTYPE html>
<!-- test from https://www.codespeedy.com/save-html-form-data-in-a-txt-text-file-in-php/ -->
<!-- first example now using file_put_contents() -- Fafa's idea -->
<html>
<head>
  <title>Store form data in .txt file</title>
</head>
<body>
  <form method="post">
    Enter Your Text Here:<br>
    <textarea name="comment" rows="10" cols="60"></textarea>
	<br>
    <input type="submit" name="submit">
  </form>
</body>
</html>
<?php
              
if(isset($_POST['textdata']))
{
	$data=$_POST['textdata'];
	file_put_contents('data.txt', $data, FILE_APPEND);
//	$fp = fopen('data.txt', 'a');
//	fwrite($fp, $data);
//	fclose($fp);
}
?>