/* ************************************************************************************************
 * template/js/pass.transaction.js
 * 
 * @author: Matt Glick (matt.j.glick@gmail.com)
 * 
 * @description: Gets details for a transaction in a table
 * 
 * ************************************************************************************************/
$(document).ready(function() {
	$('.transaction').qtip({
		show: { effect: { length: 200 }, delay: 1000 },
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
			tip: { corner:'leftMiddle', size: { x:10, y: 10}}
		})
	});
	
	
	$('.ajax.transaction').live('click',function() {
		var row = $(this);
		var transactionID = $(this).attr('id');
		if (row.next().is('tr.details')) {
			row.next('tr.details').find('td')
			 .wrapInner('<div style="display: block;" />')
			 .parent()
			 .find('td > div')
			 .slideUp( function(){

				$(this).parent().parent().remove();

			 });
		} else {
			$.ajax({
				type: 'GET',
				url: '../ajax/api.php',
				data: 'action=transaction&q='+transactionID,
				success: function(data) {
					
					var json = JSON.parse(data);
					if (json.pass_type == 'bulk' || json.pass_type == 'family') {
					row.after('<tr class="details" ><td colspan="100%" style="padding:0px;"><table cellspacing="0" class="nested">'
						+'<tr><td><b>Group/Organization:</b></td><td><a href="'+global.path+'tools/profile.php?person_id='+json.person_id+'">'+json.person_name+'</a></td></tr>'
						+'<tr><td><b>PASS List:</b></td><td><a href="'+global.path+'tools/pass_list.php?pass_list_id='+json.pass_list_id+'">'+json.pass_list_name+'</a></td></tr>'
						+'<tr><td><b>PASS Count:</b></td><td>'+json.pass_count+'</a></td></tr>'
						+'<tr><td><b>Floor Access Time:</b></td><td>'+json.time_added+'</a></td></tr>'
						+'</table></td></tr>');
					} else if (json.pass_type == 'press') {
					row.after('<tr class="details" ><td colspan="100%" style="padding:0px;"><table cellspacing="0" class="nested">'
						+'<tr><td><b>Name:</b></td><td><a href="'+global.path+'tools/profile.php?person_id='+json.person_id+'">'+json.person_name+'</a></td></tr>'
						+'<tr><td><b>PASS List:</b></td><td><a href="'+global.path+'tools/pass_list.php?pass_list_id='+json.pass_list_id+'">'+json.pass_list_name+'</a></td></tr>'
						+'<tr><td><b>PASS Type:</b></td><td>'+json.pass_type_name+'</td></tr>'
						+'<tr><td><b>Floor Access Time:</b></td><td>'+json.time_added+'</a></td></tr>'
						+'<tr><td><b>Barcode:</b></td><td>'+json.barcode+'</td></tr>'
						+'</table></td></tr>');
					} else {
					row.after('<tr class="details" ><td colspan="100%" style="padding:0px;"><table cellspacing="0" class="nested">'
						+'<tr><td><b>Name:</b></td><td><a href="'+global.path+'tools/profile.php?person_id='+json.person_id+'">'+json.person_name+'</a></td></tr>'
						+'<tr><td><b>PASS List:</b></td><td><a href="'+global.path+'tools/pass_list.php?pass_list_id='+json.pass_list_id+'">'+json.pass_list_name+'</a></td></tr>'
						+'<tr><td><b>PASS Type:</b></td><td>'+json.pass_type_name+'</td></tr>'
						+'<tr><td><b>Floor Access Time:</b></td><td>'+json.time_added+'</a></td></tr>'
						+'<tr><td><b>Barcode:</b></td><td>'+json.barcode+'</td></tr>'
                        +'<tr><td><b>Binder:</b></td><td>'+json.binder_name+'</td></tr>'
                        +'<tr><td><b>ID Page:</b></td><td>'+json.binder_page+'</td></tr>'
                        +'<tr><td><b>ID Slot:</b></td><td>'+json.binder_slot+'</td></tr>'
						+'</table></td></tr>');
					}
					row.next('.details').find('td')
					 .wrapInner('<div style="display: none;" />')
					 .parent()
					 .find('td > div')
					 .slideDown( function(){

					  var $set = $(this);
					  $set.replaceWith($set.contents());

					 });
				}
			});
		}
	});
});