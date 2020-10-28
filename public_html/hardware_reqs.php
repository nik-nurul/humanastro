<?php
	session_start(); // used to pass userId from page to page
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Astronomy Test - System Requirements</title>

<?php
	require_once 'includes/head-base.html';
?>
	<!-- for detecting browser, OS and webcam -->
	<script src="javascript/hardware_reqs.js"></script>
    
	<!-- for navigation bar -->
    <script src="javascript/style.js"></script>
</head>

<body>

<?php
	require_once 'includes/header.html';
?>
    <section>
<?php
	require_once 'includes/feeback-link.php';
?>
	<div id="content_paragraph">
		<h2 class="heading_font"> Hardware Requirements </h2>
        <hr class="heading">
        <p class="paragraph_font">
				It is recommended that you have a desktop or laptop computer with minimum requirements of a 2GHz processor,
				4GB of RAM and a minimum display resolution of 1366x768px to be used in order to complete the test smoothly.
				A compatible webcam accessible through a browser is also required in order to track eye movement during the tests and
				above all a stable wi-fi connection.			
		</p><br/><br/>

	  <h2 class="heading_font"> Your System </h2>
      <hr class="heading">
      <p class="paragraph_font">
	    Screen resolution: <span id="width"></span>x<span id="height"></span><br/><br/>
	    Webcam: <span id="webcam"></span><br/><br/>
		Operating system: <span id="os"></span><br/><br/>
		Browser: <span id="browser"></span><br/><br/>
		Mobile: <span id="mobile"></span><br/><br/>
	  </p>
	  <h4 class="heading_font"> <span class="line">How to discover</span>
		<span class="line"> processor (CPU)</span><span class="line"> and memory (RAM) specifications</span></h4>
      <p class="paragraph_font">
	    These links are to external sites. Please use your browsers' "back" button to return to this site.<br/><br/>
		On <a href="https://www.hellotech.com/guide/for/how-to-check-computer-specs-windows-10">Windows 10</a><br/><br/>
		On <a href="https://www.macworld.co.uk/how-to/mac/how-check-mac-specs-processor-ram-3594298/">Mac</a><br/><br/>
		On <a href="https://alvinalexander.com/linux-unix/linux-processor-cpu-memory-information-commands/">Linux</a><br/><br/>
	  </p><br/><br/>
	  
		<a href="./"><button id="home" class="bttn paragraph_font" type="button">Home</button></a>
    </div>
	  
	<script>
		document.getElementById("width").innerHTML = (screen.width*window.devicePixelRatio);
		document.getElementById("height").innerHTML = (screen.height*window.devicePixelRatio);

		detectWebcam(
			(hasWebcam)=>{
				document.getElementById("webcam").innerHTML = (hasWebcam ? 'Available' : 'Unavailabvle');
		});

		document.getElementById("os").innerHTML = jscd.os +' '+ jscd.osVersion;
		document.getElementById("browser").innerHTML = jscd.browser +' '+ jscd.browserMajorVersion +' (' + jscd.browserVersion + ')';
		document.getElementById("mobile").innerHTML = jscd.mobile;
	</script>
	  
	  
	  
</section>

<?php
	require_once 'includes/footer.html';
?>

</body>
</html>
