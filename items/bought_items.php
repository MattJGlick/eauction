<?php 
/* ************************************************************************************************
 * floor/check_on.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "Bought Items";
$body_type =  $page_title;

require '../includes/html.header.inc.php';
	
// Format messages for display
$messages = formatMessages();
$request = $_REQUEST;

if(isset($_SESSION['user']['id']))
{		
	// select item_id of items that the user has bought
	$sql = "SELECT B.item_id FROM bids B WHERE buyer_id = :user_id
			AND bid_id IN (SELECT bid_id FROM auctions_complete)";
	$params = array(':user_id' => $_SESSION['user']['id']);
	$item_id = query($sql, $params);
	
	if ($item_id->rowCount() != 0)
	{ 
		while ($item = fetch($item_id)) 
		{
			//select item info of specific
			$sql = "SELECT * FROM items WHERE item_id = :item_id";
			$params = array(':item_id' => $item['item_id']);
			$result = query($sql, $params);
			$itemInfo = fetch($result);
		
			?>
			<body>
				<h1 style="font-size: 150%;"><b><?php echo $itemInfo['name']; ?></b></h1><br>
				<h1><b>Description:</b>
					<?php echo $itemInfo['description']; ?></h1><br>
				<h1><b>Location:</b>
					<?php echo $itemInfo['location']; ?></h1><br>
			<?php
			$sql = "SELECT MAX(amount) AS amount FROM `bids` WHERE item_id = :item_id";
			$params = array(':item_id' => $itemInfo['item_id']);
			$result = query($sql,$params);
			$maxBid = fetch($result);
			?>		
					
				<h1><b>Winning Bid:</b>
					<?php echo $maxBid['amount']; ?></h1><br>
		<?php			
		}
	}
}	
else
{
	?>
	<body>
	<h1 style="font-size: 150%;"><b>You are not logged in</b></h1><br>
	<?php
}
?>