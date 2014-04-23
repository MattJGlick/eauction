<?php
/* ************************************************************************************************
 * includes/header.inc.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Header for pages that require authenticated users.
 * 
 * ************************************************************************************************/

$body_type = $page_title;
//debug($_SESSION['user']);
//Start session
@session_start();

require_once 'db.inc.php';
require_once 'functions.inc.php';
require_once 'config.inc.php';
// Check if current user is allowed to view this page
 
$request = $_REQUEST;
 
if (isset($request['action']))
{
	if($request['action'] == 'search')
	{
	   header( 'Location: http://localhost/eauction/items/search.php?search='.$request['search']."&submit=true") ;
	}

	if($request['action'] == 'login')
	{
		$sql = "SELECT * FROM sellers WHERE username = :username";
		$params = array(':username' => $request['username']);
		$result = query($sql,$params);
		$row = fetch($result);
		
		// hash the password
		$password = md5($request['password']);
		
		if($password != $row['password'])
		{
			// username or password is incorrect
			$success = 0;
			message('error','This username or password is incorrect! Please enter a valid username and password.');
		}
		else
		{
			$_SESSION['user']['id'] = $row['seller_id'];	
			$_SESSION['user']['username'] = $request['username'];

			$sql = "SELECT * FROM people WHERE seller_id = :seller_id";
			$params = array(':seller_id' => $row['seller_id']);
			$result = query($sql,$params);
			$row = fetch($result);
			
			$_SESSION['user']['name'] = $row['first_name']." ".$row['last_name'];
		}
	}
} 
 
// check to see what auctions are complete
$current_date = date("Y-m-d H:i:s");
$null_date = "0000-00-00 00:00:00";

// select all items that have a start_date of more than two weeks ago and who aren't already complete
$sql = "SELECT * FROM items I WHERE :current_date > DATE_ADD(start_date, INTERVAL 14 DAY)
							  AND I.item_id NOT IN (SELECT B.item_id FROM bids B, auctions_complete A 
							  WHERE A.bid_id = B.bid_id)";
$params = array(':current_date' => $current_date);
$items = query($sql,$params);

if ($items->rowCount() != 0)
{ 
	while ($item = fetch($items)) 
	{
		// select bid_id of winning bid
		$sql = "SELECT B.bid_id FROM bids B WHERE B.item_id = :item_id AND
				B.amount = (SELECT max(B2.amount) FROM bids B2 WHERE item_id = :item_id)";
		$params = array(':item_id' => $item['item_id']);
		$result = query($sql, $params);
		$bid_id = fetch($result);
		
		if($bid_id != NULL)
		{
			// insert into auctions_complete table
			$sql = "INSERT INTO auctions_complete(bid_id, date_item_received, date_money_received, date_item_sent, date_money_sent)
										   VALUES(:bid_id, :nullDate, :nullDate, :nullDate, :nullDate)";
			$params = array(':bid_id' => $bid_id['bid_id'], ':nullDate' => $null_date);
			$result = query($sql, $params);
		}
	}
}
 
?>    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
 <title>E-Auction</title>
 
 <link rel="stylesheet" href="<?php echo PATH?>template/themes/default/css/jquery-ui-1.9.0.custom.min.css" type="text/css" media="screen, projection" />
 <link rel="stylesheet" href="<?php echo PATH?>template/themes/default/css/chosen.css" type="text/css" media="screen, projection" />
 <link rel="stylesheet" href="<?php echo PATH?>template/themes/default/css/theme.css" type="text/css" media="screen, projection" />
 <link rel="shortcut icon" href="<?php echo PATH;?>favicon.ico" >
 
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery.fancyletter.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery.qtip-1.0.0-rc3.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery.validate.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery.tinyscrollbar.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/date.format.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery-ui-1.9.0.custom.min.js"></script>

 <script type="text/javascript">
 var global = {
			   path : '<?php echo PATH; ?>'
			}
	
