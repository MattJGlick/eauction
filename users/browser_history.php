<?php 

$page_title = "Item Browsing History";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

$sql = "SELECT * FROM browsing_history WHERE 
			buyer_id = :buyer_id";
$params = array('buyer_id' => $_SESSION['user']['id']);
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
	<div class="section_title">Previous Item Views</div>
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
						$sql = "SELECT * FROM items WHERE 
									item_id = :item_id";
						$params = array('item_id' => $view['item_id']);
						$result = query($sql,$params);
						$item = fetch($result);

						?>

						<tr class="tooltip_right">
							<td><b><?php echo $count ?></b></td>
							<td><a href="<?php echo PATH.'items/view_item.php?item_id	='.$item['item_id']?>"><?php echo $item['name'];?></a></td>
							<td><?php echo $view['date_browsed'] ?></td>
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
