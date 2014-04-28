<?php 

$page_title = "Telemarketer Report";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

$sql = "SELECT * FROM people";
$result = query($sql);

if($result->rowCount() != 0)
{
	while($single_result = fetch($result))
	{
		$search_results[] = $single_result;
	
	
	}
	
}



// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_description">Telemarketer Report.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
	
	<?php
		if(isset($search_results))
		{
			?>

			<br/><br/>
			<div class="section_title_divider"></div>
			<div class="section_content">
				<table cellspacing="0">
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Phone Number</th>
						<th>Email</th>
						<th>Age</th>
						<th>Gender</th>
						<th>Income</th>
						<th>Address</th>
					</tr>
					<?php foreach ($search_results as $search_result){
						$sql2 = "SELECT * FROM addresses where addresses.seller_id =  :seller_id";
						$params2 = array(':seller_id'=>$search_result['seller_id']);
						$results2 = query($sql2,$params2);
						$search_results2 ="";
						if($results2->rowCount() !=0)
						{
							
							while($single_results2 = fetch($results2))
							{
								$search_results2 .=$single_results2['street']." ".$single_results2['city']." ".$single_results2['state']." ".$single_results2['zip_code']."<br/><br/>";
							}
							
						}
					
					?>
						<tr >
							<td><?php echo $search_result['first_name']; ?></td>
							<td><?php echo $search_result['last_name']; ?></td>
							<td><?php echo $search_result['phone_number']; ?></td>
							<td><?php echo $search_result['email']; ?></td>
							<td><?php echo $search_result['age']; ?></td>
							<td><?php echo $search_result['gender']; ?></td>
							<td><?php echo $search_result['annual_income']; ?></td>
							<td><?php echo $search_results2; ?></td>
						</tr>
					<?php }?>
				</table>
			</div>			
			
			<?php
		}
		
		?>
	
	</form>
</div>

<?php
	require '../includes/footer.inc.php';
?>
