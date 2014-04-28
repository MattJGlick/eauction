<?php 
/* ************************************************************************************************
 * items/search.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "Browse Items";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

if(!isset($request['category_id']))
	$request['category_id'] = 0;

$search_results = array();

if($request['category_id'] == 0)
{
	$sql = "SELECT *
				FROM items";
	$items = query($sql);
}
else
{
	$sql = "SELECT *
				FROM items
				WHERE 
				category_id = :category_id
				OR category_id = (SELECT parent_id FROM categories WHERE category_id = :category_id)";

	$params = array(':category_id' => $request['category_id']);
	$items = query($sql,$params);
}

if ($items->rowCount() != 0)
{ 
	while ($single_result = fetch($items)) 
	{
		if(!in_array($single_result, $search_results))
		{
			$search_results[] = $single_result;
		}
	}
}


$sql = "SELECT *
			FROM categories
			WHERE 
			category_id = :category_id";

$params = array(':category_id' => $request['category_id']);
$category = query($sql,$params);
$category = fetch($category);


$sql = "SELECT *
			FROM categories
			WHERE 
			category_id = (SELECT parent_id FROM categories WHERE category_id = :category_id)";
$params = array(':category_id' => $request['category_id']);
$parent_category = query($sql,$params);
$parent_category = fetch($parent_category);


$sql = "SELECT *
			FROM categories
			WHERE 
			parent_id = :category_id";
$params = array(':category_id' => $request['category_id']);
$children = query($sql,$params);

$sql = "SELECT COUNT(*)
			FROM items";
$totalCount = query($sql);
$totalCount = fetch($totalCount);
$totalCount = $totalCount['COUNT(*)'];

// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_description">Please Complete the Search Form Below.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		<?php
		echo "<a href='http://localhost/eauction/items/browse_items.php?category_id=0'>All (".$totalCount.") </a>";

		if($parent_category)
		{
			$sql = "SELECT COUNT(*)
						FROM items
						WHERE category_id = :category_id";
			$params = array(':category_id' => $parent_category['category_id']);						
			$catCount = query($sql, $params);
			$catCount = fetch($catCount);
			$catCount = $catCount['COUNT(*)'];


			$sql = "SELECT COUNT(*)
						FROM items
						WHERE category_id IN (SELECT category_id FROM categories WHERE parent_id = :category_id)";
			$params = array(':category_id' => $parent_category['category_id']);						
			
			$parentCount = query($sql, $params);
			$parentCount= fetch($parentCount);
			$parentCount = $parentCount['COUNT(*)'];

			$catCount = $catCount + $parentCount;


			$link = "http://localhost/eauction/items/browse_items.php?category_id=".$parent_category['category_id'];
			echo " -> <a href='$link'>".$parent_category['name']."(".$catCount.")</a>";
		}

		if($category)
		{
			$sql = "SELECT COUNT(*)
						FROM items
						WHERE category_id = :category_id";
			$params = array(':category_id' => $category['category_id']);						
			$catCount = query($sql, $params);
			$catCount = fetch($catCount);
			$catCount = $catCount['COUNT(*)'];


			$sql = "SELECT COUNT(*)
						FROM items
						WHERE category_id IN (SELECT category_id FROM categories WHERE parent_id = :category_id)";
			$params = array(':category_id' => $category['category_id']);						
			
			$parentCount = query($sql, $params);
			$parentCount= fetch($parentCount);
			$parentCount = $parentCount['COUNT(*)'];

			$catCount = $catCount + $parentCount;

			$link = "http://localhost/eauction/items/browse_items.php?category_id=".$category['category_id'];
			echo " -> <a href='$link'>".$category['name']."(".$catCount.")</a>";
		}		

		echo ":";

		while ($child = fetch($children)) 
		{
			$sql = "SELECT COUNT(*)
						FROM items
						WHERE category_id = :category_id";
			$params = array(':category_id' => $child['category_id']);						
			$childCount = query($sql, $params);
			$childCount = fetch($childCount);
			$childCount = $childCount['COUNT(*)'];
			
			$sql = "SELECT COUNT(*)
						FROM items
						WHERE category_id IN (SELECT category_id FROM categories WHERE parent_id = :category_id)";
			$params = array(':category_id' => $child['category_id']);						
			
			$parentCount = query($sql, $params);
			$parentCount= fetch($parentCount);
			$parentCount = $parentCount['COUNT(*)'];

			$childCount = $childCount + $parentCount;

			$link = "http://localhost/eauction/items/browse_items.php?category_id=".$child['category_id'];
			echo "<a href='$link'>".$child['name']."(".$childCount.")</a> ";
		}

		?>

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
						<th>Number of Bids</th>	
						<th>End Date</th>							
						<th>Buy It Now</th>
						<th>Seller Rating</th>						
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
							$sql = "SELECT COUNT(*)
										FROM bids
										WHERE item_id = :item_id";
							$params = array(':item_id' => $search_result['item_id']);
							$result = query($sql,$params);
							$result = fetch($result);

							$end_date = date('Y-m-d', strtotime($search_result['start_date'].' + 2 weeks'));

							$sql = "SELECT AVG(r.number)
										FROM rating r, items i, bids b
										WHERE 
											b.bid_id = r.bid_id AND
											b.item_id = i.item_id AND
											i.seller_id = :seller_id";
							$params = array(':seller_id' => $search_result['seller_id']);
							$rating = query($sql,$params);
							$rating = fetch($rating);
							//die(var_dump($rating));
							if($rating['AVG(r.number)'] == NULL)
								$rating = "Not yet rated.";
							else
								$rating = $rating['AVG(r.number)'];

							?>
							<tr >
								<td><a href="<?php echo PATH.'items/view_item.php?item_id	='.$search_result['item_id']?>"><?php echo $search_result['name'];?></a></td>
								<td><?php echo $search_result['description']; ?></td>
								<td><?php echo $search_result['location']; ?></td>
								<td><?php echo $result['COUNT(*)']; ?></td>
								<td><?php echo $end_date; ?></td>
								<td><?php echo $search_result['bin_price']; ?></td>																					
								<td><?php echo $rating ?></td>																													
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
