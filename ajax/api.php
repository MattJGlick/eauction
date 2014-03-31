<?php
/* ************************************************************************************************
 * ajax/api.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: API for internal AJAX queries
 * 					
 * 
 * ************************************************************************************************/
 
 //Start session
@session_start();
 
require_once '../includes/db.inc.php';
require_once '../includes/functions.inc.php';
require_once '../includes/config.inc.php';

checkPermissions('','',array());
 
$action = (isset($_GET['action'])) ? $_GET['action'] : false;
$q = (isset($_GET['q'])) ? sanitize($_GET['q']) : false;
 
 if ($action == 'transaction') {
	
	$sql = "SELECT 	plp.id 			AS person_id, 
					plp.name 		AS person_name, 
					pl.name 		AS pass_list_name, 
					pl.id 			AS pass_list_id, 
					pt.id 			AS pass_type_id, 
					pt.color 		AS pass_color, 
					pt.hex 			AS pass_hex, 
					pt.abbrev 		AS pass_abbrev,
					pt.name 		AS pass_type_name, 
					LOWER(pt.checkonType)	AS pass_type,
					t.timeAdded 	AS time_added, 
					t.timeRemoved 	AS time_removed, 
					t.barcode 		AS barcode, 
					t.idSlot 		AS binder_slot, 
					t.binder 		AS binder_id,
					b.name			AS binder_name,
					t.id 			AS transaction_id, 
					t.user 			AS user,
					t.passCount		AS pass_count
			FROM transactions AS t,
				passListPeople AS plp,
				passLists AS pl,
				passTypes AS pt,
				binders AS b
			WHERE t.spectator = plp.id
				AND t.passList = pl.id
				AND t.passType = pt.id
				AND t.binder = b.id
				AND t.id = :q
			ORDER BY t.timeAdded DESC
			LIMIT 1";
			
	$params = array(':q' => $q);
				
	$result = query($sql,$params);
	
	$row = fetch($result);
	
	if ($row['pass_type'] == 'general') {
		
		$row['binder_page'] = substr($row['binder_slot'], 0, 2);
		$row['binder_slot'] = substr($row['binder_slot'], 2, 1);
		
	} else {
	
		$row['binder_page'] = substr($row['binder_slot'],1,2);
		$row['binder_slot'] = substr($row['binder_slot'],-1,1);
		
	}

	
	// Set pass colors for general org passes
	if ($row['pass_abbrev'] == 'ORG') {
		$sql = "SELECT pc.color AS pass_color, pc.hex AS pass_hex
				FROM passColors AS pc
				WHERE pc.barcodeStart <= :barcode
				AND pc.barcodeEnd >= :barcode";
				
		$params = array(':barcode' => $row['barcode']);
				
		$result = query($sql,$params);;
		
		$pass_colors = fetch($result);
		$row['pass_color'] = $pass_colors['pass_color'];
		$row['pass_hex'] = $pass_colors['pass_hex'];
		
		
	}
	
	$json = (isset($row)) ? json_encode($row) : 'No Results';
	
	print_r($json);
 
 }
 
