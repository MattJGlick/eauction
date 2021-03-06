<?php 

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
$verified = false;

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
	else if($request['amount'] <= $items['reserve_price'])
	{
		// bid does not exceed reserve price for this item
		$success = 0;
		message('error','Invalid bid. Bid does exceed the reserve price set by the seller');
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

		message('success','Bid Placed!');		
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
	
	$sql = "SELECT bid_id FROM bids WHERE bid_type = 'buy it now' AND item_id = :item_id";
	$params = array(':item_id' => $request['item_id']);
	$bids = query($sql,$params);
	$bids = fetch($bids);

	// insert item into auctions complete table
	$sql = "INSERT INTO auctions_complete(bid_id, date_item_received, date_money_received, date_item_sent, date_money_sent)
								   VALUES(:bid_id, :nullDate, :nullDate, :nullDate, :nullDate)";
	$params = array(':bid_id' => $bids['bid_id'], ':nullDate' => $null_date);
	$result = query($sql, $params);

	message('success','You have bought this item!');
}	

$sql = "SELECT * FROM items WHERE item_id = :item_id";
$params = array(':item_id' => $request['item_id']);
$result = query($sql,$params);
$items = fetch($result);

// select current bid
$sql = "SELECT MAX(amount) AS amount FROM `bids` WHERE item_id = :item_id";
$params = array(':item_id' => $request['item_id']);
$result = query($sql,$params);
$maxBid = fetch($result);



// check to see what auctions are complete
$current_date = date("Y-m-d H:i:s");
$null_date = "0000-00-00 00:00:00";

// select all items that have a start_date of more than two weeks ago and who aren't already complete
$sql = "SELECT * FROM items I WHERE :current_date > DATE_ADD(start_date, INTERVAL 14 DAY)
							  AND I.item_id = :item_id";
$params = array(':current_date' => $current_date, ':item_id' => $request['item_id']);
$result = query($sql,$params);
$too_long = fetch($result);

$sql = "SELECT * FROM auctions_complete a, bids b WHERE a.bid_id = b.bid_id AND b.item_id = :item_id";
$params = array(':item_id' => $request['item_id']);
$result = query($sql, $params);
$item_sold = fetch($result);

// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';

if(isset($request['Login']))
{
	// hash the password
	$password = md5($request['password']);
	
	$sql = "SELECT * FROM sellers WHERE username = :username";
	$params = array(':username' => $_SESSION['user']['username']);
	$result = query($sql,$params);
	$row = fetch($result);

	if($password == $row['password'])
	{
		$verified = true;
	}
}

if($verified)
{?>

	<div class="section_content">
		<body>
			<h1 style="font-size: 150%;"><b><?php echo $items['name']; ?></b></h1><br>
			<h1><b>Description:</b>
				<?php echo $items['description']; ?></h1><br>
			<h1><b>Location:</b>
				<?php echo $items['location']; ?></h1><br>
			<h1><b>Reserve Price:</b> $
				<?php echo $items['reserve_price']; ?></h1><br>
			<h1><b>Buy It Now Price:</b> $
				<?php echo $items['bin_price']; ?></h1>

		<?php 
		if(!$too_long && !$item_sold)
		{ 
		?>
		
			<form id="BIN_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
				<input name="BIN" type="submit" value="Buy it now"/>
				<input type="hidden" id="item_id" name="item_id" value="<?php echo $request['item_id']?>">
			</form>
			
		<?php 
		} 
		?>
				
			<h1><b><br>Current Bid:</b> $
				<?php echo $maxBid['amount']; ?></h1>	
		
		<?php 
		if(!$too_long && !$item_sold)
		{ 	?>

			<form id="place_bid_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
				Bid: $
				<input id="amount" name="amount" type="text" class="text"/><br />
				<input type="hidden" id="item_id" name="item_id" value="<?php echo $request['item_id']?>">

				<input name="submit_bid" type="submit" value="Submit Bid"/>	
			</form>



			<?php	
		}
		else
		{
		?>
			<br/><h1 style="font-size: 150%;"><b>Auction Closed</b></h1>
		<?php 
		}
		?>

		</body>
	</div>
<?php
}
else
{
?>
	<form id="enter_password" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		<h1>Please re-enter your password to proceed to the bidding page. </h1><br/>
		<input id="password" name="password" type="password" class="text"  placeholder="Password"/>
		<input type="hidden" id="item_id" name="item_id" value="<?php echo $request['item_id']?>">
		<input name="Login" type="submit" value="Login"/>
	</form>		
<?php
}
	require '../includes/footer.inc.php';
?>