<?php 
/* ************************************************************************************************
 * floor/check_on.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "Place a Bid";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Format messages for display
$messages = formatMessages();

$sql = "SELECT * FROM items WHERE seller_id = :seller_id";
$params = array(':seller_id' => $_SESSION['user']['id']);
$result = query($sql,$params);
$row = fetch($result);

// select the largest current bid

if(!is_numeric($request['bid']))	
{
	// bid is bad
	$success = 0;
	message('error','Invalid bid entry. Only enter a number.');
}

// make sure entered bid is greater than current bid

if($success)
{
	// insert them into the seller table
	$sql = "INSERT INTO bids
			(amount, bid_date, bid_id, bid_type, buyer_id, item_id)
			VALUES
			(:amount, :bid_date, :bid_id, :bid_type, :buyer_id, :item_id);";
	$params = array('bid_id' => $_SESSION['bid']['id']);
	$result = query($sql,$params);
}

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_content">
	<body>
		<h1 style="font-size: 150%;"><b><?php echo $row['name']; ?></b></h1><br>
		<h1><b>Description:</b>
			<?php echo $row['description']; ?></h1><br>
		<h1><b>Buy It Now Price:</b>
			<?php echo $row['bin_price']; ?></h1><br>
		<h1><b>Current Bid:</b>
			<?php echo $row['description']; ?></h1><br>	
	</body>
</div>

<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Bid:
		<input id="bid" name="bid" type="text" class="text"/><br /><br />

		<input name="submit" type="submit" value="Submit"/>
	
	</form>
</div>

<?php
	require '../includes/footer.inc.php';
?>