<?php
/* ************************************************************************************************
 * home.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: Shows a broad overview of the system
 * 
 * ************************************************************************************************/
 
$page_title = "Dashboard";

require_once 'includes/php.header.inc.php';
require_once 'includes/html.header.inc.php';
$_SESSION['user']['theme'] = 'default';

$user_id = $_SESSION['user']['id'];

$sql = "SELECT id AS user_id, userName AS user_name, email AS user_email, online AS user_status, userClass AS user_class, image AS user_image, modified AS user_modified, created AS user_created, psu AS user_psu
		FROM users
		WHERE id = :user_id";

$params = array(':user_id' 	=> $user_id);
		
$result = query($sql,$params);

if ($result->rowCount() == 0) {
	message('error','No results found for for the given user ID.');
} elseif ($result->rowCount() == 1) {
	$row = fetch($result);
	$display=1;
} else {
	message('error','Multiple results found for the given user ID.',1,3);
}

if (checkPermissions('feature','full dashboard')) {
$display = 1;
} else {
$display = 2;
}


//This section grabs the Floor Composition broken down by pass type for use in the pie chart.
$passcountTotal = getFloorCount();

$types = array('general','vip','bulk','press','family');

foreach ($types as $type) {
    $passcountByType[$type] = getFloorCount($type);
}


// This section handles how users that have signed up but not been activated are handled; primarily
// this covers THON Chairs. There are three displays used for this feature:
// Display 3: Allows users to search for a PSU ID that they would like to associate with their
// account.
// Display 4: Displays whether an ID was found with the user's parameters and allows them to confirm
// the association of that PSU ID with their PASS account.
// Display 5: If a user has already attached their PSU ID with their PASS Account, a message is
// displayed and they are asked to wait for activation. 
if ($_SESSION['user']['status']=='pending') {
    
    if (isset($_POST['psu_id'])) {        
        $display = 4;
        $sql = "SELECT COUNT(psu) AS count FROM organizations WHERE psu = :psu_id;";
        $params = array(':psu_id' 	=> $_POST['psu_id']);
        
        $idFound = fetch(query($sql, $params));
        
        if ($idFound['count'] == 0) {
            $idSearchMessage = '<div class = "error">The PSU ID '.$_POST['psu_id']. ' does not exist
                in the system. Please try again.</div><input type="submit" value = "Back">';
        } else {
            $idSearchMessage = '<div class = "success">The PSU ID <b>'.$_POST['psu_id']. '</b> was found and is associated with <b>'
                                .$idFound['count']. '</b> organizations. If this is correct, please confirm your selection.</div>
                                <input name="confirmedID" type="hidden" value="'.$_POST['psu_id'].'" />
                                <input type="submit" value = "Confirm">';
        }
    } else if ($row['user_psu'] != "xyz123" || isset($_POST['confirmedID'])) {
        $display = 5;
        if (isset($_POST['confirmedID'])) {
            $sql = "UPDATE users SET psu = :psu_id WHERE id = :user_id;";
            $params = array(':psu_id' => $_POST['confirmedID'],
                            ':user_id' => $user_id);
            
            query($sql, $params);
            
            echo '<meta http-equiv="refresh" content="0">';
        }
    } else if ($row['user_psu'] == "xyz123") {
        $display = 3;  
    }
} elseif ($_SESSION['user']['status']=='denied') {

	$display = 6;
	message('error','Your account has been denied access. Please contact the PASS Team at pass@thon.org if you believe this was an error.');
	
}

