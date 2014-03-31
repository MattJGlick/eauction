<?php
/* ************************************************************************************************
 * includes/db.inc.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: Connects to the MySQL database
 * 
 * ************************************************************************************************/

$options = array(
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
); 
 
$localDB = TRUE;

if ($localDB) {
// Local XAMPP Database
$db_host = "localhost"; 
$db_user = "root";
$db_pass = "";
$db_name = "pass";

} else {
// Online Database at pass13.org
$db_host = "localhost"; 
$db_user = "onther7_pass13";
$db_pass = "TeiSSmRTFG2UkyabxlLi";
$db_name = "onther7_pass_live";

}
$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, $options);
?>