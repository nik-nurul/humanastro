<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Consent</title>
<?php
include 'includes/head-base.html';
?>
</head>

<body>

<?php
include 'includes/header.html';
?>
    <section>
<?php
include 'includes/feeback-link.html';
?>
      <!-- division part for consent statement -->
      <div id="content_paragraph">
          <h2 class="heading_font"> Asking for consent </h2>
          <hr class="heading">
          <p class="paragraph_font">
              All information taken from the study will be coded to protect each subject’s name. No
              names or other identifying information will be used when discussing or reporting data. The
              investigator(s) will safely keep all files and data collected in a secured locked cabinet in the
              principal investigators office. Once the data has been fully analyzed it will be destroyed.
              Example: Your responses are completely anonymous. No personal identifying information or IP
              addresses will be collected. Data will be aggregated via the Qualtrics reporting function.
              Quantitative results will be shared with the Chairperson and the faculty in the academic unit.
              Qualitative results will be shared with the Chairperson and the Provost’s Office.
              Example: I would like to interview you “on the record” so that I can identify you in publications
              resulting from this research. However, if you wish to remain anonymous, I will keep your name
              separate from your words; I will not use your name in any quotations or reports of my findings; I
              will use a pseudonym of your choosing; and I will omit or obscure any identifying details
          </p><br/><br/>
          <p class="paragraph_font" id="bttn_question">
            Do you consent to participate?
          </p>
			<!--Yes button will direct the user to the next page (create user ID),
			No button will direct the user back to the home page-->
			<form id="consent" action="createid.php" method="post">
				<input id="consent_yes" type="hidden" name="consent" value="true"/>
				<input class="bttn" id="consent" type="submit" value="Yes"/>
				<a href="home.php"><button class="bttn" id="nobutt" type="button">No</button></a>
			</form>
      </div>

  </section>



	<!--footer section-->
	<footer>
		<p>Software Engineering Project A<p>
		<p>&#169; Swinburne University of Technology</p>
	</footer>

</body>
</html>
