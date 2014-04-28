<?php 
/* ************************************************************************************************
 * floor/check_on.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "Purchased Items";
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
			
			<div class="section_content">
				<div class="section_title"><?php echo $itemInfo['name']; ?></div>
				<div class="section_title_divider"></div>			
			
				<body>				
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
				$sql = "SELECT * FROM items WHERE category_id = :category_id AND item_id != :item_id";
				$params = array(':category_id' => $itemInfo['category_id'], ':item_id' => $itemInfo['item_id']);
				$result = query($sql,$params);
				$relatedItem = fetch($result);
				?>
					
					<h1><b>You might also like:</b>
						<td><a href="<?php echo PATH.'items/view_item.php?item_id	='.$relatedItem['item_id']?>"><?php echo $relatedItem['name'];?></a></td>

					<a href="http://www.facebook.com" target="_blank">
						<img src="http://localhost/eauction/includes/facebook.png" alt="facebook">
					</a>						
							
					<?php 
					
					$sql = "SELECT R.bid_id FROM rating R, bids B 
							WHERE R.bid_id =  B.bid_id AND B.item_id = :item_id";
					$params = array(':item_id' => $itemInfo['item_id']);
					$rating = query($sql, $params);
					$rating = fetch($rating);

					if($rating == FALSE)
					{
					$link = 'rating/rating.php?item_id='.$itemInfo['item_id']; ?>
					<br/><a class="button" href="<?php echo PATH.$link; ?>">Rate This Sale</a>
					<?php
					}
					?>
					<br/><br/>
			</div>
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