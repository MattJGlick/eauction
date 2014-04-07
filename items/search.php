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
	
	
	

	if($success)
	{
		
	}
}

// Format messages for display
$messages = formatMessages();

?>
<?php echo (isset($messages)) ? $messages : '';?>

<div class="section_description">Please Complete the Search Form Below.</div>
<div class="section_content">
	<form id="person_search_form" class="input_text" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Search Categories:  <br/>
		<input id="categories" name="categories" type="text" class="text"/><br />
		
	

		Search item names:<br />
		<input id="itemNames" name="itemNames" type="text" class ="text"/><br/>
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
