<?php
// this is the main configuration file for the website
session_start();

// detect which environment the code is running in
if($_SERVER['SERVER_NAME'] == "localhost"){
	// DEV ENVIRONMENT SETTINGS
	define("DEBUG_MODE", true);
	define("DB_HOST", "localhost");
	define("DB_USER", "root");
	define("DB_PASSWORD", "");
	define("DB_NAME", "acmedb");
	define("SITE_ADMIN_EMAIL", "sjohn285@yahoo.com");
	define("SITE_DOMAIN", $_SERVER['SERVER_NAME']);
	//define("ROOT_DIR", "/adv-web-dev/php/site-setup-7-file-uploads/");

}else{
	// PRODUCTION SETTINGS
	if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
	}
	define("DEBUG_MODE", true); 
	// you may want to set DEBUG_MODE to true when you 
	// are first setting up your live site, but once you get
	// everything working you'd want it off.
	define("DB_HOST", "localhost");
	define("DB_USER", "johnsons");
	define("DB_PASSWORD", "XRp1lj74k5");
	define("DB_NAME", "johnsons_acmedb");
	define("SITE_ADMIN_EMAIL", "sjohn285@yahoo.com");
	define("SITE_DOMAIN", $_SERVER['johnsonspencer.com']);
	//define("ROOT_DIR", "/adv-web-dev/php/site-setup-7-file-uploads/");
}

// if we are in debug mode then display all errors and set error reporting to all 
if(DEBUG_MODE){
	// turn on error messages
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

// the $link variable will be our connection to the database
$link = null;

function get_link(){

	global $link;
		
	if($link == null){
		
		$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

		if(!$link){
			throw new Exception(mysqli_connect_error()); 
		}
	}

	return $link;
}


// set up custom error handling
require_once('custom_error_handler.inc.php');
