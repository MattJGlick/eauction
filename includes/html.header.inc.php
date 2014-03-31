<?php
/* ************************************************************************************************
 * includes/header.inc.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: Header for pages that require authenticated users. This only includes the HTML
 * portion of the original header page.
 * 
 * ************************************************************************************************/
 

$ajax = (isset($_GET['ajax'])) ? true : false;

    if (!$ajax) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
 <title>PASS System - <?php echo $page_title?></title>
 
 <link rel="stylesheet" href="<?php echo PATH?>template/themes/<?php echo $_SESSION['user']['theme']?>/css/jquery-ui-1.9.0.custom.min.css" type="text/css" media="screen, projection" />
 <link rel="stylesheet" href="<?php echo PATH?>template/themes/<?php echo $_SESSION['user']['theme']?>/css/chosen.css" type="text/css" media="screen, projection" />
 <link rel="stylesheet" href="<?php echo PATH?>template/themes/<?php echo $_SESSION['user']['theme']?>/css/theme.css" type="text/css" media="screen, projection" />
 <link rel="shortcut icon" href="<?php echo PATH;?>favicon.ico" >
<?php if ((DEBUG) && (!MOBILE)) {?> <link rel="stylesheet" href="<?php echo PATH?>template/themes/<?php echo $_SESSION['user']['theme']?>/css/sqlsyntax.css" type="text/css" media="screen, projection" /><?php }?>
<?php if (MOBILE) {?> <meta id="viewport" name="viewport" content ="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" /><?php }?>
 
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
			
			<?php if (MOBILE) {?>
			$('a', '#navigation').each(function() {
				if ($(this).next().attr('class') == "navigation_header") {
					$(this).css("border-bottom-right-radius","2px");
					$(this).css("border-bottom-left-radius","2px");
					$(this).css("padding-bottom","10px");
				}
				$(this).append("<div style='float:right;'>>></div>")
				
			
			});
			$('.menu').click(function() {
				//$('#navigation').css('white-space', 'nowrap');
				//$('#navigation').animate({width: 'toggle'});
				if ($('#navigation').css('left').replace(/[^-\d\.]/g, '') >= 0) {
					$('#navigation').animate({
						left: '-150%'
					}, 500);
					$("#page_title").css('left','100%');
					$("#content").css('left','100%');
					$('#page_title').animate({
						left: '0%'
					}, 500)
					$('#content').animate({
						left: '0%'
					}, 500)
					$(this).children('a').text('Menu');
				} else {
					$('#page_title').animate({
						left: '150%'
					}, 500);
					$('#content').animate({
						left: '150%'
					}, 500);
					$("#page_title").css('left','-150%');
					$("#content").css('left','-150%');
					$("#navigation").css('left','-100%');
					$('#navigation').animate({
						left: '0%'
					}, 500)
					$(this).children('a').text('Back');
				}
			
			});
			
			$("#navigation").css('width',$(window).width()-20);
			$("#page_title").css('width',$(window).width()-40);
			$("#content").css('width',$(window).width()-40);
			<?php } else {?>

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
			
			// Check global variable if user timeout is enabled or not.
			if ( <?php echo CM_LOGOUT ?> == 1 )
			{
				// Session timeout
				setTimeout(function(){ window.location = global.path+'index.php?action=logout'; }, 1000*60*(<?php echo USER_TIMEOUT; ?>+2)); // No this is not the only way users are timed out
			}
			
			// Feedback
			/*$( ".feedback" ).click(function() {
				$( "#dialog" ).attr('title','PASS System 4 Feedback').dialog({
					autoOpen: false,
					show: "drop",
					hide: "drop",
					resizable: false,
					height:500,
					width: 600,
					modal:true,
					buttons:false
				}).load('<?php echo PATH.'ext/feedback.php?ajax=true'; ?>').dialog('open');
				return false;
			});*/
			
			<?php  if (checkPermissions('feature','captain chat')) {?>
			// Chat
			refreshUsers();
			refreshChat();
			$('#chat').click(function() {
				$('#header div.window_container').fadeToggle(150);
				if ($('#header div.window_container').is(':visible')) {
					clearNotifications()
					refreshUsers()
				}
				
			});
			
			function linkify(text) {
				text = text.replace("\\", "");
				text = text.replace(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig, '<a href="$1" target="_blank">$1</a>');
				text = text.replace(/\B@([\w-]+)/gm, '<a href="http://twitter.com/$1" target="_blank">@$1</a>');
				return text;
				
			} 
			
			function refreshUsers() {
				$.ajax({
					type: 'GET',
					url: '../ajax/api.php',
					data: 'action=check_online_users',
					success: function(data) {
						
						var json = JSON.parse(data);
						
						$('#header div.users').html('');
						if (json.code == 200) {
						
							$.each(json.results, function(key,value) {
								var name = value['user_name'].split(' ');
								$('#header div.users').append('<div class="user"><div class="img" style="background:url('+value['user_image']+'); background-size:25px 25px;"></div><span>'+name[0]+'</span></div>');
								
							});
							
						}

					}
				});
			}
			
			function refreshChat() {
				$.ajax({
					type: 'GET',
					url: '../chat/captain_chat.json',
					dataType: 'json',
					success: function(data) {

						$('#header div.chat').html('');
						
						$.each(data, function(key,value) {

							appendMessage(value);
							
						});
						
						$('#chat_log').tinyscrollbar_update('bottom');
						
					}
				});
			}
			
			function appendMessage(json) {
				var nameCurrent = $('#user').text().split(' ');
				var t = json['time'].split(/[- :]/);
				var date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
				date = dateFormat(date, "ddd h:MM TT");
				if (json['text'] != null) {
					if (nameCurrent[0] == json['name']) {
						$('#header div.chat').append('<span><b><font color="blue">'+json['name']+'</font></b> <i>('+date+')</i>: '+linkify(json['text'])+'</span></br>');
					} else {
						$('#header div.chat').append('<span><b><font color="red">'+json['name']+'</font></b> <i>('+date+')</i>: '+linkify(json['text'])+'</span></br>');
					}
				}
			}
			
			function updateNotifications(number) {
				$('#notifications').html(parseInt($('#notifications').html())+number);
				$('#notifications').fadeIn();
			}
			
			function clearNotifications() {
				$('#notifications').fadeOut(function() {
					$('#notifications').html('0');
				});
			}
			
			check = {
				messages: function(callback) {
					$.ajax({
						type: 'GET',
						url: '../ajax/api.php',
						data: 'action=check_messages',
						success: function(data) {
							callback.call(this,data);
						}
					});
				},
				numMessages: function(callback) {
					$.ajax({
						type: 'GET',
						url: '../ajax/api.php',
						data: 'action=check_num_messages',
						success: function(data) {
							callback.call(this,data);
						}
					});
				}
			}
			
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
			
			setInterval(function() {
				if ($('#header .window_container').is(':visible')) {
					refreshUsers()
				}
			}, 15000);
			
			function sendMessage(text) {
				var name = $('#user').text().split(' ');
				$.ajax({
					type: 'GET',
					url: '../ajax/api.php',
					data: 'action=send_captain_message&q='+name[0]+'+text:+'+text,
					success: function(data) {

						refreshChat();
						
						check.numMessages(function(data) {
							sizeNum = data;
							updatedNum = data;
						});

					}
				});
			}
			
			$('.chat_area textarea').focus(function() {
				delay(function(){
					$(this).keypress(function(event){
						var keycode = (event.keyCode ? event.keyCode : event.which);
						if(keycode == '13'){
							event.preventDefault();
							if ($('.chat_area textarea').val() != '') {
								sendMessage($('.chat_area textarea').val())
								$('.chat_area textarea').val('');
								$('#chat_log').tinyscrollbar_update('bottom')
							}
						}
					})
					
				}, 10 );
			});

			// Chat Scrollbar
			$('#chat_log').tinyscrollbar();
			
			$('#chat_log').hover(function() {
				$('#chat_log .track').fadeIn(150);
			},function() {
				$('#chat_log .track').fadeOut(150);
			});
			
			<?php }?>
			<?php }?>
			
			<?php if ($validate) {?>
			get = {
				model: function(callback) {
					$.ajax({
						url: "<?php echo $validate?>",
						dataType: "json",
						success: function(data){
							callback.call(this,data);
						}
					});
					
				}
			}

			
			$.ajax({
				url: "<?php echo $validate?>",
				dataType: "json",
				success: function(data){
					$.each(data,function (form, data) {
						
						$(form).validate({
							errorClass: "errormessage",
							onkeyup: false,
							validClass: 'valid',
							rules: data.rules,
							messages: data.messages,
							errorPlacement: function(error, element){
								// Set positioning based on the elements position in the form
								var elem = $(element),
									corners = ['left center', 'right center'],
									flipIt = elem.parents('span.right').length > 0;
				 
								// Check we have a valid error message
								if(!error.is(':empty')) {
									// Apply the tooltip only if it isn't valid
									elem.filter(':not(.valid)').qtip({
										overwrite: false,
										how: { effect: { length: 200 } },
										position: {
										  corner: {
											 tooltip: 'topMiddle',
											 target: 'bottomMiddle' 
											}
										},
										content: error,
										style: ({
											tip: true,
											'font-size': 12,
											border: { radius: 3, width: 1},
											name: 'red' 
										}),
										show: {
											event: false,
											ready: true
										},
										hide: false
									})
				 
									// If we have a tooltip on this element already, just update its content
									.qtip('option', 'content.text', error);
								}
				 
								// If the error is empty, remove the qTip
								else { elem.qtip('destroy'); }
							},
							success: $.noop, // Odd workaround for errorPlacement not firing!
						});
						
					});
				}
			});
			
			<?php }?>
			
     });
 </script>
