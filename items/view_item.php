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

$request = $_REQUEST;

// save the browser history
if(isset($_SESSION['user']['id']))
{
	date_default_timezone_set('America/New_York'); 
	$date_browsed = date("Y-m-d H:i:s");

	$sql = "INSERT INTO browsing_history
				(buyer_id, item_id, date_browsed)
				VALUES 
				(:buyer_id, :item_id, :date_browsed)";
	$params = array(':buyer_id' => $_SESSION['user']['id'], 
					':item_id' => $request['item_id'], 
					':date_browsed' => $date_browsed);
	$result = query($sql,$params);
}

$sql = "SELECT * FROM items WHERE item_id = :item_id";
$params = array(':item_id' => $request['item_id']);
$result = query($sql,$params);
$row = fetch($result);

// select current bid
$sql = "SELECT MAX(amount) AS amount FROM `bids` WHERE item_id = :item_id";
$params = array(':item_id' => $request['item_id']);
$result = query($sql,$params);
$maxBid = fetch($result);

$categories[] = $row['category_id'];
$sql = "SELECT parent_id FROM `categories` WHERE category_id = :category_id";
$params = array(':category_id' => $row['category_id']);
$result = query($sql,$params);
$parent_id = fetch($result);
$categories[] = $parent_id['parent_id'];

while($parent_id['parent_id'] != 0)
{
	$sql = "SELECT parent_id FROM `categories` WHERE category_id = :category_id";
	$params = array(':category_id' => $parent_id['parent_id']);
	$result = query($sql,$params);
	$parent_id = fetch($result);
	$categories[] = $parent_id['parent_id'];		
}

$categories = array_reverse($categories);

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_content">
	<body>
		<h1 style="font-size: 150%;"><b><?php echo $row['name']; ?></b></h1><br>
		<h1><b>Category:</b>
			<?php
			$count = 0;

			foreach($categories as $category)
			{
				$count++;

				if($category != 0)
				{
					$sql = "SELECT name FROM `categories` WHERE category_id = :category_id";
					$params = array(':category_id' => $category);
					$result = query($sql,$params);
					$result = fetch($result);

					$sql = "SELECT COUNT(*) FROM `items` WHERE category_id = :category_id";
					$params = array(':category_id' => $category);
					$countTotal = query($sql,$params);
					$countTotal = fetch($countTotal);	
					$countTotal = $countTotal['COUNT(*)'];
					
				 	echo $result['name']."(".$countTotal.")";

				 	if($count >= 1 && $count != count($categories))
				 		echo " -> ";
				}

			} ?>
		 <br><br></h1>
		<h1><b>Description:</b>
			<?php echo $row['description']; ?></h1><br>
		<h1><b>Location:</b>
			<?php echo $row['location']; ?></h1><br>
		<h1><b>Reserve Price:</b>
			<?php echo $row['reserve_price']; ?></h1><br>			
		<h1><b>Current Bid:</b>
			<?php echo $maxBid['amount']; ?></h1><br>
			
		<div class="buttons"> 
			<?php $link = 'bidding/bid.php?item_id='.$request['item_id']; ?>
			<br/><a class="button" href="<?php echo PATH.$link; ?>">Place Bid</a>
		</div>	
			
	</body>
</div>

<?php
	require '../includes/footer.inc.php';
?>