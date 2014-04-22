<?php 
/* ************************************************************************************************
 * floor/check_on.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "Add Auction Item";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

if(isset($request['submit']))
{
	$success = 1;
	
	$varCategory = $_POST['category'];
	$errorMessage = "";
	
	if(($request['category']=="")) 
	{
		$success = 0;
		message('error','You forgot to select your category!');
	}

	
	if(!is_numeric($request['reservePrice']))	
	{
		// bad reserve Price
		$success = 0;
		message('error','Please only have numbers in your reserve price, not any commas or dollar signs.');
	}
	if(!is_numeric($request['BIN']))
	{
		//bad buy it now price
		$success = 0;
		message('error','Please only have numbers in your buy it now price, not any commas or dollar signs.');
	}
	
	

	if($success)
	{
		date_default_timezone_set('America/New_York'); 
		$join_date = date("Y-m-d H:i:s");
		
		$sql = "INSERT INTO items
				(seller_id, name, description, start_date, location, bin_price,reserve_price,url,category_id)
			   VALUES
				(:seller_id,:name,:description,:start_date,:location,:bin_price,:reserve_price,:url,:category_id);";
		//need to get seller ID!!
		$params = array(':seller_id' => $_SESSION['user']['id'],':name'=> $request['itemName'],':description'=>$request['desc'],':start_date'=>$join_date,
		':location'=>$request['LOI'],':bin_price'=>$request['BIN'],':reserve_price'=>$request['reservePrice'],':url'=> $request['url'],':category_id'=>$request['category']);
		
		$result = query($sql,$params);		
	
	
	
		message('success','You have created a new item!');
	}
}

// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_description">Please Complete the New Auction Item Form Below.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Name of item: <br/>
		<input id="itemName" name="itemName" type="text" class="text"/><br /><br />
		
		<!-- Reserve Price:(Once the bidding has reached the reserve price, the item can be bought by the bidder)<br/> -->
		Reserve Price: <br/>
		<input id="reservePrice" name="reservePrice" type="text" class="text"/><br />

		<!--Buy It Now Price: (The price at which the user can buy the item)<br/> -->
		Buy It Now Price: <br/>
		<input id="BIN" name="BIN" type="text" class="text"/><br /><br />
		
		Location of Item (Use a ZipCode):<br/>
		<input id="LOI" name="LOI" type="text" class="text"/><br />
		
		Main Category:
		<br/>
		<select name = "category">
		<option value="">Select Category</option>
		<?php
		$sql = "SELECT category_id, name FROM categories";
		$resultCat = query($sql);
		if($resultCat->rowCount()!=0)
		{
			while($single_resultCat = fetch($resultCat))
			{
				$search_resultsCats[] = $single_resultCat;
			}
		}

		foreach($search_resultsCats as $search_resultCat)
		{
		
		?>
		
		
			<option value=" <?php echo $search_resultCat['category_id']; ?>"><?php echo $search_resultCat['name']; ?></option>
			
		
		<?php
		}
		?>
		</select>
		<br/>
		
		URL of Item:<br/>
		<input id="url" name="url" type="text" class="text"/><br />


		Description of Item:<br />
		<textarea name="desc" id="desc"></textarea>
		<br/>


		<input name="submit" type="submit" value="Submit"/>
	
	</form>
</div>

<?php
	require '../includes/footer.inc.php';
?>
