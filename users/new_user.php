<?php 
/* ************************************************************************************************
 * floor/check_on.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
 $page_title = "User Registration";
 $body_type =  $page_title;

$override = array();

// Validate and initialize inputs
$request = $_REQUEST;

// CODE TO SUBMIT NEW USER

require '../includes/html.header.inc.php';
// Format messages for display
$messages = formatMessages();

require '../includes/footer.inc.php';

?>
