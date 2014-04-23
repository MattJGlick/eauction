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

@session_start(); 
$request = $_REQUEST;

$action = (isset($_GET['action'])) ? sanitize($_GET['action']) : false;

$_SESSION['user']['theme'] = 'default';

if(isset($request['action']))
{
	if($request['action'] == 'logout')
	{
		unset($_SESSION['user']['id']);
	}
}

require_once 'includes/html.header.inc.php';
?>

<?php echo (isset($messages)) ? $messages : '';?>

<!--<div class="section_description">Please Complete the Search Form Below.</div> -->
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
	<div class="section_title">Welcome To E - Auction</div>
	<div class="section_title_divider"></div>	
	E-Auction is an online auction site where you can sell and buy items from other users. 
	<br/>
	<br/>
	We are currently in our beta stage and working hard improving E-auction and its functionality
	<br/>
	<br/>
	Happy Bidding!
	
	</form>
</div>

<?php
require 'includes/footer.inc.php';

?>	