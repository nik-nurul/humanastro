<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Feedback</title>
<?php
include 'includes/head-base.html';
?>
</head>

<body>
	
<?php
include 'includes/header.html';
?>
<!-- division for user feedback form-->
  <section>  
  
	<div id="content_paragraph">

		<!--Page header-->
		<h2 class="heading_font">User Feedbacks</h2>
		<hr class="heading"><br/>
		
		<!--Content paragraph-->
		<p class="paragraph_font">The following questions is designed to record your experience throughout the process of using the website and completing the test.
		Future improvements and maintanance will be done based on the rate (1 to 5) given to us. 
		You can also provide additional feedbacks in the issues/comments section.</p>
		
		<br/>
		<br/>
 
		<form>
			<p>
				Design, feel and clarity  
				<div class="range">
					<input type="range" min="1" max="6" steps="1" value="6">
				</div>
	
					<ul class="range-labels">
					<li>1</li>
					<li>2</li>
					<li>3</li>
					<li>4</li>
					<li>5</li>
					<li>No answer</li>
					</ul>
			</p>
			<br/>
			<br/>
			
			<p>
				Navigation and control  
				<div class="range">
					<input type="range" min="1" max="6" steps="1" value="6">
				</div>
	
					<ul class="range-labels">
					<li>1</li>
					<li>2</li>
					<li>3</li>
					<li>4</li>
					<li>5</li>
					<li>No answer</li>
					</ul>
			</p>
			<br/>
			<br/>
			
			<p>
				Loading time  
				<div class="range">
					<input type="range" min="1" max="6" steps="1" value="6">
				</div>
	
					<ul class="range-labels">
					<li>1</li>
					<li>2</li>
					<li>3</li>
					<li>4</li>
					<li>5</li>
					<li>No answer</li>
					</ul>
			</p>
			<br/>
			<br/>
			
			<p>
				Test completion experience  
				<div class="range">
					<input type="range" min="1" max="6" steps="1" value="6">
				</div>
	
					<ul class="range-labels">
					<li>1</li>
					<li>2</li>
					<li>3</li>
					<li>4</li>
					<li>5</li>
					<li>No answer</li>
					</ul>
			</p>
			<br/>
			<br/>
			
			<!--Best aspect-->
			<p><label for="baspect">Best aspect of the website:</label>
			<br/>
					<input class="resize" type="text" name= "Best aspect" id="baspect" maxlength="40" size="50" required="required"/>
			</p>		
			<!--Worse aspect-->
			<p><label for="waspect">Aspect that require improvement/attention:</label> 
			<br/>		
					<input class="resize" type="text" name= "Last name" id="waspect" maxlength="40" size="50" required="required"/>
			</p>
			
			<p><label for="comment">Additional comments/issues</label>
				<br />
					<textarea id="comment" name="Comment" placeholder="Eg: Specify and explain any addition comments you have or issues encountered." rows="8" cols="60"></textarea>	
			</p>
		</form>

	  
		<!-- section for buttons  -->
		<!-- this button will redirect to homepage -->
		<a href="index.php"><input id="quitBttn" type= "reset" value="Exit to Home"/></a>
		<!-- this button will redirect to webcam calibration page-->
		<a href="calibration.php"><input id="submitBttn" type= "submit" value="Submit and continue"/></a>
		
		<br/>
		
	</div>
  </section>
  
<?php
include 'includes/footer.html';
?>

</body>
</html>