if ($action == 'transaction_list') {

	if ($q) {
	
		$q = stripslashes($q);
		$q = json_decode($q,true);
		
		$filters = '';
		$results_per_page = 15;
		
		$q['page'] = (isset($q['page'])) ? $q['page'] : 1;
		
		if (isset($q['filters']) && !empty($q['filters'])) {
		
			$pass_list['string'] 	= ' AND (';
			$pass_list['count'] 	= 0;
			
			foreach($q['filters'] as $filter) {
			
				if ($filter['type'] == 'name') {
					
					$filters .= ' AND plp.name LIKE :person_name';
					$params[':person_name'] = '%'.$filter['string'].'%';
					
				} elseif ($filter['type'] == 'check_off_time') {
					
					$filters .= ' AND t.timeRemoved BETWEEN :start_time AND :end_time';
					$params[':start_time'] = date("Y-m-d H:i:s",strtotime($filter['start_time']));
					$params[':end_time'] = date("Y-m-d H:i:s",strtotime($filter['end_time']));
					
				} elseif ($filter['type'] == 'check_on_time') {
					
					$filters .= ' AND t.timeAdded BETWEEN :start_time AND :end_time';
					$params[':start_time'] = date("Y-m-d H:i:s",strtotime($filter['start_time']));
					$params[':end_time'] = date("Y-m-d H:i:s",strtotime($filter['end_time']));
					
				} elseif ($filter['type'] == 'pass_list') {
					
					$pass_list['count']++;
					$pass_list['string'] .= ($pass_list['count'] == 1) ? 'pl.id = :pass_list_id_'.$pass_list['count'] : ' OR pl.id = :pass_list_id_'.$pass_list['count'];
					$params[':pass_list_id_'.$pass_list['count']] = $filter['string'];
					
				} elseif ($filter['type'] == 'tag') {
					
					if ($filter['string'] == 'general' || $filter['string'] == 'bulk' || $filter['string'] == 'family' || $filter['string'] == 'vip' || $filter['string'] == 'press') {
					
						$filters .= " AND pt.checkonType = '".$filter['string']."'";
					
					} elseif ($filter['string'] == 'expired') {
					
						$filters .= " AND NOW() >= t.passExpiration
									AND t.passExpired = 0
									AND t.spectator !=0
									AND pt.checkonType != 'bulk'";
					
					} elseif ($filter['string'] == 'on_floor') {
					
						$filters .= " AND t.passExpired = 0";
					
					} elseif ($filter['string'] == 'off_floor') {
					
						$filters .= " AND t.passExpired = 1";
					
					} elseif ($filter['string'] == 'access') {
					
						$filters .= " AND t.user = ".$_SESSION['user']['id'];
					
					} elseif ($filter['string'] == 'exit') {
					
						$filters .= " AND t.userExit = ".$_SESSION['user']['id'];
					
					} elseif ($filter['string'] == 'access_or_exit') {
					
						$filters .= " AND (t.userExit = ".$_SESSION['user']['id']." OR t.user = ".$_SESSION['user']['id'].")";
					
					}
					
					
					
				}
			
			}
			
			$filters .= ($pass_list['count'] != 0) ? $pass_list['string'].')': '';
			
		}
		
		// Determine what page to get
		$offset = ($q['page']) ? ' OFFSET '.intval($results_per_page*($q['page']-1)) : '';
		
		// Determine how many pages of results are in the DB for the given filters
		$sql = "SELECT 	COUNT(*) AS number
				FROM transactions 	AS t,
					passListPeople 	AS plp,
					passLists 		AS pl,
					passTypes 		AS pt
				WHERE  t.spectator = plp.id
					AND t.passList = pl.id
					AND pt.id = pl.type
					".$filters;
		
		$result = (isset($params) && !empty($q['filters'])) ? query($sql,$params) : query($sql);
		
		$count = fetch($result);
		$count = $count['number'];
		$max_page = ceil($count/$results_per_page);
		$max_page = ($max_page == 0) ? 1 : $max_page;
		$max_page = (15<=$max_page) ? 15 : $max_page;
		
		if ($max_page >= $q['page']) {
		
			$sql = "SELECT 	plp.name 			AS person_name, 
							t.timeAdded 		AS time_added, 
							t.timeRemoved 		AS time_removed, 
							t.id 				AS transaction_id, 
							t.user 				AS user_access, 
							t.userExit 			AS user_exit, 
							t.userID 			AS user_id, 
							t.userExitID 		AS user_exitID, 
							t.passExpiration 	AS transaction_expiration_time, 
							pt.checkonType 		AS checkon_type
						FROM transactions 		AS t,
							passListPeople 		AS plp,
							passLists 			AS pl,
							passTypes 			AS pt
						WHERE  t.spectator = plp.id
							AND t.passList = pl.id
							AND pt.id = pl.type
							".$filters."
						ORDER BY t.id DESC
						LIMIT ".$results_per_page.$offset;
			
			$result = (isset($params) && !empty($q['filters'])) ? query($sql,$params) : query($sql);

			if (fetch($result,'count') == 0) {
				
					$data['message'] 	= 'Request completed successfully but no transactions were found.';
					$data['code'] 		= 201;
					$data['status'] 	= 'OK';

			} else {

				while ($transaction = fetch($result)) {
					$transaction['tags'] = tags($transaction);
					$transactions[] = $transaction;
				}
				
				
				
				$data['message'] 	= 'Successfully queried list of transactions';
				$data['code'] 		= 200;
				$data['status'] 	= 'OK';
				$data['max_page'] 	= $max_page;
				$data['results']	= (isset($transactions)) ? $transactions : array();
				
				
			}
			
		} else {
		
			$data['message'] 	= 'Attempted to load a page outside the range of results.';
			$data['code'] 		= 400;
			$data['status'] 	= 'Error';
		
		}
		
	} else {
	
		$data['message'] 	= 'No parameters send to API.';
		$data['code'] 		= 400;
		$data['status'] 	= 'Error';
	
	}
	
	print_r(json_encode($data));
}
 
 if ($action == 'user') {
	
	$sql = "SELECT u.userName AS user_name, u.image AS user_image, u.phone AS user_phone, u.email AS user_email, u.id AS user_id
			FROM users AS u
			WHERE u.id = :q";
			
	$params = array(':q' => $q);
				
	$result = query($sql,$params);
	
	$row = fetch($result);
	
	$json = (isset($row)) ? json_encode($row) : 'No Results';
	
	print_r($json);
 
}

 if ($action == 'user_list') {
	
	$sql = "SELECT u.userName AS user_name, u.image AS user_image, u.id AS user_id, u.userClass AS user_class
			FROM users AS u
			WHERE password IS null
			ORDER BY u.userClass
			LIMIT 10
			OFFSET ".$q;
				
	$result = query($sql);
	
	while($row = fetch($result)) {
		$row['user_type'] = getUser(array('get' => 'name', 'class' => $row['user_class']));
		$rows[] = $row;
	}
	
	$json = (isset($rows)) ? json_encode($rows) : 'No Results';
	
	print_r($json);
 
}

 if ($action == 'user_number') {
	
	$sql = "SELECT COUNT(*) AS number
 			FROM users AS u
			WHERE password IS null";
				
	$result = query($sql);
	
	$row = fetch($result);
	
	$json = (isset($row)) ? json_encode($row) : 'No Results';
	
	print_r($json);
 
}
 
