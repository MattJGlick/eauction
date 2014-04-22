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

$request = $_REQUEST;

$sql = "SELECT * FROM items WHERE item_id = :item_id";
$params = array(':item_id' => $request['item_id']);
$result = query($sql,$params);
$items = fetch($result);

// select current bid
$sql = "SELECT MAX(amount) AS amount FROM `bids` WHERE item_id = :item_id";
$params = array(':item_id' => $request['item_id']);
$result = query($sql,$params);
$maxBid = fetch($result);

$success = 0;

if(isset($request['submit_bid']))
{
	$success = 1;

	if(!is_numeric($request['amount']))	
	{
		// bid is not correct format
		$success = 0;
		message('error','Invalid bid entry. Only enter a number.');
	}
	else if($request['amount'] <= (1.05*$maxBid['amount']))
	{
		// bid does not exceed current bid by 5%
		$success = 0;
		message('error','Invalid bid. Bid must exceed the current bid by 5%');
	}
	
	if($success)
	{
		$bid_date = date("Y-m-d H:i:s");
		
		// insert them into the seller table
		$sql = "INSERT INTO bids
				(amount, bid_date, bid_type, buyer_id, item_id)
				VALUES
				(:amount, :bid_date, :bid_type, :buyer_id, :item_id);";
		$params = array(':amount' => $request['amount'], ':bid_date' => $bid_date,
						':bid_type' => 'bid', ':buyer_id' => $_SESSION['user']['id'],
						':item_id' => $items['item_id']);
		$result = query($sql,$params);
	}
}
else if(isset($request['BIN']))
{
	$bid_date = date("Y-m-d H:i:s");

	// insert them into the seller table
	$sql = "INSERT INTO bids
			(amount, bid_date, bid_type, buyer_id, item_id)
			VALUES
			(:amount, :bid_date, :bid_type, :buyer_id, :item_id);";
	$params = array(':amount' => $items['bin_price'], ':bid_date' => $bid_date,
					':bid_type' => 'Buy it now', ':buyer_id' => $_SESSION['user']['id'],
					':item_id' => $request['item_id']);
	$result = query($sql,$params);	
}	

// check to see what auctions are complete
$current_date = date("Y-m-d H:i:s");
$null_date = "0000-00-00 00:00:00";

// select all items that have a start_date of more than two weeks ago and who aren't already complete
$sql = "SELECT * FROM items I WHERE :current_date < DATE_ADD(start_date, INTERVAL 14 DAY)
							  AND I.item_id = :item_id";
$params = array(':current_date' => $current_date, ':item_id' => $request['item_id']);
$result = query($sql,$params);
$validBid = fetch($result);

// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_content">
	<body>
		<h1 style="font-size: 150%;"><b><?php echo $items['name']; ?></b></h1><br>
		<h1><b>Description:</b>
			<?php echo $items['description']; ?></h1><br>
		<h1><b>Location:</b>
			<?php echo $items['location']; ?></h1><br>				
		<h1><b>Buy It Now Price:</b> $
			<?php echo $items['bin_price']; ?></h1>

	<?php if($validBid != false)
	{ ?>
	<form id="BIN_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		<input name="BIN" type="submit" value="Buy it now"/>
	    <input type="hidden" id="item_id" name="item_id" value="<?php echo $request['item_id']?>">

	</form>
	<?php } ?>
			
		<h1><b><br>Current Bid:</b> $
			<?php echo $maxBid['amount']; ?></h1>	
	
	<?php if($validBid != false)
	{ ?>
	<form id="place_bid_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Bid: $
		<input id="amount" name="amount" type="text" class="text"/><br />
	    <input type="hidden" id="item_id" name="item_id" value="<?php echo $request['item_id']?>">

		<input name="submit_bid" type="submit" value="Submit Bid"/>	
	</form>
	<?php }
	else
	{ ?>
	<h1 style="font-size: 150%;"><b>Auction Closed</b></h1>
	<?php } ?>
	
	</body>
</div>

<?php
	require '../includes/footer.inc.php';
?>