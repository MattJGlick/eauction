<?php 
/* ************************************************************************************************
 * items/search.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "Confirm Item";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;


if(isset($request['submit']))
{
	date_default_timezone_set('America/New_York');
	$current_date = date("Y-m-d H:i:s");
	
	$sql3 = " SELECT A.bid_id
		From auctions_complete A, bids B, items S 
		where A.bid_id = B.bid_id and B.item_id = S.item_id  AND S.item_id = :item_id";
		
	$params3 = array(':item_id' => $request['item_id']);

		
	$result3 = query($sql3,$params3);
	
	$result3 = fetch($result3);


	if(isset($request['date_money_sent']))
	{
		if($request['date_money_sent'] == 'yes')
		{
			$sql2 = "UPDATE auctions_complete
					SET date_money_sent = :current_date
					where bid_id = :bid_id";
					
			
			$params2 = array(':current_date'=>$current_date,':bid_id' =>$result3['bid_id']);
			$results2 = query($sql2,$params2);
		}
	}
	
	if(isset($request['date_money_received']))
	{	
		if($request['date_money_received'] == 'yes')
		{
			$sql2 = "UPDATE auctions_complete
					SET date_money_received = :current_date
					where bid_id = :bid_id";
					
			
			$params2 = array(':current_date'=>$current_date,':bid_id' =>$result3['bid_id']);
			$results2 = query($sql2,$params2);
		}
	}
	if(isset($request['date_item_sent']))
	{	
		if($request['date_item_sent'] == 'yes')
		{
			$sql2 = "UPDATE auctions_complete
					SET date_item_sent = :current_date
					where bid_id = :bid_id";
					
			
			$params2 = array(':current_date'=>$current_date,':bid_id' =>$result3['bid_id']);
			$results2 = query($sql2,$params2);
		}
	}
	if(isset($request['date_item_received']))
	{	
		if($request['date_item_received'] == 'yes')
		{
			$sql2 = "UPDATE auctions_complete
					SET date_item_received = :current_date
					where bid_id = :bid_id";
					
			
			$params2 = array(':current_date'=>$current_date,':bid_id' =>$result3['bid_id']);
			$results2 = query($sql2,$params2);
		}
	}
	
	

}

$sql = "select S.name, A.date_item_received,A.date_money_received,A.date_item_sent,A.date_money_sent 
		From auctions_complete A, bids B, items S 
		where A.bid_id = B.bid_id and B.item_id = S.item_id  AND S.item_id = :item_id";
		
$params = array(':item_id' => $request['item_id']);

		
$result = query($sql,$params);

if($result->rowCount() != 0)
{
	while($single_result = fetch($result))
	{
		$search_results[] = $single_result;
	}	
}

//die(var_dump($search_results['date_item_received']));

// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_description">Confirm Item.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
	
	<div class="section_title_divider"></div>
			<div class="section_content">
				<table cellspacing="0">
					<tr>
						<th>Name</th>
						<th>Money Sent</th>
						<th>Money Received</th>						
						<th>Item Sent</th>
						<th>Item Received</th>
						
					</tr>
					
						<tr >
						<?php 
						foreach ($search_results as $search_result) 
						{
							

							?>
							<tr >
							
								<td><?php echo $search_result['name']; ?></td>
								<?php
								if($search_result['date_money_sent'] == '0000-00-00 00:00:00')
								{
								
									?><td> <input type="checkbox" name="date_money_sent" value="yes"><br></td> <?php
								}
								else
								{
									?> <td><?php echo $search_result['date_money_sent']; ?></td> <?php
								}
								?>
								
								<?php
								if($search_result['date_money_received'] == '0000-00-00 00:00:00')
								{
								
									?><td> <input type="checkbox" name="date_money_received" value="yes"><br></td> <?php
								}
								else
								{
									?> <td><?php echo $search_result['date_money_received']; ?></td> <?php
								}
								?>
								<?php
								if($search_result['date_item_sent'] == '0000-00-00 00:00:00')
								{
								
									?><td> <input type="checkbox" name="date_item_sent" value="yes"><br></td> <?php
								}
								else
								{
									?> <td><?php echo $search_result['date_item_sent']; ?></td> <?php
								}
								?>
								<?php
								if($search_result['date_item_received'] == '0000-00-00 00:00:00')
								{
								
									?><td> <input type="checkbox" name="date_item_received" value="yes"><br></td> <?php
								}
								else
								{
									?> <td><?php echo $search_result['date_item_received']; ?></td> <?php
								}
								?>
								
								
																																				
							</tr>
						<?php	
						 }

						?>
						</tr>
					
				</table>
			</div>
			<input type="hidden" id="item_id" name="item_id" value="<?php echo $request['item_id']?>">
			<input name="submit" type="submit" value="Confirm"/>
	
	
	</form>
</div>

<?php
	require '../includes/footer.inc.php';
?>