if ($action == 'pass_list') {

	$q = urldecode($q);
	 
	$sql = "SELECT pl.id AS pass_list_id, pl.name AS pass_list_name, pt.accessLoc AS pass_location, pt.abbrev AS pass_list_abbreviation, pt.color AS pass_color, pt.validStart AS pass_start, pt.validEnd AS pass_end, pt.name AS pass_type, pl.total AS passes_total, pl.avail AS passes_available, pl.totalFinale AS finale_passes_total, pl.availFinale AS finale_passes_available
			FROM passLists AS pl,
				passTypes AS pt
			WHERE pl.id = :q
			AND pl.type = pt.id";
	
	$params = array(':q' => $q);
	
	$result = query($sql,$params);
	
	$row = fetch($result);
	
	$json = (isset($row)) ? json_encode($row) : 'No Results';
	
	print_r($json);

}
 
if ($action == 'floor_total') {
	
	if ($q == 'total') {
	
		$row = getFloorCount();
	
	} else {	
		
		$types = array('general','vip','press','bulk','family');
		
		foreach($types as $type) {
			$rows[$type] = getFloorCount($type);		
		}
		
		$rows['total'] = getFloorCount();
	
	}
	
	$json = (isset($rows)) ? json_encode($rows) : 'No Results';
	
	print_r($json);

}

if ($action == 'binder_contents') {

	if ($q) {
		
		if (strlen($q)==4) {
			$binder = substr($q,0, 2);
			$page = substr($q, -2);
		} else {
			$binder = $q[0];
			$page = substr($q, -2);
		}
		
		
		// Get list of activity from this user
		$sql = "SELECT plp.name AS person_name, t.timeAdded AS time_added, t.timeRemoved AS time_removed, t.id AS transaction_id, t.idSlot AS slot,t.barcode AS barcode
				FROM transactions AS t,
					passListPeople AS plp,
					passLists AS pl
				WHERE t.passExpired = '0'
					AND t.binder = :binder
					AND t.idSlot LIKE :page
					AND t.spectator = plp.id
					AND t.passList = pl.id
				LIMIT 10";
		
		$params = array(':page' 		=> $page.'%',
						':binder' 		=> $binder);
		
		$result = query($sql,$params);;

		while ($row = fetch($result)) {	
			$rows[] = $row;
		}
	
	}
	
	$json = (isset($rows)) ? json_encode($rows) : 'No Results';
	
	print_r($json);

}

