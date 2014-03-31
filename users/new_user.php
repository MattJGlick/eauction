<?php 
/* ************************************************************************************************
 * floor/check_on.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: Check a guest onto the floor.
 * 
 * ************************************************************************************************/
$page_title = "User Registration";
$body_type =  $page_title;

$override = array();

// Validate and initialize inputs
$request = $_REQUEST;

// CODE TO SUBMIT NEW USER
// die(var_dump($request));

require '../includes/html.header.inc.php';
// Format messages for display
$messages = formatMessages();

?>

<div class="section_description">Please Complete the User Registration Form Below.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Username: <br/>
		<input id="username" name="username" type="text" class="text"/><br />
		Password:<br/>
		<input id="password" name="password" type="text" class="text"/><br />
		Confirm Password:<br/>
		<input id="confirm_password" name="confirm_password" type="text" class="text"/><br /><br/>
		First Name:<br/>
		<input id="first_name" name="first_name" type="text" class="text"/><br />
		Last Name:<br/>
		<input id="last_name" name="last_name" type="text" class="text"/><br />
		Email:<br/>
		<input id="email" name="email" type="text" class="text"/><br />
		Phone Number:<br/>
		<input id="phone_number" name="phone_number" type="text" class="text"/><br /><br/>
		Age:<br/>
		<input id="age" name="age" type="text" class="text"/><br />

		<select>
			<option value="male">Male</option>
			<option value="female">Female</option>
		</select>

		Annual Income:<br/>
		<input id="annual_income" name="annual_income" type="text" class="text"/><br /><br />

		<input name="submit" type="submit" value="Submit"/>
	
	</form>
</div>

<?php
	require '../includes/footer.inc.php';
?>