if ($display == 1) {
?>
			<div class="section_title">Overview</div>
			<div class="section_title_divider"></div>
			<div id="live_activity" class="section_description" style="height: 200px;"></div>
			
			<div class="half">
				<div class="section_title">System Status</div>
				<div class="section_title_divider"></div>
				<div class="section_description">A quick overview of who is on the floor by type.</div>
				<div class="section_content">
					<table cellspacing="0">
						<tr class="tooltip_right" title="The total number of people on the floor not including BJC Staff, Captains, and Committee Members.">
							<td colspan="2"><b>Floor Total</b></td>
							<td id="floor_total"><?php echo $passcountTotal?></td>
						</tr>
						<tr>
							<td class="table_indent"></td>
							<td><i>Organization</i></td>
							<td id="floor_general"><?php echo $passcountByType['general']?></td>
						</tr>
						<tr>
							<td class="table_indent"></td>
							<td><i>Family</i></td>
							<td id="floor_family"><?php echo $passcountByType['family']?></td>
						</tr>
						<tr>
							<td class="table_indent"></td>
							<td><i>Special Guest</i></td>
							<td id="floor_vip"><?php echo $passcountByType['vip']?></td>
						</tr>
						<tr>
							<td class="table_indent"></td>
							<td><i>Press</i></td>
							<td id="floor_press"><?php echo $passcountByType['press']?></td>
						</tr>
						<tr>
							<td class="table_indent"></td>
							<td><i>Bulk</i></td>
							<td id="floor_bulk"><?php echo $passcountByType['bulk']?></td>
						</tr>
						<tr class="tooltip_right" title="Indicates if the system is allowing access to the floor.">
							<td colspan="2"><b>Floor</b></td>
							<td><?php echo ((FLOOR_OPEN) ? '<font color="green">OPEN</font>' : '<font color="red">CLOSED</font>') ?></td>
						</tr>
						<tr class="tooltip_right" title="Indicates if the system is operating in finale mode. Only finale pass holders can access the floor if this is active.">
							<td colspan="2"><b>Finale</b></td>
							<td><?php echo ((FINALE) ? '<font color="yellow">ON</font>' : '<font color="green">OFF</font>') ?></td>
						</tr>
						<tr class="tooltip_right" title="Indicates if the external application access is active.">
							<td colspan="2"><b>API</b></td>
							<td><?php echo ((API_ACCESS) ? '<font color="green">ON</font>' : '<font color="red">OFF</font>') ?></td>
						</tr>
						<tr class="tooltip_right" title="Indicates if debug mode is active.">
							<td colspan="2"><b>Debug</b></td>
							<td><?php echo ((DEBUG) ? '<font color="yellow">ON</font>' : '<font color="green">OFF</font>') ?></td>
						</tr>
						<tr class="tooltip_right" title="Indicates new users are allowed to sign up.">
							<td colspan="2"><b>Signup</b></td>
							<td><?php echo ((USER_SIGNUP) ? '<font color="red">ON</font>' : '<font color="green">OFF</font>') ?></td>
						</tr>
					</table> 
				</div>
			</div>
			<div class="half">
				<div class="section_title">Floor Composition</div>
				<div class="section_title_divider"></div>
				<div class="section_description" id="floor_composition" style="height:300px;">
				</div>
			</div>
			
			<div class="section_title">Profile</div>
			<div class="section_title_divider"></div>
			<div class="section_description" style="height:150px;">
				<div class="profile_img" style="background:url(<?php echo $row['user_image']?>) left no-repeat;background-size: 150px 150px;"></div>
				<div class="section_content">
				<b>Name:</b> <?php echo $row['user_name']?></br>
				<b>Type:</b> <?php echo getUser(array('get' => 'name', 'class' => $row['user_class']))?></br>
				<b>Email:</b> <?php echo $row['user_email']?></br>
				<b>Joined:</b> <?php echo date("F j, Y, g:i a",strtotime($row['user_created']));?></br>
				</div>
			</div>	
			
			
<script type="text/javascript" src="<?php echo PATH?>template/js/jquery-ui-1.8.22.custom.min.js"></script>		
<script src="<?php echo PATH.'template/js/highcharts.js'?>"></script>
<script src="<?php echo PATH.'template/js/highcharts_exporting.js'?>"></script>
<script type="text/javascript">
$(function () {
    $(document).ready(function() {
		
		$.fn.animateHighlight = function(highlightColor, duration) {
			var highlightBg = highlightColor || "#FFFF9C";
			var animateMs = duration || 1500;
			var originalBg = this.parent().css("backgroundColor");
			this.stop().css("background-color", highlightBg).animate({backgroundColor: originalBg}, animateMs);
		};
		
		// Update floor total
		/*function getFloorData() {
			$.ajax({
				type: 'GET',
				url: '../ajax/api.php',
				data: 'action=floor_total',
				success: function(data) {
				
					var json = JSON.parse(data);
					$.each(json, function(key,value) {
						if ($('#floor_'+key).text() != value) {
							$('#floor_'+key).text(value).animateHighlight("#EEEEEE", 1500);
						}
					});
					
					var stats = {total:0,general:0,family:0,vip:0,press:0,bulk:0};
					$.each(stats, function(key,value) {
						if (!json[key]) {
							if ($('#floor_'+key).text() != value) {
								$('#floor_'+key).text("0").animateHighlight("#EEEEEE", 1500);
							}
						}
					});
				}
			});
		}*/
		
		get = {
			floorComposition: function(callback) {
				$.ajax({
					type: 'GET',
					url: '../ajax/api.php',
					data: 'action=floor_total',
					success: function(data) {
					
						var json = JSON.parse(data);
						callback.call(this,json);
						
						$.each(json, function(key,value) {
							if ($('#floor_'+key).text() != value) {
								$('#floor_'+key).text(value).animateHighlight("#EEEEEE", 1500);
							}
						});
						
						var stats = {total:0,General:0,family:0,vip:0,press:0,bulk:0};
						$.each(stats, function(key,value) {
							if (!json[key]) {
								if ($('#floor_'+key).text() != value) {
									$('#floor_'+key).text("0").animateHighlight("#EEEEEE", 1500);
								}
							}
						});

					}
				});
			}
		}
		
		/*get.floorComposition(function(data) {
			console.log(data);
		});*/
		
		$.ajaxSetup({ cache: false });
		/*setInterval(function() {
			get.floorComposition(function(data) {
				console.log('test');
				console.log(data);
			});

		}, 5000);*/
		
		// Floor composition
		$(function () {
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'floor_composition',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						backgroundColor:'transparent'
					},
					exporting: {
						buttons: {

							printButton:{
								enabled:false
							},
							exportButton: {
								enabled:false
							}

						}
					},
					credits: {
						enabled: false
					},
					title: {
						text: ''
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false
							},
							showInLegend: true
						}
					},
					series: [{
						type: 'pie',
						name: 'Floor Composition',
						data: [
							['Organization',   <?php echo $passcountByType['general']?>],
							['Special Guest',       <?php echo $passcountByType['vip']?>],
							['Family', <?php echo $passcountByType['family']?>],
							['Bulk',    <?php echo $passcountByType['bulk']?>],
							['Press',     <?php echo $passcountByType['press']?>],
							
						]
					}]
				});
			});
			
		});
	
        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });
    
        var chart;
        chart = new Highcharts.Chart({
            chart: {
				backgroundColor: 'transparent',
                renderTo: 'live_activity',
                type: 'area',
                marginRight: 10,
				borderWidth: 1,
				borderColor: "#CECECE",
				borderRadius: 2,
                events: {
                    load: function() {
    
                        // set up the updating of the chart each second
                        var floorGeneral = this.series[0];
                        var floorPress = this.series[1];
                        var floorBulk = this.series[2];
                        var floorVIP = this.series[3];
                        var floorFamily = this.series[4];
                        setInterval(function() {
							get.floorComposition(function(data){
								var x = (new Date()).getTime()
								floorGeneral.addPoint([x, parseInt(data['general'])], true, true);
								floorPress.addPoint([x, parseInt(data['press'])], true, true);
								floorBulk.addPoint([x, parseInt(data['bulk'])], true, true);
								floorVIP.addPoint([x, parseInt(data['vip'])], true, true);
								floorFamily.addPoint([x, parseInt(data['family'])], true, true);
							})
                            
                        }, 5000);
                    }
                }
            },
			credits: {
				enabled: false
			},
            title: {
                text: ''
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 100,
				gridLineColor: "#333333",
				lineColor: "#333333",
				tickColor : "#333333"
				
            },
            yAxis: {
                title: {
                    text: 'Value'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#333333'
                }],
				gridLineColor: "#333333"
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) +'<br/>'+
                        Highcharts.numberFormat(this.y, 2);
                }
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
			plotOptions: {
                area: {
                    stacking: 'normal'
                }
            },
            series: [{
                name: 'General',
				color: '#6495ED',
                data: (function() {
					var data = [], time = (new Date()).getTime();
					for (i = -59; i <= 0; i++) {
					data.push([time, null]);     
					}		
					return data;
				})()
            },{
                name: 'Press',
				color: '#DDA0DD',
                data: (function() {
					var data = [], time = (new Date()).getTime();
					for (i = -59; i <= 0; i++) {
					data.push([time, null]);     
					}		
					return data;
				})()
            },{
                name: 'Bulk',
				color: '#FFD700',
                data: (function() {
					var data = [], time = (new Date()).getTime();
					for (i = -59; i <= 0; i++) {
					data.push([time, null]);     
					}		
					return data;
				})()
            },{
                name: 'VIP',
				color: '#FFB6C1',
                data: (function() {
					var data = [], time = (new Date()).getTime();
					for (i = -59; i <= 0; i++) {
					data.push([time, null]);     
					}		
					return data;
				})()
            },{
                name: 'Family',
				color: '#FFFF00',
                data: (function() {
					var data = [], time = (new Date()).getTime();
					for (i = -59; i <= 0; i++) {
					data.push([time, null]);     
					}		
					return data;
				})()
            }
			
			]
        });
		
		
    });
    
});
</script>
<?php } elseif ($display == 2) { ?>
			<div class="section_title">Overview</div>
			<div class="section_title_divider"></div>
			<div class="section_description">Welcome to PASS System 4, here is a quick overview of the systems current status.</div>
			
			<div class="section_content">
				<table cellspacing="0">
					<tr>
						<th colspan="100%">System Status</th>
					</tr>
					<tr class="tooltip_right" title="Indicates if the system is allowing access to the floor">
						<td colspan="2"><b>Floor</b></td>
						<td><?php echo ((FLOOR_OPEN) ? '<font color="green">OPEN</font>' : '<font color="red">CLOSED</font>') ?></td>
					</tr>
					<tr class="tooltip_right" title="Indicates if the system is operating in finale mode. Only finale pass holders can access the floor if this is active.">
						<td colspan="2"><b>Finale</b></td>
						<td><?php echo ((FINALE) ? '<font color="yellow">ON</font>' : '<font color="green">OFF</font>') ?></td>
					</tr>
					<tr class="tooltip_right" title="Indicates if the external application access is active">
						<td colspan="2"><b>API</b></td>
						<td><?php echo ((API_ACCESS) ? '<font color="green">ON</font>' : '<font color="red">OFF</font>') ?></td>
					</tr>
					<tr class="tooltip_right" title="Indicates if debug mode is active">
						<td colspan="2"><b>Debug</b></td>
						<td><?php echo ((DEBUG) ? '<font color="yellow">ON</font>' : '<font color="green">OFF</font>') ?></td>
					</tr>
					<tr class="tooltip_right" title="Indicates new users are allowed to sign up">
						<td colspan="2"><b>Signup</b></td>
						<td><?php echo ((USER_SIGNUP) ? '<font color="red">ON</font>' : '<font color="green">OFF</font>') ?></td>
					</tr>
				</table> 
			</div>
<?php } elseif ($display == 3
                || $display == 4
                || $display == 5) { ?>
			<?php echo (isset($messages)) ? $messages : '';?>
			<div class="message notice">This account has not been activated yet. If you believe this is an error please contact the PASS Team at pass@thon.org.</div>
			<div class="section_title">Profile</div>
			<div class="section_title_divider"></div>
			<div class="section_description" style="height:150px;">
				<div class="profile_img" style="background:url(<?php echo $row['user_image']?>) left no-repeat;background-size: 150px 150px;"></div>
				<div class="section_content">
				<b>Name:</b> <?php echo $row['user_name']?></br>
				<b>Type:</b> <?php echo getUser(array('get' => 'name', 'class' => $row['user_class']))?></br>
				<b>Email:</b> <?php echo $row['user_email']?></br>
				<b>PSU ID:</b> <?php echo $row['user_psu']?></br>
				<b>Joined:</b> <?php echo date("F j, Y, g:i a",strtotime($row['user_created']));?></br>
				</div>
			</div>   
			<div class="section_title">PSU Access Account</div>
			<div class="section_title_divider"></div>
			<div class="section_description">
				Please enter your PSU Access Account (ex. xyz123) and click save.
			</div>
			<div class="section_content">
				<?php if ($display == 3) { ?>
                                <form id="chair_psu_id" class="signup" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
					<input type="text" class="text" name = "psu_id" title="PSU ID (ex. xyz123)"/>
                                        <input type="submit" value = "Save" >
				</form>
                                <?php } elseif ($display == 4) { ?>
                                    <form id="chair_psu_id" class="signup" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
                                    <?php echo $idSearchMessage;?>
                                    </form>
                                <?php } elseif ($display == 5) { ?>
                            <div class ="notice">You have successfully associated the PSU ID <b><?php echo $row['user_psu'];?></b> with your
                                    account. Please wait for the administrator to activate your PASS account to gain
                                    full access to the system.
                            </div>
                                <?php } ?>
			</div>
			<div class="section_description">
				This will be used to link your PASS System account with information on THINK to verify your THON Chair status in the system. 
				Your PASS System account will allow you to view and manage your PASS List during THON weekend. 
				More information and a tutorial about how to use this feature will be sent out in a future THON Chair Update
			</div>
<?php } elseif ($display == 6) { ?>
<?php echo (isset($messages)) ? $messages : '';?>
<?php
}
require 'includes/footer.inc.php';

?>
</html>