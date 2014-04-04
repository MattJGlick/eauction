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
$request = $_REQUEST;

$action = (isset($_GET['action'])) ? sanitize($_GET['action']) : false;

$_SESSION['user']['theme'] = 'default';

if(isset($request['action']))
{
	if($request['action'] == 'logout')
	{
		$_SESSION['user']['id'] = NULL;
	}
}

require 'includes/footer.inc.php';

?>	