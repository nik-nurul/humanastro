<?php
session_start(); // used to pass userId from page to page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - Contact</title>
<?php
require_once 'includes/head-base.html';
?>
</head>

<body class="Contactbg">

<?php
require_once 'includes/header.html';
?>
	<!--division for paragraph about the test-->
	<section>
<?php
require_once 'includes/feeback-link.php';
?>
<link rel="stylesheet" type="text/css" href="styles/ContactStyle.css">
        <div id="contact_paragraph">
            <h2 class="heading_font"> Contact </h2>
            <hr class="heading">
        		<p class="paragraph_font contentbg">
                (03) 9214 8000
            <p>

            <p  class="paragraph_font contentbg">
                The Centre for Astrophysics and Supercomputing <br/>
                Swinburne University of Technology <br/>
                Mail H29, PO Box 218 <br/>
                Hawthorn VIC 3122 <br/>
            </p>

            <p  class="paragraph_font contentbg">
                You can visit <a href ="feedback.php" id="contactpageFeedback">this page</a> if you have any enquiries or feedback
            </p>

        </div>
    </section>

<?php
require_once 'includes/footer.html';
?>

</body>
</html>