if ($action == 'binder_pages') {

	if ($q) {
		
		// Get list of activity from this user
		$sql = "SELECT minSlot, maxSlot
				FROM binders
				WHERE id = :q";
		
		$params = array(':q' => $q);
		
		$result = query($sql,$params);;

		$row = fetch($result);
		
		$data['first'] = ltrim(substr($row['minSlot'],0,2),'0');
		$data['last'] = ltrim(substr($row['maxSlot'],0,2),'0');
	
	}
	
	$json = (isset($data)) ? json_encode($data) : 'No Results';
	
	print_r($json);

}

if ($action == 'person_search') {
	
	if ($q) {
		
		$q = urldecode($q);
		
		$name = explode(" ", $q);
	
		if (1<count($name)) {
		
			foreach ($name as $key => $value) {
				$name_sql[] = "plp.name LIKE :name".$key;
			}
			$name_sql = implode(" AND ", $name_sql);
			$name_sql = "OR ( ".$name_sql.")";
			
		}
		
		$sql = "SELECT plp.id AS person_id, plp.name AS person_name, plp.email AS person_email, plp.onFloor AS on_floor, pl.name AS pass_list_name, pt.checkonType AS pass_type
			FROM passListPeople AS plp,
				passLists AS pl,
				passTypes AS pt
			WHERE (plp.name LIKE :search OR plp.email LIKE :search  ".((1<count($name)) ? $name_sql : '').")
				AND plp.passList = pl.id
				AND pl.type = pt.id
				AND (pt.checkonType = 'general'".((checkPermissions('feature','bulk check on')) ? " OR pt.checkonType = 'bulk'" : "").((checkPermissions('feature','family check on')) ? " OR pt.checkonType = 'family'" : "").((checkPermissions('feature','vip check on')) ? " OR pt.checkonType = 'vip'" : "").")";
				
		$params = array( ':search' => "%" . $q . "%");
		
		if (1<count($name)) {
		
			foreach ($name as $key => $value) {
				$params[':name'.$key] = "%" . $name[$key] . "%";
			}
			
		}
				
		$result = query($sql,$params);
		
		if ($result->rowCount() != 0) {
			
			while ($row = fetch($result)) {
				
				$data[] = $row;
				
			}
		
		}
	
	}
	
	$json = (isset($data)) ? json_encode($data) : 'No Results';
	
	print_r($json);

}

if ($action == 'list_search') {

	if ($q) {

		$q = urldecode($q);
		
		$name = explode(" ", $q);
		
		if (1<count($name)) {
		
			foreach ($name as $key => $value) {
				$name_sql[] = "pl.name LIKE :name".$key;
			}
			$name_sql = implode(" AND ", $name_sql);
			$name_sql = "OR ( ".$name_sql.")";
			
		}
		
		$sql = "SELECT pl.name AS pass_list_name, pl.id AS pass_list_id
				FROM passLists AS pl,
					passTypes AS pt
				WHERE (pl.name LIKE :q  ".((1<count($name)) ? $name_sql : '').")
					AND pl.type = pt.id
				LIMIT 30";
				
		$params = array( ':q' => "%" . $q . "%");
		
		if (1<count($name)) {
		
			foreach ($name as $key => $value) {
				$params[':name'.$key] = "%" . $name[$key] . "%";
			}
			
		}
				
		$result = query($sql,$params);
		
		if ($result->rowCount() != 0) {
			
			while ($row = fetch($result)) {
				
				$data[] = $row;
				
			}
		
		}
	
	}
	
	$json = (isset($data)) ? json_encode($data) : 'No Results';
	
	print_r($json);

}

