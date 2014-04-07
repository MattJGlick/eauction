<?php 
/* ************************************************************************************************
 * users/browser_history.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: browsing history of the current user
 * 
 * ************************************************************************************************/
$page_title = "Item Browsing History";
$body_type =  $page_title;

require '../includes/html.header.inc.php';

// Validate and initialize inputs
$request = $_REQUEST;

$sql = "SELECT * FROM browsing_history WHERE 
			buyer = :buyer_id";
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
	<div class="section_title">Previous Orders</div>
	<div class="section_title_divider"></div>				
		<div class="section_content">
			<table cellspacing="0">
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
							<td colspan="2"><b><?php echo $count ?></b></td>
							<td id="floor_total"><?php echo "Item: ".$item['name'] ?></td>
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
