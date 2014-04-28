<?php 

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
		$date_searched = date("Y-m-d H:i:s");

		$sql = "INSERT INTO search_history
					(seller_id, search, date_searched)
					VALUES 
					(:seller_id, :search, :date_searched)";
		$params = array(':seller_id' => $_SESSION['user']['id'], 
						':search' => $request['search'], 
						':date_searched' => $date_searched);
		$result = query($sql,$params);
	}

	// parse the search history into single words.
	$words = explode(" ", $request['search']);

	$search_results = array();

	foreach($words as $word)
	{
		$sql = "SELECT *
					FROM items
					WHERE 
						name LIKE :search AND
						bin_price < :price";

		if(isset($request['price']))
		{
			if($request['price'] == "")
			{
				$request['price'] = 100000000000000000000000000000;
			}
		}
		else
		{
			$request['price'] = 100000000000000000000000000000;
		}

		$params = array(':search' => "%".$word."%", ':price' => $request['price']);

		//die(var_dump($params));
		$result = query($sql,$params);

		if ($result->rowCount() != 0)
		{ 
			while ($single_result = fetch($result)) 
			{
        		if(!in_array($single_result, $search_results))
        		{
					if(isset($request['zip']))
					{
						$sql = "SELECT lat, lon
									FROM zips
									WHERE zip = :zip";
						$params = array(':zip' => $request['zip']);
						$zips = query($sql,$params);
						$zips = fetch($zips);

						$sql = "SELECT lat, lon
									FROM zips
									WHERE zip = :zip";
						$params = array(':zip' => $single_result['location']);
						$result_zips = query($sql,$params);
						$result_zips = fetch($result_zips);

	        			$lat1 = $result_zips['lat'];
	        			$lat2 = $zips['lat'];
	        			$lon1 = $result_zips['lon'];
	 	       			$lon2 = $zips['lon'];

	    				$theta = $lon1 - $lon2;
						$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
						$dist = acos($dist);
						$dist = rad2deg($dist);
						$single_result['dist'] = $dist;
					}

					$search_results[] = $single_result;
				}
			}
		}
	}

	function aasort (&$array, $key) {
	    $sorter=array();
	    $ret=array();
	    reset($array);
	    foreach ($array as $ii => $va) {
	        $sorter[$ii]=$va[$key];
	    }
	    asort($sorter);
	    foreach ($sorter as $ii => $va) {
	        $ret[$ii]=$array[$ii];
	    }
	    $array=$ret;
	}

	if(isset($request['zip']))
	{
		aasort($search_results, "dist");
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
		<input id="search" name="search" type="text" class ="text" value=<?php if(isset($request['search'])) echo $request['search'] ?>><br/>
		Enter a Zip Code to Sort By Nearest Location:<br/>
		(Good test: 82801/54498)<br />
		<input id="zip" name="zip" type="text" class ="text"/><br/>
		Max Price:<br/>
		<input id="price" name="price" type="text" class ="text"><br/>

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