if ($action == 'check_online_users') {
	
	// Check if user has been inactive for too long but dont log this as new activiy
	checkTimeout(false);
	
	// See who is online
	$sql = "SELECT u.userName AS user_name, u.image AS user_image, u.activity AS last_activity, u.id AS user_id
			FROM users AS u
			WHERE online = '1'
			AND u.userClass != '7'";
					
	$result = query($sql);
	
	while ($row = fetch($result)) {
		
		$data['results'][] = $row;
		
		if (USER_TIMEOUT*60 < (time()-strtotime($row['last_activity']))) {
		
			$sql = "UPDATE users 
					SET online = 0
					WHERE id = :user_id";
			
			$params = array(':user_id' => $row['user_id']);
			
			query($sql,$params);
			
		}
		
	}
	
	$data['message'] 	= (isset($data['results'])) ? 'Successfully retrieved list of online users.' : 'No online users found in the system.';
	$data['code'] 		= (isset($data['code'])) ? 200 : 201;
	$data['status'] 	= 'OK';
	
	print_r(json_encode($data));
	

}

if ($action == 'check_new_messages') {

	$file = "../chat/captain_chat.json";
	
	$q = stripslashes($q);
	
	if (file_exists($file) && $q) {
		
		$data = filesize($file);
		
		if ($q == $data) {
			$data = true;
		} else {
			
		}
		
		$data = ($q == $data) ? 'false' : 'true';
		
	}
	
	$json = (isset($data)) ? $data : 'No Results';
	
	print_r($json);
	

}

if ($action == 'check_messages') {

	$file = "../chat/captain_chat.json";
	
	if (file_exists($file)) {
		
		$data = filesize($file);
	}
	
	$json = (isset($data)) ? $data : 'No Results';
	
	print_r($json);
	

}

if ($action == 'check_num_messages') {

	$file = "../chat/captain_chat.json";
	
	if (file_exists($file)) {
		$text = file_get_contents($file);
		$array = json_decode($text,true);
		$messages = count($array);
	}
	
	$json = (isset($messages)) ? $messages : 'No Results';
	
	print_r($json);
	

}

if ($action == 'send_captain_message') {
	// Need to add something to archive old messages or the file will become too large
	if ($q) {
		
		$file = "../chat/captain_chat.json";
		
		$data = explode(' text: ',$q);
		
		if (file_exists($file)) {
			$text = file_get_contents($file);
			$array = json_decode($text,true);
			$array[] = array('id' => count($array), 'time' => date('Y-m-d H:i:s',time()), 'name' => $data[0], 'text' => $data[1]);
			$data = json_encode($array);
			file_put_contents($file, $data);
		}
		
	}
	
	$json = (isset($data)) ? $data : 'Fail';
	
	print_r($json);
	

}

if ($action == 'organization_link_action') {
	if (checkPermissions('feature','organization linking')) {
		if ($q) {
			
			$q = stripslashes($q);
			$q = json_decode($q,true);
			
			$sql = "UPDATE users
					SET status = :action
					WHERE id = :entry_id";
			
			$params = array(':entry_id' => $q['entry_id'],
							':action' => $q['action']);

			$result = query($sql, $params);
            
            // Check if existing entry exists for linking that user to an organization.
            $sql = "SELECT COUNT(*) AS rowCount
                    FROM orgChairs
                    WHERE userID = :user_id
                        AND organizationID = :org_id;";
                        
            $params = array(':user_id'  => $q['entry_id'],
                            ':org_id'   => $q['organization']);

            $result = query($sql, $params);
            $entryExists = fetch($result);
            
            
            // If entry already exists, then update it. Otherwise, create a new entry.
			if($entryExists['rowCount']) {
                $sql = "UPDATE orgChairs 
                        SET status = :action, 
                            adminID = :admin_id
                        WHERE userID = :user_id
                        AND organizationID = :org_id;";
                        
                $params = array(':action'   => $q['action'],
                                ':admin_id' => $_SESSION['user']['id'],
                                ':user_id'  => $q['entry_id'],
                                ':org_id'   => $q['organization']);
                                
                query($sql, $params);
            } else {
                $sql = "INSERT INTO orgChairs(userID, organizationID, status, adminID)
                        VALUES (:user_id, :org_id, :action, :admin_id);";
                        
                $params = array(':user_id'  => $q['entry_id'],
                                ':org_id'   => $q['organization'],
                                ':action'   => $q['action'],
                                ':admin_id' => $_SESSION['user']['id']);

                query($sql, $params);                        
            }
			// $sql = "SELECT 	*
				// FROM 	requestsLog AS r
				// WHERE r.psu = :psu AND r.organization = :organization";

			// $params = array( ':psu' => $q['user_psu'], ':organization' => $q['organization']);
				
			// $result = query($sql, $params);

			// if(fetch($result, 'count') > 0) {
				// $sql = "UPDATE requestsLog
						// SET user = :user, organization = :organization, name = :name, action = :action
						// WHERE psu = :psu";
				
				// $params = array( ':user' => $_SESSION['user']['name'],
								 // ':organization' => $q['organization'],
								 // ':name' => $q['user_name'],
								 // ':psu' => $q['user_psu'],
								 // ':action' => $q['action']
							   // );

				// $result = query($sql, $params);
			// }
			// else {
				// $sql = "INSERT INTO requestsLog
						// (user, organization, name, psu, action) 
						// VALUES (:user, :organization, :name, :psu, :action)";
				
				// $params = array( ':user' => $_SESSION['user']['name'],
								 // ':organization' => $q['organization'],
								 // ':name' => $q['user_name'],
								 // ':psu' => $q['user_psu'],
								 // ':action' => $q['action']
							   // );

				// $result = query($sql, $params);
			// }
		}
	}
}