$.ajaxSetup({
    // Disable caching of AJAX responses
    cache: false
});
	
	$(document).ready(function() {
			 
		   // Grey out text box description
		   $(".text").focus(function(srcc)
			{
				if ($(this).val() == $(this)[0].title)
				{
					$(this).removeClass("text_active");
					$(this).addClass("focus");
					$(this).val("");
				}
			});
				
			$(".text").blur(function()
			{
				if ($(this).val() == "")
				{
					$(this).addClass("text_active");
					$(this).removeClass("focus");
					$(this).val($(this)[0].title);
				}
			});
			
			$(".text").blur(); 
				
			// Small caps for titles
			$('.section_title').fancyletter({ltrClassPrefix: 'bg-', characters: '[A-S]'});
 			
			var delay = (function(){
				var timer = 0;
				return function(callback, ms){
					clearTimeout (timer);
					timer = setTimeout(callback, ms);
				};
			})();
				
			$('.tooltip').qtip({
				show: {
					effect: { length: 200 } 
				},
				position: {
				  corner: {
					 tooltip: 'topMiddle',
					 target: 'bottomMiddle' 
					}
				},
				content: {
				 text: false
				},
				style: ({
					tip: true,
					'font-size': 12,
					border: {
						color: '#333',
						radius: 2, 
						width: 0
					},
					background: '#333',
					color: '#EEEEEE',
					tip: { corner:'topMiddle', size: { x:10, y: 5}}
				})
			});
			$('.tooltip_right').qtip({
				show: {
					effect: { length:200 } 
				},
				position: {
				  corner: {
					 tooltip: 'leftMiddle',
					 target: 'rightMiddle' 
					}
				},
				content: {
				 text: false
				},
				style: ({
					tip: true,
					'font-size': 12,
					border: {
						color: '#333',
						radius: 2, 
						width: 0
					},
					background: '#333',
					color: '#EEEEEE'
					//tip: { corner:'topMiddle', size: { x:10, y: 5}}
				})
			});
			
			// Connect
			function repositionOptions(element) {
				var login = $('#'+element+'_options');
				var login_button = $('#'+element+'_show_options');
				var login_top = login_button.outerHeight()+login_button.position().top+5;
				var login_left = login_button.position().left -login.width()+login_button.outerWidth();
				login.css('left',login_left);
				login.css('top',login_top);
			}
			
			$('#login_show_options').click(function(){
				repositionOptions('login');
				$('#login_options').fadeToggle(150);
				if ($('#signup_options').is(':visible')) {
					$('#signup_options').fadeToggle(150);
				}
			});
			$('#signup_show_options').click(function(){
				repositionOptions('signup');
				$('#signup_options').fadeToggle(150);
				if ($('#login_options').is(':visible')) {
					$('#login_options').fadeToggle(150);
				}
			});
			
			$(window).resize(function() {
			
				if ($('#login_options').is(':visible')) {
					repositionOptions('login');
				}
				if ($('#signup_options').is(':visible')) {
					repositionOptions('signup');
				}
				
			});
			
			var size = '0';
			var updated = '0';
			setInterval(function() {
				if ($('#header .window_container').is(':visible')) {
					if (updated == '0') {
						$('#chat_log').tinyscrollbar_update('bottom');
						
						check.messages(function(data) {
							size = data;
							updated = data;
						});
					} else {
						check.messages(function(data) {
							updated = data;
							if (size != updated) {
								refreshChat();
								size = data;
							}
						});
					}
				}

			}, 2000);
			
			var sizeNum = '0';
			var updatedNum = '0';
			var newNum = '0';
			setInterval(function() {
				if ($('#header .window_container').is(':hidden')) {
					if (updatedNum == '0') {
						$('#chat_log').tinyscrollbar_update('bottom');
						
						check.numMessages(function(data) {
							sizeNum = data;
							updatedNum = data;
						});
					} else {
						check.numMessages(function(data) {
							updatedNum = data;
							if (sizeNum != updatedNum) {
								
								updateNotifications(updatedNum-sizeNum);
								sizeNum = data;
							}
						});
					}
				}
			}, 5000);
     });
 </script>
</head>
<body id="<?php echo str_replace(' ','_',strtolower($body_type))?>">
<div id="wrap">
	<div id="header">
		<div id="logo_background">
			<div id="logo" onclick="location.href='<?php echo PATH."/index..php"?>';"></div>
		</div>
		
		<div id="logout" class="tooltip" title="Logout from the E-Auction System" onclick="location.href='<?php echo PATH.'index.php?action=logout';?>';"></div>
		<div class="header_div"></div>


		

		<div id="logo_background">
			<div id="logo" onclick="location.href='<?php echo PATH.'index.php'?>';"></div>
			<form class="search_box" name="search_box" method="post" action="<?php echo PATH.'index.php?action=search'?>">
				<input id="search" name="search" type="search" class="text"  placeholder="Search"/>
			</form>
			<form class="login" name="login" method="post" action="<?php echo PATH.'index.php?action=login'?>">
				<?php if(!isset($_SESSION['user']['id'])) { ?>
					<input id="username" name="username" type="username" class="text" placeholder="Username"/>
					<input id="password" name="password" type="password" class="text"  placeholder="Password"/>
					<a onClick=" document.login.submit();" title="Login using an E-Auction account." class="button">Login</a>
					<a onClick="location.href='<?php echo PATH.'users/new_user.php';?>';" title="Register an E-Auction account." class="button">Sign Up</a>
				<?php } else { ?>
					<div id="user" onclick="location.href='<?php echo PATH.'users/view_user.php';?>';"><?php echo $_SESSION['user']['name']." - ".$_SESSION['user']['username'];?></div>
				<?php } ?>
			</form>
		</div>
	</div>

	<div id="dialog"></div>
	<div id="container">
		<div id="navigation">
			<?php if(isset($_SESSION['user']['id'])) { ?>
				<div class="navigation_header">Buying</div>
					<a href="<?php echo PATH?>items/search.php">Search</a>				
					<a href="<?php echo PATH?>items/bought_items.php">Purchased Items</a>
					<a href="<?php echo PATH?>users/search_history.php">Search History</a>
					<a href="<?php echo PATH?>users/browser_history.php">Browser History</a>
				<div class="navigation_header">Selling</div>
					<a href="<?php echo PATH?>items/add_item.php">Add Items</a>
					<a href="<?php echo PATH?>users/view_seller_item.php">Your Sales</a>					
				<div class="navigation_header">Profile</div>
					<a href="<?php echo PATH?>users/view_user.php">View Profile</a>
			<?php } ?>
			<?php if(isset($_SESSION['user']['id'])) { 
				if($_SESSION['user']['id'] == 7) { ?>
					<div class="navigation_header">TeleMarketing</div>
						<a href="<?php echo PATH?>telemarketers/telemarkets_report.php">Telemarketer Report</a>
				<?php } ?>										
			<?php } ?>					
		</div>
	<div id="page_title"><?php echo $page_title?></div>

	<div id="content">   

	     
