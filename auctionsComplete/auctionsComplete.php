<?php 
/* ************************************************************************************************
 * auctionsComplete/auctionsComplete.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * ************************************************************************************************/

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

$messages = formatMessages();
echo (isset($messages)) ? $messages : '';

if(!isset($_SESSION['user']['id']))
{
	?>

	<?php
}
	require '../includes/footer.inc.php';
?>
