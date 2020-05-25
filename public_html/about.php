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
        <div id="contact_paragraph">
            <h2 class="heading_font"> About Us </h2>
            <hr class="heading">
        		<p class="paragraph_font contentbg">
              These days, automated techniques, such as Artificial Intelligence and Machine Learning,
              are expected to make discoveries for astronomers.  However, the human visual system is
              regularly proposed as being the gold standard for novel discovery in astronomy, but with
              limited research to support that claim. Hence, this project is to create application to measure
              the visual-discovery skills of astronomers.
            <p>

            <p  class="paragraph_font contentbg">
              This project forms part of a larger research investigation currently underway by Chris and his
              collaborators, studying Cyber-physical discovery systems: where humans and automated processes
              (e.g. artificial intelligence) working seamlessly together to maximise human discovery potential.
              These approaches have many applications in scientific research (astronomy, medical research, etc),
              industry, cybersecurity and defence.
            </p>

        </div>
    </section>

<?php
require_once 'includes/footer.html';
?>

</body>
</html>
