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
	message('error',$request['category']);
	
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
				(seller_id, name, description, start_date, location, bin_price,reserve_price,url)
			   VALUES
				(:seller_id,:name,:description,:start_date,:location,:bin_price,:reserve_price,:url);";
		//need to get seller ID!!
		$params = array(':seller_id' => $_SESSION['user']['id'],':name'=> $request['itemName'],':description'=>$request['desc'],':start_date'=>$join_date,
		':location'=>$request['LOI'],':bin_price'=>$request['BIN'],':reserve_price'=>$request['reservePrice'],':url'=> '');
		
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
			<option value="Book">Book</option>
			<option value="Consumer Electronics">Consumer Electronics</option>
			<option value="Computer & Tablets">Computers & Tablets</option>
			<option value="DVD & Movies">DVD & Movies</option>
			<option value="Music">Music</option>
			<option value="Motor Vehicles">Motor Vehicles</option>
			<option value="Clothing, Shoes & Accessories">Clothing, Shoes & Accessories</option>
			<option value="Cell Phone & Accessories">Cell Phones & Accessories</option>
			<option value="Home & Garden">Home & Garden</option>
			<option value="Jewelry">Jewelry</option>
			<option value="Sporting Goods">Sporting Goods</option>
			<option value="Pet Supplies">Pet Supplies</option>
			<option value="Tickets">Tickets</option>
			<option value="Video Games & Consoles">Video Games & Consoles</option>
			<option value="Toys & Games">Toys & Games</option>
		</select>
		
		
		
		<br/>

		Description of Item:<br />
		<textarea name="desc" id="desc"></textarea>
		<!--<select>
			<option value="male">Male</option>
			<option value="female">Female</option>
		</select><br/> -->

		<input name="submit" type="submit" value="Submit"/>
	
	</form>
</div>

<?php
	require '../includes/footer.inc.php';
?>
