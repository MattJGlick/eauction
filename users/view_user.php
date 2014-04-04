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

$sql = "SELECT * FROM people WHERE seller_id = :seller_id";
$params = array(':seller_id' => $_SESSION['user']['id']);
$result = query($sql,$params);
$row = fetch($result);

$sql = "SELECT username FROM sellers WHERE seller_id = :seller_id";
$params = array('seller_id' => $_SESSION['user']['id']);
$result = query($sql,$params);
$row2 = fetch($result);

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_content">
	<body>
		<h1><b>Username:</b>
			<?php echo $row2['username']; ?></h1><br>
		<h1><b>First Name:</b>
			<?php echo $row['first_name']; ?></h1><br>
		<h1><b>Last Name:</b>
			<?php echo $row['last_name']; ?></h1><br>
		<h1><b>Email:</b>
			<?php echo $row['email']; ?></h1><br>
		<h1><b>Phone Number:</b>
			<?php echo $row['phone_number']; ?></h1><br>
		<h1><b>Age:</b>
			<?php echo $row['age']; ?></h1><br>
	</body>
</div>

<?php
	require '../includes/footer.inc.php';
?>