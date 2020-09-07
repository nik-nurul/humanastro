<?php
	// sanitise self describe gender to remove all special characters
	function sanitise_input($data) {
		//Remove any special characters
		$data = preg_replace('/[^A-Za-z0-9 \-\_\.\?\,\!]/', '', $data); // allow some punctuation and numbers so people can comment
		//Removes leading or trailing spaces
		$data = trim($data);
		//Remove backslashes in front of quotes
		$data = stripslashes($data);
		return $data;
	}
	
	// return true if the string is valid JSON
	function isJson($string) {
	 json_decode($string);
	 return (json_last_error() == JSON_ERROR_NONE);
	}
	
	// write the current page the user is on so the user can
	// resume an interrupted session
	function set_current_page($bulk, $_id, $file){
		$bulk->update(
			[ "_id" => $_id ],
			[ '$set' => [ "current_page" => $file ] ]
		);	
	}
?>