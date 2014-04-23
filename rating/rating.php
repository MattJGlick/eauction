<?php 

$page_title = "Make a Rating";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Format messages for display
$messages = formatMessages();

// Validate and initialize inputs
$request = $_REQUEST;

if(isset($request['submit']))
{
	$sql = "SELECT A.bid_id FROM auctions_complete A, bids B WHERE B.item_id = :item_id AND A.bid_id = B.bid_id";
	$params = array(':item_id' => $request['item_id']);
	$result = query($sql, $params);
	$bid_id = fetch($result);

	$sql = "INSERT INTO rating(bid_id, number, description)
			VALUES(:bid_id, :number, :desc)";		
	$params = array(':bid_id' => $bid_id['bid_id'], ':number' => $request['rating'], ':desc' => $request['desc']);	
	$result = query($sql, $params);	
}

$sql = "SELECT R.bid_id FROM rating R, bids B 
	    WHERE R.bid_id =  B.bid_id AND B.item_id = :item_id";
$params = array(':item_id' => $request['item_id']);
$rating = query($sql, $params);
$rating = fetch($rating);

if($rating == FALSE)
{
?>

<div class="section_description">Please Complete the Rating Form Below.</div>
<div class="section_content">
	<form id="rating_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>"><br>
			<b>Rate this sale between 1 and 5: </b><br><br>
			<p style="text-indent: 5em;">
				1 <input id="rating1" name="rating" type="radio" value="1"/>		
				2 <input id="rating2" name="rating" type="radio" value="2"/>		
				3 <input id="rating3" name="rating" type="radio" value="3"/>		
				4 <input id="rating4" name="rating" type="radio" value="4"/>		
				5 <input id="rating5" name="rating" type="radio" value="5"/><br><br>
			</p>
			
			<b>Write any comments that you have about this sale: </b><br><br>			
			<textarea rows="6" cols="50" name="desc" form="rating_form">
			Enter comments here...</textarea>
			
			<input type="hidden" id="item_id" name="item_id" value="<?php echo $request['item_id']?>">
			<br /><input name="submit" type="submit" value="Submit"/>
	</form>
</div>
<?php
}
else
{	
	message('success','You have successfully made a rating.');	
	$messages = formatMessages();
	echo (isset($messages)) ? $messages : '';
	
	$link = 'items/bought_items.php'; ?>
	<input type="hidden" id="item_id" name="item_id" value="<?php echo $request['item_id']?>">
	<br/><a class="button" href="<?php echo PATH.$link; ?>">Return to Bought Items</a>
	
<?php
}
?>	
