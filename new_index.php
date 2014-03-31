<?php
/* ************************************************************************************************
 * index.php
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Login prompt and public landing page
 * 
 * ************************************************************************************************/

require 'includes/db.inc.php';
require 'includes/functions.inc.php';
require 'includes/config.inc.php';

@session_start(); 

$action = (isset($_GET['action'])) ? sanitize($_GET['action']) : false;
$auth = (isset($_GET['r'])) ? sanitize($_GET['r']) : false;

// Define mobile
if (!isset($_SESSION['user']['theme'])) 
{
	define('MOBILE', detectMobile());
	$_SESSION['user']['theme'] = ((!MOBILE) ? 'default' : 'mobile');
} 
else
{
	if ($action == 'theme') 
	{
		if ($_SESSION['user']['theme'] == 'default') 
		{
      $_SESSION['user']['theme'] = 'mobile';
		}
		  else
		{
			$_SESSION['user']['theme'] = 'default';
		}
	}

	define('MOBILE', (($_SESSION['user']['theme']=='default') ? false : true));
}

// Redirect user if they are already logged in
if ((isset($_SESSION['user']['home'])) && (!isset($_GET['action']))){
	header('Location: '.PATH.$_SESSION['user']['home']);
}

// TODO CHECK TO SEE IF THIS STILL APPLIES WITH WEBACCESS
// Generate auth seed
//$seed = encrypt($_SERVER['SERVER_NAME'].','.microtime(true).','.getmypid(),AUTH_KEY);

// Check if an auth response has been sent
if ($auth) 
{
		
	$auth = authenticate($auth);

	if (isset($auth['error'])) 
	{
		message('error',$auth['error']);
		$action = false;
		$auth = false;
	}
}

// login attempt
if ($action == 'login')
{

	if ((LOCAL_ACCESS) && (!LOCAL)) 
	{
		message('error login_message','External access to the PASS System has been disabled. Please contact pass@thon.org if you have any questions or concerns.');
	}
	else
	{
			// ------------------needs editing-------------------------------------
	  // here I will need to recieve the psu username from webaccess.
	  // this will then get checked against the think_users table to see if THINK knows who they are. 
	  // if(think no)
	  //    prompt them to go to THINK
	  // if(think yes)
	  //    check to see what their role is in THINK
	  //    update the class for the user in the session variable below
	  
	  
	  			// Set session variables
					$_SESSION['user'] = array(
												'name' => $row['userName'], 
												'timeout' => date('Y-m-d H:i:s',time() + USER_TIMEOUT*60), 
												'class' => $row['userClass'], 
												'type' => getUser(array('get' => 'name', 
												'class' => $row['userClass'])), 
												'home' => getUser(array('get' => 'home', 
												'class' => $row['userClass'])), 
												'id' => $row['id'], 
												'theme' => 'default', 
												'image' => $row['image'], 
												'status' => $row['status']
											);
					
					// Log user event
					userLog('Login');
					
					// Redirect to user home page
					header('Location: '.PATH.$_SESSION['user']['home']);
			// -------------------------end of changes------------------------------
	}

// logout the user					
}
elseif (($action == 'logout') && (isset($_SESSION['user']['name'])))
{

	if ($_SESSION['user']['id']<999) {
	
		// Mark user as offline
		$sql = "UPDATE users 
				SET online = '0' 
				WHERE id = :user_id";
			
		$params = array(':user_id' => $_SESSION['user']['id']);
			
		query($sql,$params);
		
	}
	
	userLog('Logout');
	
	// New Code block to destroy session and remove session cookie.
    // Unset session variables.
    $_SESSION = array();
    
    // Kill the session and delete the cookie.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session.
    session_destroy();
	
	$_SESSION['user']['theme'] = ((!MOBILE) ? 'default' : 'mobile');
	
	message('success','You have been logged out!');
}

