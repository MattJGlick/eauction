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

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_content">
	<body>
		<h1 style="font-size: 150%;"><b><?php echo $row['name']; ?></b></h1><br>
		<h1><b>Item ID:</b>
			<?php echo $row['item_id']; ?></h1><br>
		<h1><b>Seller ID:</b>
			<?php echo $row['seller_id']; ?></h1><br>			
		<h1><b>Description:</b>
			<?php echo $row['description']; ?></h1><br>
		<h1><b>Location:</b>
			<?php echo $row['location']; ?></h1><br>
	</body>
</div>

<?php
	require '../includes/footer.inc.php';
?>