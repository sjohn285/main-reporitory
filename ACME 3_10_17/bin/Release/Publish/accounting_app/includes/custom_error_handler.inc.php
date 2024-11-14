<?php
/*
ERROR HANDLING
*/
function myErrorHandler($errno, $errstr, $errfile, $errline){

	$str = "THIS IS OUR CUSTOM ERROR HANDLER<br>";
	$str .= "ERROR NUMBER: " . $errno . "<br>ERROR MSG: " . $errstr . "<br>FILE: " . $errfile . "<br>LINE NUMBER: " . $errline . "<br><br>";
	
	if(DEBUG_MODE){
		echo($str);
	}else{
		// You might want to send all the super globals with the error message 
		$str .= print_r($_POST, true);
		$str .= print_r($_GET, true);
		$str .= print_r($_SERVER, true);
		$str .= print_r($_FILES, true);
		$str .= print_r($_COOKIE, true);
		$str .= print_r($_SESSION, true);
		$str .= print_r($_REQUEST, true);
		$str .= print_r($_ENV, true);
		
		//send email to web admin
		mail(SITE_ADMIN_EMAIL, SITE_DOMAIN . " - ERROR", $str);
		
		//TODO: echo a nice message to the user, or redirect to an error page
		die("We are sorry, there has been an error. But we have been notified and are working in it.");
	}
}

set_error_handler("myErrorHandler");


/*
EXCEPTION HANDLING
*/
function myExceptionHandler($exception) {

	$str = "THIS IS OUR CUSTOM EXCEPTION HANDLER<br>";
	$str .= $exception->getMessage();

    if(DEBUG_MODE){
		echo($str);
	}else{
		//How to handle exceptions???
		
		// You might want to send all the super globals with the error message 
		$str .= print_r($_POST, true);
		$str .= print_r($_GET, true);
		$str .= print_r($_SERVER, true);
		$str .= print_r($_FILES, true);
		$str .= print_r($_COOKIE, true);
		$str .= print_r($_SESSION, true);
		$str .= print_r($_REQUEST, true);
		$str .= print_r($_ENV, true);
		
		//send email to web admin
		mail(SITE_ADMIN_EMAIL, SITE_DOMAIN . " - EXCEPTION", $str);
		//TODO: echo a nice message to the user, or redirect to an error page??????
	}
}

set_exception_handler("myExceptionHandler");

// WHAT'S THE DIFFERENCE BETWEEN EXCEPTIONS AND ERRORS?
// Exceptions are thrown, and intended to be caught
// Errors are generally not recoverable