// Format messages for display
$messages = formatMessages();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
 <title>THON PASS System</title>
 
 <link rel="stylesheet" href="<?php echo PATH?>template/themes/<?php echo $_SESSION['user']['theme']?>/css/theme.css" type="text/css" media="screen, projection" />
 <link rel="shortcut icon" href="<?php echo PATH;?>favicon.ico" >
 <meta id="viewport" name="viewport" content ="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
 <meta http-equiv="content-type" content="text/html; charset=utf-8" />
 
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery.fancyletter.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/coin-slider.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/supersized.3.1.3.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery.qtip-1.0.0-rc3.min.js"></script>
 <script type="text/javascript" src="<?php echo PATH?>template/js/jquery.tinyscrollbar.min.js"></script>
 
 <script type="text/javascript">
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
			
			<?php if (MOBILE) {?>
			jQuery(function($){
				$.supersized({
					//Functionality
					slideshow               :   1,
					autoplay				:	1,
					start_slide             :   0,
					random					: 	0,
					slide_interval          :   5000,
					transition              :   1,
					transition_speed		:	750,
					new_window				:	0,
					pause_hover             :   0,
					keyboard_nav            :   0,
					performance				:	1,
					image_protect			:	1,
					
					//Size & Position
					min_width		        :   0,
					min_height		        :   0,
					vertical_center         :   1,
					horizontal_center       :   1,
					fit_portrait         	:   1,
					fit_landscape			:   0,
					
					//Components
					thumbnail_navigation    :   0,
					slide_counter           :   0,
					slides					:   [
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_1.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_2.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_3.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_4.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_5.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_6.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_7.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_8.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_9.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_10.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_11.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_12.jpg' },
														{ image : '<?php echo PATH?>template/themes/mobile/images/backgrounds/loginbg_13.jpg' }
												]
				});
			});
		<?php } else {?>
		
		// coin-slider
		$('#coin-slider').coinslider({ width: 1060, height: 480, navigation: false, delay: 5000,links: false });
		
		$('#login_title').fancyletter({ltrClassPrefix: 'bg-', characters: '[A-S]'});
		
		
		
		// Connect options
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

		});

		$(window).resize(function() {
		
			if ($('#login_options').is(':visible')) {
				repositionOptions('login');
			}

		});

		function twitterLinkify(text) {
			text = text.replace(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig, '<a href="$1" target="_blank">$1</a>');
			text = text.replace(/\B@([\w-]+)/gm, '<a href="http://twitter.com/$1" target="_blank">@$1</a>');
			text = text.replace(/#([a-zA-Z0-9]+)/g, '<a href="http://twitter.com/search/?src=hash&q=%23$1" target="_blank">#$1</a>');
			return text;
			
		} 
		
		// Feed
		$.ajax({
			dataType: 'jsonp',
			url: 'http://search.twitter.com/search.json',
			data: 'q="%40thon" OR "%23thon14"-filter:retweets',
			success: function(json) {
				$.each(json.results, function(key, value) {
					
				  if (key==0) {
						$('#feed #scrollbar1 .viewport .overview').append('<div class="login_box_content">'+
							'<div class="tweet_image" style="background:url('+value.profile_image_url+') left no-repeat;background-size: 35px 35px;"></div>'+
							'<div class="tweet_content"><a href="http://twitter.com/'+value.from_user+'"><b>'+value.from_user_name+'</b><font color="grey">@'+value.from_user+'</font></a> </br>'+twitterLinkify(value.text)+'</div>'+
						'</div>')
					} else {
						$('#feed #scrollbar1 .viewport .overview').append('<div class="login_box_divider"></div>'+
						'<div class="login_box_content">'+
							'<div class="tweet_image" style="background:url('+value.profile_image_url+') left no-repeat;background-size: 35px 35px;"></div>'+
							'<div class="tweet_content"><a href="http://twitter.com/'+value.from_user+'"><b>'+value.from_user_name+'</b> <font color="grey">@'+value.from_user+'</font></a></br>'+twitterLinkify(value.text)+'</div>'+
						'</div>')
					}
				});
				
			// Feed Scroll
			$('#scrollbar1').tinyscrollbar();
			}
		});
		
		$('#feed').hover(function() {
			$('#scrollbar1 .track').fadeIn(150);
		},function() {
			$('#scrollbar1 .track').fadeOut(150);
		});
		
		
		
		// qTip
		$('#login_sidebar .login_link').qtip({
			show: { effect: { length: 200 } },
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
				color: '#EEEEEE',
				tip: { corner:'leftMiddle', size: { x:5, y: 10}}
			})
		});
		
		$('form.login input, form.login a').qtip({
			show: { 
				delay: 1500, 
				effect: { length: 200 } 
			},
			position: {
			  corner: {
				 tooltip: 'topRight',
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
				tip: { corner:'topRight', size: { x:10, y: 10}}
			})
			
		});
	<?php }?>
     });
 </script>
