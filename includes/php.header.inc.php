<?php
/* ************************************************************************************************
 * includes/header.inc.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: Header for pages that require authenticated users. This is only the PHP portion of
 * the original header file.
 * 
 * ************************************************************************************************/
$body_type = $page_title;
//debug($_SESSION['user']);
//Start session
@session_start();

require_once 'db.inc.php';
require_once 'functions.inc.php';
require_once 'config.inc.php';
// Check if current user is allowed to view this page
checkPermissions('page',$_SERVER['PHP_SELF']);
// Check if user has timed out
checkTimeout();
// Set mobile theming
if (!isset($_SESSION['user']['theme'])) {
	define('MOBILE', detectMobile());
	$_SESSION['user']['theme'] = ((!MOBILE) ? 'default' : 'mobile');
} else {
	define('MOBILE', (($_SESSION['user']['theme']=='default') ? false : true));
}
?>