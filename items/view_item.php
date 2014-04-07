<?php 
/* ************************************************************************************************
 * floor/check_on.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "Item Information";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Format messages for display
$messages = formatMessages();

$sql = "SELECT * FROM items WHERE seller_id = :seller_id";
$params = array(':seller_id' => $_SESSION['user']['id']);
$result = query($sql,$params);
$row = fetch($result);

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_content">
	<body>
		<h1 style="font-size: 150%;"><b><?php echo $row['name']; ?></b></h1><br>
		<h1><b>Description:</b>
			<?php echo $row['description']; ?></h1><br>
		<h1><b>Location:</b>
			<?php echo $row['location']; ?></h1><br>
			<button onclick="location.href='<?php echo PATH.'bidding/bid.php';?>';">Place Bid</button>
	</body>
</div>

<?php
	require '../includes/footer.inc.php';
?>