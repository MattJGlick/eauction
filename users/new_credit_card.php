<?php 
/* ************************************************************************************************
 * users/new_credit_card.php
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
	
	if(!is_numeric($request['security_code']))	
	{
		$success = 0;
		message('error','Please only user numbers in the security code area');
	}

	if(!is_numeric($request['ccn']))	
	{
		$success = 0;
		message('error','Please only user numbers in the credit card number');
	}

	if($request['type'] != "Mastercard" && $request['type'] != "Visa" && $request['type'] != "American Express")	
	{
		$success = 0;
		message('error','Please use a correct credit card type');
	}	

	if($success)
	{
		$sql = "INSERT INTO credit_cards
					(buyer_id, ccn, security_code, type, exp_date)
				VALUES
					(:buyer_id, :ccn, :security_code, :type, :exp_date);";
		$params = array(':buyer_id' => $_SESSION['user']['id'], ':ccn' => $request['ccn'],
						':security_code' => $request['security_code'], ':type' => $request['type'],
						':exp_date' => $request['exp_date']);
		$result = query($sql,$params);

		message('success','You have added a credit card!');		
	}
}

$messages = formatMessages();
echo (isset($messages)) ? $messages : ''; ?>

	<div class="section_description">Please add a credit card below.</div>
	<div class="section_content">
		<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
			Credit Card Number:<br/>
			<input id="ccn" name="ccn" type="text" class="text"/><br />
			Security Code:<br/>
			<input id="security_code" name="security_code" type="text" class="text"/><br />
			Type (MasterCard, Visa, American Express):<br/>
			<input id="type" name="type" type="text" class="text"/><br />			
			Exp Date:<br/>
			<input id="exp_date" name="exp_date" type="text" class="text"/><br />						

			<input name="submit" type="submit" value="Submit"/>
		</form>
	</div>

	<div>
		<a href="<?php echo PATH.'users/view_user.php'; ?>">Return to User Profile</a>
	</div>

<?
require '../includes/footer.inc.php';
?>
