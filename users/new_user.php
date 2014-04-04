<?php 
/* ************************************************************************************************
 * users/new_user.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * ************************************************************************************************/
$page_title = "User Registration";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

if(isset($request['submit']))
{
	$success = 1;

	// check to see if the username is valid
	$sql = "SELECT COUNT(*) as count
		FROM sellers
		WHERE username = :username";
		
	$params = array(':username' => $request['username']);
	$result = query($sql,$params);
	$row = fetch($result);
		
	if($row['count'] != "0")
	{
		// username is already taken
		$success = 0;
		message('error','This username is already taken! Please choose another.');
	}

	if($request['password'] !== $request['confirm_password'])
	{
		// now we need to check the passwords to make sure they are the same
		$success = 0;
		message('error','Make sure that the passwords are the same!');
	}

	if(strlen($request['phone_number']) != 10 || !is_numeric($request['phone_number']))	
	{
		// phone number is bad
		$success = 0;
		message('error','Phone number is not correct! Please use: 8885551122 format.');

	}
	
	if(!is_numeric($request['annual_income']))	
	{
		// phone number is bad
		$success = 0;
		message('error','Please only have numbers in your salary, not any commas or dollar signs.');
	}

	if($success)
	{
		// hash the password
		$password = md5($request['password']);
		date_default_timezone_set('America/New_York'); 
		$join_date = date("Y-m-d H:i:s");

		// insert them into the seller table
		$sql = "INSERT INTO sellers
					(username, password, join_date, user_type)
				VALUES
					(:username, :password, :join_date, :user_type);";
		$params = array(':username' => $request['username'], ':password' => $password,
						':join_date' => $join_date, ':user_type' => 'person');
		$result = query($sql,$params);
		
		// retrieve the seller id
		$sql = "SELECT MAX(seller_id) as seller_id
			FROM sellers";
			
		$params = array();
		$result = query($sql,$params);
		$row = fetch($result);

		// insert them into the people table
		$sql = "INSERT INTO people
					(seller_id, email, age, phone_number, first_name, last_name, gender, annual_income)
				VALUES
					(:seller_id, :email, :age, :phone_number, :first_name, :last_name, :gender, :annual_income);";
		$params = array(':email' => $request['email'], ':age' => $request['age'],
						':phone_number' => $request['phone_number'], ':first_name' => $request['first_name'],
						':last_name' => $request['last_name'], ':gender' => $request['gender'],
						':annual_income' => $request['annual_income'], ':seller_id' => $row['seller_id']);
		$result = query($sql,$params);		

		$_SESSION['user']['id'] = $row['seller_id'];
		$_SESSION['user']['name'] = $request['first_name']." ".$request['last_name'];
		$_SESSION['user']['username'] = $request['username'];
	}
}

if(isset($_SESSION['user']['id']))
{
	if($_SESSION['user']['id'] != NULL)
	{
		message('success','You have successfully registered!');
	}
}

$messages = formatMessages();
echo (isset($messages)) ? $messages : '';

if(!isset($_SESSION['user']['id']))
{
	?>
		<div class="section_description">Please Complete the User Registration Form Below.</div>
		<div class="section_content">
			<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
				Username: <br/>
				<input id="username" name="username" type="text" class="text"/><br />
				Password:<br/>
				<input id="password" name="password" type="password" class="text"/><br />
				Confirm Password:<br/>
				<input id="confirm_password" name="confirm_password" type="password" class="text"/><br /><br/>
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
				Gender:<br />
				<input id="gender" name="gender" type="text" class="text"/><br />
				<!--<select>
					<option value="male">Male</option>
					<option value="female">Female</option>
				</select><br/> -->

				Annual Income:<br/>
				<input id="annual_income" name="annual_income" type="text" class="text"/><br /><br />

				<input name="submit" type="submit" value="Submit"/>
			
			</form>
		</div>

	<?php
}
	require '../includes/footer.inc.php';
?>
