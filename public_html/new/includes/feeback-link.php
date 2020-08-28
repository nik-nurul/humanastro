
	<!-- feedback button (sticky to the left page) -->
	<div class="feedbacklink">
		<form id="feedback_link" action="feedback.php" method="post" >
			<input id="feedbackreturn" type="hidden" name="feedbackreturn" value="<?php
				echo basename($_SERVER['SCRIPT_FILENAME']);
			?>" />
			<input class="bttnfeedback" id="feedback" type="submit" value="Feedback" />
		</form>
	</div>

