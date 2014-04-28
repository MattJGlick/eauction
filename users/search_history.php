<?php 

$page_title = "Item Search History";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

$sql = "SELECT * FROM search_history WHERE 
			seller_id = :seller_id
			ORDER BY search_id DESC";
$params = array('seller_id' => $_SESSION['user']['id']);
$result = query($sql,$params);

if(fetch($result,'count') != 0)
{
	while ($view = fetch($result)) 
	{
		$views[] = $view;
	}
}

$messages = formatMessages();
echo (isset($messages)) ? $messages : ''; ?>

<div class="section_content">
	<div class="section_title">Previous Searches</div>
	<div class="section_title_divider"></div>				
		<div class="section_content">
			<table cellspacing="0">
				<tr>
					<th>Count</th>
					<th>Item Name</th>
					<th>Date Browsed</th>
				</tr>

				<?php
				if(isset($views))
				{
					$count = 1;

					foreach($views as $view)
					{
						?>

						<tr class="tooltip_right">
							<td><b><?php echo $count ?></b></td>
							<td><?php echo $view['search'] ?></td>
							<td><?php echo $view['date_searched'] ?></td>
						</tr>

						<?php
						$count++;
					}
				}
				?>
			</table> 
		</div>					
	</div>
</div>

<?
require '../includes/footer.inc.php';
?>
