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
		
		Location of Item:<br/>
		<input id="LOI" name="LOI" type="text" class="text"/><br />

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