if ($action == 'organization_link_bulk_action') {

	$json = array	(	
						'status' 	=> 'OK',
						'code' 		=> 200,
						'message' 	=> 'Successfully linked users to organizations.'
					);

	if (checkPermissions('feature','organization linking')) {
		if ($q) {
			
			$q = stripslashes($q);
			$q = json_decode($q,true);
			
			foreach ($q['data'] as $link) {
				
				$sql = "UPDATE users
						SET status = :action
						WHERE id = :entry_id";
				
				$params = array(':entry_id' => $link['entry_id'],
								':action' => $q['action']);

				$result = query($sql, $params);
				
				// Check if existing entry exists for linking that user to an organization.
				$sql = "SELECT COUNT(*) AS rowCount
						FROM orgChairs
						WHERE userID = :user_id
							AND organizationID = :org_id;";
							
				$params = array(':user_id'  => $link['entry_id'],
								':org_id'   => $link['organization']);

				$result = query($sql, $params);
				$entryExists = fetch($result);
				
				// If entry already exists, then update it. Otherwise, create a new entry.
				if($entryExists['rowCount']) {
					$sql = "UPDATE orgChairs 
							SET status = :action, 
								adminID = :admin_id
							WHERE userID = :user_id
							AND organizationID = :org_id;";
							
					$params = array(':action'   => $q['action'],
									':admin_id' => $_SESSION['user']['id'],
									':user_id'  => $link['entry_id'],
									':org_id'   => $link['organization']);
									
					query($sql, $params);
					
				} else {
				
					$sql = "INSERT INTO orgChairs(userID, organizationID, status, adminID)
							VALUES (:user_id, :org_id, :action, :admin_id);";
							
					$params = array(':user_id'  => $link['entry_id'],
									':org_id'   => $link['organization'],
									':action'   => $q['action'],
									':admin_id' => $_SESSION['user']['id']);

					query($sql, $params);  
					
				}
				
			}
			
		} else {
		
			$json = array	(	
								'status' 	=> 'Error',
								'code' 		=> 400,
								'message' 	=> 'No parameters set.'
							);
		
		}
	} else {
	
		$json = array	(	
							'status' 	=> 'Forbidden',
							'code' 		=> 403,
							'message' 	=> 'You not have permission to access this feature.'
						);
	
	}
	
	$json = json_encode($json);
	
	print_r($json);
}

if ($action == 'update_psu_id') {
		if ($q) {
			
			$q = stripslashes($q);
			$q = json_decode($q,true);
			
			$sql = "UPDATE users
					SET psu = :psu
					WHERE id = :user_id";
			
			$params = array(':psu' => $q['psu'],
							':user_id' => $_SESSION['user']['id']);

			$result = query($sql, $params);
			
		}
	
		print_r('True');
}

