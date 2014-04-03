<?php 
/* ************************************************************************************************
 * 
 * @author: Matt Welk
 * 
 * ************************************************************************************************/
$page_title = "Profile Information";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Format messages for display
$messages = formatMessages();

$query = "SELECT * FROM people WHERE first_name = 'Matt'";
$result = mysql_query($query);
$row = 0;

$password = mysql_result($result, $row, "password");
$firstName = mysql_result($result, $row, "first_name");
$lastName = mysql_result($result, $row, "last_name");
$email = mysql_result($result, $row, "email");
$phoneNumber = mysql_result($result, $row, "phone_number");
$age = mysql_result($result, $row, "age");
$gender = mysql_result($result, $row, "gender");
$income = mysql_result($result, $row, "annual_income");

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_content">
	<body>
		<h1>Username:</h1><br>
			<p><?php echo $username; ?></p><br>
		<h1>Password:</h1><br>
			<p><?php echo $password; ?></p><br>
		<h1>First Name:</h1><br>
			<p><?php echo $first_name; ?></p><br>
		<h1>Last Name:</h1><br>
			<p><?php echo $last_name; ?></p><br>
		<h1>Email:</h1><br>
			<p><?php echo $email; ?></p><br>
		<h1>Phone Number:</h1><br>
			<p><?php echo $phone_number; ?></p><br>
		<h1>Age:</h1><br>
			<p><?php echo $age; ?></p><br>
		<h1>Gender:</h1><br>
			<p><?php echo $gender; ?></p><br>
		<h1>Annual Income:<h1/><br>
			<p><?php echo $annual_income; ?></p><br>
	</body>
</div>

<?php
	require '../includes/footer.inc.php';
?>