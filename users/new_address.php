<?php 
/* ************************************************************************************************
 * users/new_address.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Allow user to submit an address
 * 
 * ************************************************************************************************/
$page_title = "Add Address";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

if(isset($request['submit']))
{
	$success = 1;

	if(!is_numeric($request['zip']))	
	{
		// phone number is bad
		$success = 0;
		message('error','Please only user numbers in the zip code area');
	}


	if($success)
	{
		$sql = "INSERT INTO addresses
					(seller_id, street, city, state, zip_code)
				VALUES
					(:seller_id, :street, :city, :state, :zip_code);";
		$params = array(':seller_id' => $_SESSION['user']['id'], ':street' => $request['street'],
						':city' => $request['city'], ':state' => $request['state'],
						':zip_code' => $request['zip_code']);
		$result = query($sql,$params);

		message('success','You have added an address!');		
	}
}

die(var_dump($_SESSION['user']));

$messages = formatMessages();
echo (isset($messages)) ? $messages : ''; ?>

	<div class="section_description">Please add an address below.</div>
	<div class="section_content">
		<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
			Street:<br/>
			<input id="street" name="street" type="text" class="text"/><br />
			City:<br/>
			<input id="city" name="city" type="text" class="text"/><br />
			State:<br/>
			<input id="state" name="state" type="text" class="text"/><br />			
			Zip Code:<br/>
			<input id="state" name="state" type="text" class="text"/><br />						

				<input name="submit" type="submit" value="Submit"/>
		</form>
	</div>

<?
require '../includes/footer.inc.php';
?>