if ($action == 'check_org_psu') {
		if ($q) {
			
			$q = stripslashes($q);
			$q = json_decode($q,true);
			
			$sql = "SELECT COUNT(*) AS number
					FROM organizations
					WHERE psu = :psu";
			
			$params = array(':psu' => $q['psu']);

			$result = query($sql, $params);
			
			$data = fetch($result);
			
		}
		
	$json = ($data['number']>0 ? 'True' : 'False');
	
	print_r($json);
			
}

if ($action == 'feedback') {
		if ($q) {
			
			$q = stripslashes($q);
			$q = json_decode($q,true);
			
			
			$sql = "INSERT INTO feedback
					(type, data, email, ip) 
					VALUES (:type, :data, :email, :ip)";
			
			$params = array( ':type' => $q['type'],
							 ':data' => json_encode($q['data']),
							 ':email' => $q['email'],
							 ':ip' => $_SERVER['REMOTE_ADDR']                       
						   );

			
			query($sql, $params);
			
		}
		
	$json = 'Success';
	
	print_r($json);
			
}

if ($action == 'add_to_pass_list') {
	
	if (!checkPermissions('feature','edit pass lists')) {
		
		$q = false;
		$json = array	(	
						'status' 	=> 'Error',
						'code' 		=> 0,
						'message' 	=> 'You do not have sufficient privlidges to access this feature.'
						);
		
	}
		
	if ($q) {
		
		$q = stripslashes($q);
		$q = json_decode($q,true);
		
		$sql = "SELECT MAX(plp.id) AS max_id
				FROM passListPeople AS plp
				LIMIT 1";
		
		$result = query($sql);
		
		$row = fetch($result);
		$row['max_id'] = $row['max_id'] + 1;
		
		$sql = "INSERT INTO `passListPeople` 
						(`id`, `name`, `email`, `passList`, `finale`, `onFloor`) 
				VALUES 	(:id, :name, :email, :pass_list_id, :finale, :on_floor);";
		
		foreach($q as $entry) {
		
			foreach($entry as $key => $value) {
			
				$row[$key] = $value;
				
			}
			
		}
		
		$params = array( ':id' 				=> $row['max_id'],
						 ':name' 			=> $row['name'],
						 ':email' 			=> $row['email'],
						 ':pass_list_id' 	=> $row['pass_list_id'],                     
						 ':finale' 			=> $row['finale'],
						 ':on_floor' 		=> 0,
					   );

		
		query($sql, $params);
		
		$sql = "SELECT pl.name AS pass_list_name
				FROM passLists AS pl
				WHERE pl.id = :id
				LIMIT 1";
		
		$params = array( ':id' => $row['pass_list_id']);
		
		$result = query($sql,$params);
		
		$pass_list = fetch($result);
		
		systemLog(2,$_SESSION['user']['type'].' '.$_SESSION['user']['name'].' added '.$row['name'].' (ID: '.$row['max_id'].') to PASS List '.(isset($pass_list['pass_list_name']) ? $pass_list['pass_list_name'] : '').' (ID: '.$row['pass_list_id'].').');
		
	}
		
	$json = array	(	
						'status' 	=> 'OK',
						'code' 		=> 200,
						'message' 	=> 'Successfully added person to pass list.',
						'result' 	=> array(
												'person_id' => $row['max_id']
											)
						);
	
	$json = json_encode($json);
	
	print_r($json);
			
}

if ($action == 'remove_from_pass_list') {
	
	if (!checkPermissions('feature','edit pass lists')) {
		
		$q = false;
		$json = array	(	
						'status' 	=> 'Error',
						'code' 		=> 0,
						'message' 	=> 'You do not have sufficient privlidges to access this feature.'
						);
		
	}
		
	if ($q) {
		
		$q = stripslashes($q);
		$q = json_decode($q,true);
		
		$sql = "DELETE FROM `passListPeople` 
				WHERE `passListPeople`.`id` = :id";
		
		$params = array( ':id' => $q[0]['id'] );

		query($sql, $params);
		
		systemLog(2,$_SESSION['user']['type'].' '.$_SESSION['user']['name'].' deleted person (ID: '.$q[0]['id'].') from a PASS List');
		
	}
		
	$json = array	(	
						'status' 	=> 'OK',
						'code' 		=> 200,
						'message' 	=> 'Successfully deleted person from pass list.'
					);
	
	$json = json_encode($json);
	
	print_r($json);
			
}

$db=null;
?>