<?php
/* ************************************************************************************************
 * includes/footer.inc.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: Page footer
 * 
 * ************************************************************************************************/
 unset($_SESSION['permissions']);
if (!$ajax) {
?>
		</div>
		<div id="footer">
		<?php if (MOBILE) {?>
				&#169; Penn State IFC/Panhellenic </br>Dance Marathon 2013
		<?php } else {?>
			<div class="footer_column" style="text-align:center;">
				<div class="footer_theme_logo"></div>
				&#169; Penn State IFC/Panhellenic </br>Dance Marathon 2013
			</div>
			<div class="footer_div"></div>
			<div class="footer_column">
				<b>Contact</b></br>
				pass@thon.org</br>
				1-800-392-THON
			</div>




		<?php }?>
		</div>
		
	</div>
	<?php if (DEBUG) { ?>
	<?php if (!empty($_SESSION['debug']['var'])){?>
	<table cellspacing="0" class="debug">
		<tr>
			<th colspan="3">Debug</th>
		</tr>
		<tr>
			<th>Name</th>
			<th>Value</th>
		</tr>
		<?php foreach ($_SESSION['debug']['var'] as $variable) {?>
		<tr>
			<td style="text-align:center;"><small><?php echo $variable['name']?><small></td>
			<td colspan="2"><small><pre><?php echo print_r($variable['value'],true)?></pre><small></td>
		</tr>
		<?php }?>
	</table>
	<?php } if ((!empty($_SESSION['debug']['sql'])) && (!MOBILE)){
	
		$time_total = 0;
		$num = 0;
		
		foreach ($_SESSION['debug']['sql'] as $query) {
		
			$num++;
			$time_total = $time_total + round($query['time'],3);
			
		}
	?>
	<table cellspacing="0" class="debug">
		<tr>
			<th colspan="100%">Total <i><?php echo $num?></i> queries took <i><?php echo $time_total?></i> ms</th>
		</tr>		
		<tr>
			<th>Number</th>
			<th>Query</th>
			<th>Rows</th>
			<th>Time (ms)</th>
		</tr>
		<?php foreach ($_SESSION['debug']['sql'] as $id => $query) {?>
		<tr>
			<td style="text-align:center;"><small><?php echo $id+1?><small></td>
			<td><small><?php echo PMA_SQP_formatHtml(PMA_SQP_parse($query['query']))?><small></td>
			<td style="text-align:center;"><small><?php echo $query['rows']?><small></td>
			<td style="text-align:center;"><small><?php echo round($query['time'],3)?><small></td>
		</tr>
		<?php }?>
	</table>
	
	<?php }?>
	<?php }?>

</body>
</html>
<?php } $db=null; ?>