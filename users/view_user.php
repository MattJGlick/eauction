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

$sql = "SELECT * FROM ADDRESSES WHERE seller_id = :seller_id";
$params = array('seller_id' => $_SESSION['user']['id']);
$result = query($sql,$params);

if(fetch($result,'count') != 0)
{
	while ($address = fetch($result)) 
	{
		$addresses[] = $address;
	}
}

$sql = "SELECT * FROM CREDIT_CARDS WHERE buyer_id = :buyer_id";
$params = array('buyer_id' => $_SESSION['user']['id']);
$result = query($sql,$params);

if(fetch($result,'count') != 0)
{
	while ($credit_card = fetch($result)) 
	{
		$credit_cards[] = $credit_card;
	}
}

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_content">
	<div class="section_title">Personal Information</div>
	<div class="section_title_divider"></div>				

	<body>
		<h1><b>Username:</b>
			<?php echo $_SESSION['user']['name']; ?></h1><br>
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

	<div class="section_title">Addresses</div>
	<div class="section_title_divider"></div>				

	<div class="section_content">
		<table cellspacing="0">
			<?php
			if(isset($addresses))
			{
				$count = 1;

				foreach($addresses as $address)
				{
					?>

					<tr class="tooltip_right">
						<td colspan="2"><b><?php echo $count ?></b></td>
						<td id="floor_total"><?php echo $address['street']." ".$address['city'].", ".$address['state']." ".$address['zip_code'] ?></td>
					</tr>

					<?php
					$count++;
				}
			}
			?>
		</table> 

		<div class="buttons"> 
			<br/><a class="button" href="<?php echo PATH.'users/new_address.php'; ?>">Add Address</a>
		</div>		
	</div>
	
	<div class="section_title">Credit Cards</div>
	<div class="section_title_divider"></div>				

	<div class="section_content">
		<table cellspacing="0">
			<?php
			if(isset($credit_cards))
			{
				$count = 1;

				foreach($credit_cards as $credit_card)
				{
					?>

					<tr class="tooltip_right">
						<td colspan="2"><b><?php echo $count ?></b></td>
						<td id="floor_total"><?php echo $credit_card['type']." - ".$credit_card['ccn'] ?></td>
					</tr>

					<?php
					$count++;
				}
			}
			?>
		</table> 

		<div class="buttons"> 
			<br/><a class="button" href="<?php echo PATH.'users/new_credit_card.php'; ?>">Add Credit Card</a>
		</div>		
	</div>

</div>

<?php
	require '../includes/footer.inc.php';
?>