<?php
/* ************************************************************************************************
 * includes/config.inc.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: A place to store configuration variables and constants for the system.
 * 
 * ************************************************************************************************/
 
// Script Constants
define("PATH", 'http://localhost/pass/'); 										// Path to the system on the network
define("SERVER_PATH", 'http://localhost/pass/');	                        // Path to the system on the server

//define("PATH", 'http://'.$_SERVER['HTTP_HOST'].'/'); 										// Path to the system on the network
//define("SERVER_PATH", $_SERVER['DOCUMENT_ROOT']);	                        // Path to the system on the server
//define("URI", 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);						// The full URI to the current page

// Global Variables
$sql = "SELECT *
		FROM globalVars";
				
$result = query($sql);

while ($row = fetch($result)) {
	define($row['variable'],$row['value']); 
}

// Define Local
$ip_remote = explode('.',$_SERVER['REMOTE_ADDR']);
$ip_local = explode('.',LOCAL_SUBNET);
if (($ip_remote[0] == $ip_local[0]) && ($ip_remote[1] == $ip_local[1]) && ($ip_remote[2] == $ip_local[2])) {
	define('LOCAL', true);
} else {
	define('LOCAL', false);
}

// Debug Functionality
if (DEBUG) {
	// Testing functionality
	define('PARSER_LIB_ROOT', SERVER_PATH.'/dependencies/sqlparser/');
    require_once PARSER_LIB_ROOT.'sqlparser.lib.php';
	error_reporting(E_ALL);
	unset($_SESSION['debug']['sql'], $_SESSION['debug']['var']);
} else {
	//Production functionality
	error_reporting(0);
}

// Unset temporary variables
unset($path, $ip_remote, $ip_local);

// Get validation rules
$validate = validate();
?>
