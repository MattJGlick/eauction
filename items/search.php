<?php 
/* ************************************************************************************************
 * items/search.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "Search Items";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

if(isset($request['submit']))
{
	//save the search history
	if(isset($_SESSION['user']['id']))
	{
		date_default_timezone_set('America/New_York'); 
		$date_browsed = date("Y-m-d H:i:s");

		$sql = "INSERT INTO search_history
					(seller_id, item_id, date_browsed)
					VALUES 
					(:seller_id, :search, :date_browsed)";
		$params = array(':seller_id' => $_SESSION['user']['id'], 
						':search' => $request['search'], 
						':date_browsed' => $date_browsed);
		$result = query($sql,$params);
	}

	// parse the search history into single words.
	$words = explode(" ", $request['search']);

	$search_results = array();

	foreach($words as $word)
	{
		$sql = "SELECT *
					FROM items
					WHERE name LIKE :search";
		$params = array(':search' => "%".$word."%");
		$result = query($sql,$params);

		if ($result->rowCount() != 0)
		{ 
			while ($single_result = fetch($result)) 
			{
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

<div class="section_description">Please Complete the Search Form Below.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Search:<br />
		<input id="search" name="search" type="text" class ="text"/><br/>

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
						<th>Description</th>
						<th>Location</th>						
						<th>Buy It Now</th>
					</tr>
					<?php foreach ($search_results as $search_result) 
					{?>
						<tr >
							<td><a href="<?php echo PATH.'items/view_item.php?item='.$search_result['item_id']?>"><?php echo $search_result['name'];?></a></td>
							<td><?php echo $search_result['description']; ?></td>
							<td><?php echo $search_result['location']; ?></td>
							<td><?php echo $search_result['bin_price']; ?></td>																					
						</tr>
					<?php }?>
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
