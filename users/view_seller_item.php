<?php 
/* ************************************************************************************************
 * floor/check_on.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "User Items";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

if(isset($_SESSION['user']['id']))
{
	$sql = "SELECT * FROM items where items.seller_id = :seller_id";
	$params = array(':seller_id'=>$_SESSION['user']['id']);
	$result = query($sql,$params);
	
	if($result->rowCount() != 0)
	{
		while($single_result = fetch($result))
		{
			$search_results[] = $single_result;
		}	
	}
}



// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_description">User Items.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
	
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
							<td><a href="<?php echo PATH.'items/view_item.php?item_id	='.$search_result['item_id']?>"><?php echo $search_result['name'];?></a></td>
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
