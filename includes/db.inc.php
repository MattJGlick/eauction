<?php
/* ************************************************************************************************
 * includes/db.inc.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Connects to the MySQL database
 * 
 * ************************************************************************************************/

$options = array(
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
); 
 
$localDB = TRUE;

// Local XAMPP Database
$db_host = "localhost"; 
$db_user = "root";
$db_pass = "";
$db_name = "eauction";

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, $options);

?>