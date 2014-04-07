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
	
	if(!is_numeric($request['rate']))	
	{
		// rating is bad
		$success = 0;
		message('error','Please only have numbers in your rating, not any commas or letters.');
	}
	if($request['rate'] >10 || $request['rate']<0)
	{
		//number is bad
		$success = 0;
		message('error','Please use a rating scale between 1 and 10.');
	}
	

	if($success)
	{
		$sql = "INSERT INTO rating
				(bid_id, number, description)
				VALUES
				(:bid_id,:number,:description);";
				
				
		//update bid id		
		$params = array(':bid_id'=> '1',':number'=>$request['rate'],':description'=>$request['desc']);		
		
	
	
		$result = query($sql,$params);	
	}
}

// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_description">Please Complete the New Auction Item Form Below.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Rate the seller: (use a scale of 1 to 10. 10 being the best seller. 1 being the worst seller) <br/>
		<input id="rate" name="rate" type="text" class="text"/><br /><br />
		
	

		Description of Seller:<br />
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
