<?php 

$page_title = "Search Items";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

if(isset($request['submit']))
{
	$words = explode(" ", $request['search']);

	$search_results = array();

	foreach($words as $word)
	{
		$sql = "SELECT *
					FROM auctions_complete A, bids B, items S
					WHERE 
						name LIKE :search and A.bid_id = B.bid_id and B.item_id = S.item_id";

		

		$params = array(':search' => "%".$word."%");

		
		$result = query($sql,$params);
		//die(var_dump($result));
		if ($result->rowCount() != 0)
			{		{ 
			while ($single_result = fetch($result)) 

        		if(!in_array($single_result, $search_results))
        		{
					$search_results[] = $single_result;
				}
			}
		}
	}
	
}


// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_description">Please Complete the Search Form Below to Confirm Item.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Search Item:<br />
		<input id="search" name="search" type="text" class ="text" value=<?php if(isset($request['search'])) echo $request['search'] ?>><br/>

		<input name="submit" type="submit" value="Search"/>
		
		<?php
		if(isset($search_results))
		{
			?>

			<br/><br/>
			<div class="section_title_divider"></div>
			<div class="section_content">
				<table cellspacing="0">
					<tr>
						<th>Name</th>
										
					</tr>
					<?php
					if(count($search_results) == 0)
					{ ?>
						<td colspan="7">There are no results to display</td>
					<?php } 
					else
					{	
						
						 foreach ($search_results as $search_result) 
						{
							

							?>
							<tr >
								<td><a href="<?php echo PATH.'admin/confirmation_item.php?item_id	='.$search_result['item_id']?>"><?php echo $search_result['name'];?></a></td>
								
																																				
							</tr>
						<?php }
						
					}?>
				</table>
				 
			</div>			
			
			<?php
		}
		
		
		?>
	
	</form>
</div>

<?php
	require '../includes/footer.inc.php';
?>