</head>
<body id="login">
<div id="wrap">
<?php if ((isset($_SESSION['user']['theme'])) && ($_SESSION['user']['theme']=='mobile')) 
{
?>
	<div id="wrap">
	
		<div id="mobile_container">
			<div id="mobile_banner"><h1></h1></div>
			<div id="mobile_home">
			
				<div id="mobile_links" class="button">
				
					<div class="mobile_left">
						<a href="<?php echo PATH.'ext/search.php'?>">Search</a>
					</div>
	
				</div>
				
			</div>
		</div>
		
	</div>
<?php 
}
else 
{
?>
	<div id="header" style="position:relative;">
		<form class="login" name="login" method="post" >

			<input id="password" name="password" type="password" class="text"/>
			<a onClick=" document.login.submit();" title="Login using a PASS System account." class="button">Login</a>
		</form>
	</div>
	
	<div id="container" style="height:675px;">
		<?php echo (isset($messages)) ? $messages : '';?>
		<div id="login_title"></div>
		
		<div id='login_center'>
			<div id='coin-slider'>
					<img src='<?php echo PATH?>template/themes/default/images/backgrounds/loginbg_1.jpg' >
					<?php for ($i = 2; $i <= 13; $i++) {
							echo '<img src="'.PATH.'template/themes/default/images/backgrounds/loginbg_'.$i.'.jpg" >';
						}?>
			</div>
		</div>
		
		<div id="login_sidebar">
			<div class="login_box" id="feed">
			<h3>Feed</h3>
			<div class="login_box_divider"></div>
			<div id="scrollbar1">
				<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
				<div class="viewport">
					<div class="overview">
					</div>
				</div>
			</div>
			</div>
			<div class="login_box">
				<h3>Tools</h3>
				<div class="login_box_divider"></div>
				<div class="login_box_content login_link" style="text-align:left;cursor:pointer;" onclick="location.href='<?php echo PATH.'ext/search.php'?>';"title="Check if your PASS List has any open passes before getting in line.">
					PASS List Search<div style="float:right;"><b>>></b></div>
				</div>
				<div class="login_box_divider"></div>
				<div class="login_box_content login_link" style="text-align:left;cursor:pointer;" title="Questions, Concerns, Ideas? We would love to hear your think!">
					Feedback<div style="float:right;"><b>>></b></div>
				</div>
				<div class="login_box_divider"></div>
				<div class="login_box_content login_link" style="text-align:left;cursor:pointer;" onclick="location.href='<?php echo PATH.'ext/about_system.php'?>';" title="More information and tools.">
					More<div style="float:right;"><b>>></b></div>
				</div>
			</div>
			
		</div>
		
		<div id="footer" class="login_footer">
		<div class="footer_column">
			<div class="login_theme_logo"></div>
			&#169; THON 2013-2014</br> PASS Developers
		</div>
		<div class="footer_div"></div>
		<div class="footer_column">
			<b>Contact</b></br>
			pass@thon.org</br>
			1-800-392-THON
		</div>
		<div class="footer_div"></div>
		<div class="footer_column">
			<b>Connect</b></br>
			Twitter</br>
			Facebook</br>
			Pintrest</br>
		</div>
		<div class="footer_div"></div>
		<div class="footer_column">
			<b>Web</b></br>
			<a href="http://thon.org/">THON.org</a></br>
			<a href="http://thonblog.tumblr.com/">THON Blog</a></br>
			<a href="https://store.thon.psu.edu/">THON Store</a></br>
			<a href="http://think.psu.edu/">THINK</a>
		</div>
		<div class="footer_div"></div>
		<div class="footer_column">
			<b>Contribute</b></br>
			<a href="https://secure.imodules.com/s/1218/thon/thon.aspx?sid=1218&gid=1&pgid=671&cid=2344">Donate to THON</a></br>
			<a href="http://www.thon.org/students/get_involved">Volunteer</a>
		</div>
		<div class="footer_div"></div>
		<div class="footer_column">
			<b>Theme</b></br>
			<a href="<?php echo PATH.'index.php?action=theme'?>">Mobile</a></br>
		</div>
			
		</div>
	</div>
</div>
 <script type="text/javascript">
function setMargins() {
    height = $(window).height();
    containeraHeight = $("#container").height();  
    topMargin = (height-containeraHeight)/2;
	if (topMargin<100) {
		topMargin = 50;
	}
    $("#container").css("marginTop", topMargin);    
}

$(document).ready(function() {
    setMargins();
    $(window).resize(function() {
        setMargins();    
    });
});
 </script>
 <?php }?>
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
</body>
</html>
<?php $db=null; ?>
