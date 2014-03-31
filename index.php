<?php
/* ************************************************************************************************
 * index.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Login prompt and public landing page
 * 
 * ************************************************************************************************/

$page_title = "E - Auction";
$body_type =  $page_title;

require 'includes/db.inc.php';
require 'includes/functions.inc.php';
require 'includes/config.inc.php';
require_once 'includes/html.header.inc.php';

@session_start(); 

$action = (isset($_GET['action'])) ? sanitize($_GET['action']) : false;

$_SESSION['user']['theme'] = 'default';
 

require 'includes/footer.inc.php';

?>
</html>