</head>
<body id="<?php echo str_replace(' ','_',strtolower($body_type))?>">
<div id="wrap">
	<div id="header">
		<?php if (MOBILE) {?>
			<div class="button menu"><a href="#">Menu</a></div>
		<?php } else {?>
			<?php if (checkPermissions('','',array())){?>
			<div id="logo_background">
				<div id="logo" onclick="location.href='<?php echo PATH.getUser(array('get' => 'home', 'class' => $_SESSION['user']['class']))?>';"></div>
			</div>
			
			<div class="message_of_day"><?php echo MOTD ?></div>

			<?php if(checkPermissions('feature', 'captain message')) { ?>
			<div class="captain_motd"><?php echo CAP_MOTD ?></div>
			<?php } ?>

			<div id="logout" class="tooltip" title="Logout from the PASS System" onclick="location.href='<?php echo PATH.'index.php?action=logout';?>';"></div>
			<div class="header_div"></div>
			<?php if (checkPermissions('feature', 'ping captain')) {?>
                <div id="ping_captain" class="tooltip" title="Ping a Captain!" onclick="location.href='<?php echo PATH.'/ajax/pyc.php';?>';"></div>
                <div id="notifications">0</div>
                <div class="header_div"></div>
            <?php }?>
			<?php  if (checkPermissions('feature','captain chat')) {?>
			<div id="chat" class="tooltip" title="Chat with other captains"></div>
			<div id="notifications">0</div>
			<div class="header_div"></div>
			<?php }?>
            <?php  if (checkPermissions('','',array(5,6))) {?>
			<div id="anonymous_user"><?php echo $_SESSION['user']['name'];?></div>
			<?php } else {?>
			<div id="user" onclick="location.href='<?php echo PATH.'tools/profile.php?user_id='.$_SESSION['user']['id']?>';" class="tooltip" title="View your profile"><?php echo $_SESSION['user']['name'];?><div id="user_type"><?php echo getUser(array('get' => 'name', 'class' => $_SESSION['user']['class']))?></div></div>
			<div id="user_img" style="background:url(<?php echo $_SESSION['user']['image']?>) left no-repeat;background-size: 100%;"></div>
			<?php } ?>
			<?php  if (checkPermissions('feature','captain chat')) {?>
			<div class="window_container">
			<div class="window">
				<div class="chat_area">
					<h3>Captain Chat</h3>
					<div id="chat_log">
						<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
						<div class="viewport">
							<div class="overview">
								<div class="chat">
									<span><b>PASS System</b>: Hey there! Type a message here to talk to other captains on the PASS System!</span>
								</div>
							</div>
						</div>
					</div>
					<textarea></textarea> 
				</div>
				<div class="users">
				</div>
			</div>
			</div>
			<?php } ?>
			<?php } else {?>
			<div id="logo_background">
				<div id="logo" onclick="location.href='<?php echo PATH.'index.php'?>';"></div>
				<form class="login" name="login" method="post" action="<?php echo PATH.'index.php?action=login&provider=pass'?>">
					<?php if (LOCAL) {?>
					<input id="password" name="password" type="password" class="text"/>
					<a onClick=" document.login.submit();" title="Login using a PASS System account." class="button">Login</a>
					<?php } ?>
					<?php if (USER_SIGNUP) {?>
					<a title="Login using an alternate method." class="button" id="signup_show_options">Sign Up &#x25BC;</a>
					<?php }?>
					<a title="Login using an alternate method." class="button" id="login_show_options">Connect &#x25BC;</a>
					
				</form>
				<div id="login_options" >
					<a class="btn-auth btn-google" href="<?php echo PATH.'index.php?action=login&provider=google';?>">
						Connect with <b>Google</b>
					</a>
					<a class="btn-auth btn-facebook" href="<?php echo PATH.'index.php?action=login&provider=facebook';?>">
						Connect with <b>Facebook</b>
					</a>
					<a class="btn-auth btn-twitter" href="<?php echo PATH.'index.php?action=login&provider=twitter';?>">
						Connect with <b>Twitter</b>
					</a>
				</div>
				<?php if (USER_SIGNUP) {?>
				<div id="signup_options" >
					<a class="btn-auth btn-google" href="<?php echo PATH.'index.php?action=signup&provider=google';?>">
						Sign up with <b>Google</b>
					</a>
					<a class="btn-auth btn-facebook" href="<?php echo PATH.'index.php?action=signup&provider=facebook';?>">
						Sign up with <b>Facebook</b>
					</a>
					<a class="btn-auth btn-twitter" href="<?php echo PATH.'index.php?action=login&provider=twitter';?>">
						Sign up with <b>Twitter</b>
					</a>
				</div>
				<?php }?>
			</div>
			<?php }?>
		<?php }?>
		<?php if (!strstr($_SERVER['PHP_SELF'],'feedback.php')) { ?>
		<?php } ?>
	</div>

	<div id="dialog"></div>
	<div id="container">
		<div id="navigation">
			<?php if (checkPermissions('','',array())) {?>
			<?php if ($_SESSION['user']['status']=='active') {?>
			<?php if (checkPermissions('menu','system links')){?>
			<div class="navigation_header">System</div>
				<a href="<?php echo PATH?>management/configure.php">Configure</a>
				<a href="<?php echo PATH?>management/permissions.php">Permissions</a>
				<a href="<?php echo PATH?>management/requests.php">Requests</a>
				<a href="<?php echo PATH?>management/requests_log.php">Requests Log</a>
				<a href="<?php echo PATH?>management/database.php">Database</a>			
			<div class="navigation_header">Logs</div>
				<a href="<?php echo PATH?>management/user_logs.php">User Logs</a>
				<a href="<?php echo PATH?>management/system_logs.php">System Logs</a><?php }?>
			<?php if (checkPermissions('menu','committee member links')){?>
            <div class="navigation_header">Statistics</div>
            <?php }?>
            <?php if (checkPermissions('menu','statistics links')){?>
				<a href="<?php echo PATH?>statistics/expired_passes.php">Expired Passes</a>
				<a href="<?php echo PATH?>statistics/system_status.php">System Status</a><?php }?>
                <?php if (checkPermissions('menu','committee member links')){?>
				<a href="<?php echo PATH?>statistics/transactions_history.php">Transactions History</a>
                <?php }?>
            <?php  if (checkPermissions('','',array(0,1,2,3,4,5,6))) {?>
			<div class="navigation_header">Floor</div>
				<a href="<?php echo PATH?>floor/check_on.php">Floor Access</a>
				<a href="<?php echo PATH?>floor/check_off.php">Floor Exit</a>
			<?php } if (checkPermissions('','',array(0,1,2,3,4,5,6,9))) { ?>
			<div class="navigation_header">Binder</div>
				<a href="<?php echo PATH?>binder/contents.php">Binder Contents</a>
				<a href="<?php echo PATH?>binder/info.php">Binder Info</a>
				<a href="<?php echo PATH?>binder/search.php">Binder Search</a>
			<?php } if (checkPermissions('link','binder scan')) { ?>
				<a href="<?php echo PATH?>binder/scan.php">Binder Scan</a>
			<?php } if (checkPermissions('','',array(0,1,2,3,4,5,6,8))) { ?>
			<div class="navigation_header">Tools</div>
				<a href="<?php echo PATH?>tools/profile.php">Profile Directory</a>
				<a href="<?php echo PATH?>tools/pass_list.php">PASS List Directory</a>
				<?php if (checkPermissions('menu','committee member links')){?>
				<a href="<?php echo PATH?>tools/pass_type.php">PASS Type Directory</a>
				<?php } ?>
			<?php } else { 
				if (isset($_SESSION['user']['organizations'])) {?>
			<div class="navigation_header">Tools</div>
				<?php foreach ($_SESSION['user']['organizations'] as $id => $name) { ?>
				<a href="<?php echo PATH?>tools/pass_list.php?pass_list_id=<?php echo $id;?>">View Passlist - <b><?php echo $name;?></b></a>
				<?php 			} 
							} 
						}
				 if (checkPermissions('menu','captain tool links')) {?>
				<a href="<?php echo PATH?>tools/suspend.php">Suspend PASS List</a>
				<a href="<?php echo PATH?>tools/suspension_logs.php">Suspension Logs</a>
				<?php }?>
				<?php } else { ?>
			<div class="navigation_header">Information</div>
				<a class="inactive">Activate account to enable navigation</a>
			<?php } ?>
			<?php } else {?>
			<div class="navigation_header">Tools</div>
				<!--<a href="<?php echo PATH?>ext/search.php">PASS List Search</a>-->
				<a href="<?php echo PATH?>ext/signup.php">THON Chair Signup</a>
				<!--<a href="<?php echo PATH?>ext/feedback.php">Submit Feedback</a>-->
				<?php if (MOBILE) { ?>
				<a href="<?php echo PATH.'index.php?action=theme'?>">Switch to Desktop Theme</a>
				<?php } else { ?>
				<a href="<?php echo PATH.'index.php?action=theme'?>">Switch to Mobile Theme</a>
				<?php } ?>
			<div class="navigation_header">Resources</div>
				<!--<a href="<?php echo PATH?>ext/guide.php">Quick Guide to THON 2013</a>-->
				<a href="<?php echo PATH?>ext/about_system.php">About the PASS System</a>
			<?php } ?>
			
		</div>
		
		
	<div id="page_title"><?php echo $page_title?></div>
		<div id="content">        
<?php } ?>