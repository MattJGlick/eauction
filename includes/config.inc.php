<?php
 
// Script Constants
define("PATH", 'http://localhost/eauction/'); 										// Path to the system on the network
define("SERVER_PATH", 'http://localhost/eauction/');	                        // Path to the system on the server

// Define Local
$ip_remote = explode('.',$_SERVER['REMOTE_ADDR']);

define('LOCAL', true);

// Unset temporary variables
unset($path, $ip_remote, $ip_local);

// Get validation rules
$validate = validate();
?>
