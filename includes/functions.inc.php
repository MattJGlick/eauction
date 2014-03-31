<?php
/* ************************************************************************************************
 * includes/functions.inc.php
 * 
 * @author: Ani Channarasappa (ani@channarasappa.com)
 * 
 * @description: Global system functions
 * 
 * ************************************************************************************************/
 
function getTasks() {
	
	$data = array();
	
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	
		$oSchedule = new COM("Schedule.Service");
		$oSchedule->Connect();
		$oFolder = $oSchedule->GetFolder("\\");
		$oCollection = $oFolder->GetTasks(0);
		
		for ($i=1;$i<= $oCollection->Count ;$i++){
		
			if (strstr($oCollection->Item($i)->Name,"THON PASS System")) {
				
				$data[str_replace(' ','_',$oCollection->Item($i)->Name)] = array	(
									'name' 			=> $oCollection->Item($i)->Name,
									'path' 			=> $oCollection->Item($i)->Path,
									'next_run' 		=> $oCollection->Item($i)->NextRunTime,
									'last_run' 		=> $oCollection->Item($i)->LastRunTime,
									'last_result' 	=> $oCollection->Item($i)->LastTaskResult,
									'enabled' 		=> $oCollection->Item($i)->Enabled
								);
				
			}
			
		}
		
	}
	
	return $data;
	
}
 
function tags ($data, $exclude=array() ,$type='transaction') {
	
		if ($type = 'transaction') {
		
			$tags = array();
			if (!in_array('checkon_type',$exclude)) {
				if (array_key_exists('checkon_type',$data) && $data['checkon_type'] != 'general') {
				
					$tags[] = $data['checkon_type'];
					
				}
			}
			
			if (!in_array('expired',$exclude)) {
				if (array_key_exists('transaction_expiration_time',$data)) {
					if (array_key_exists('checkon_type',$data) && ($data['checkon_type'] != 'bulk')) {
						if ((time()>strtotime($data['transaction_expiration_time'])) && ($data['time_removed'] == '0000-00-00 00:00:00')) {
							$tags[] = 'expired';
						}
					}
				}
			}
			
			if (!in_array('user_exit',$exclude)) {
				if (array_key_exists('user_exit',$data) && ($data['user_exit'] == $_SESSION['user']['id'])) {
				
					if($data['user_exit'] == 3) {
				        if($user_id = $_REQUEST['PHPSESSID'] == $data['user_exitID'])
					       $tags[] = 'R&Rexit';
					}
					else
					    array_unshift($tags, '&#9733;', 'exit');
					
				}
			}
			
			if (!in_array('user_access',$exclude)) {
				if (array_key_exists('user_access',$data) && ($data['user_access'] == $_SESSION['user']['id'])) {
				
					if($data['user_access'] == 3) {
					   if($user_id = $_REQUEST['PHPSESSID'] == $data['user_id'])
					       $tags[] = 'R&Raccess';
					}
					else
					   array_unshift($tags, '&#9733;', 'access');
					
				}
			}
			
			
			$html = '';
			if (!empty($tags)) {
			
				$self = true;
				
				foreach ($tags as $tag) {
					if (($tag == '&#9733;')) {
						if ($self) {
							$html = $html.'<div class="tag star">'.$tag.'</div>';
							$self = false;
						}
					} else {
						$html = $html.'<div class="tag '.$tag.'">'.ucfirst($tag).'</div>';
					}
				}
				
			}

		}
		
		return $html;
	
}
 
function logAccess ($request,$key='',$user_agent='default') {
	
	if ($user_agent=='default') {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
	}
	
	$page = explode('?',$_SERVER['REQUEST_URI']);
	$page = $page[0];
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$sql = "INSERT INTO accessLog
			(`key`, `client`, `request`,`page`,`ip`) 
			VALUES (:key, :user_agent, :request,:page,:ip)";
	
	$params = array(':key' 			=> $key,
					':user_agent' 	=> $user_agent,
					':request' 		=> $request,
					':page' 		=> $page,
					':ip' 			=> $ip);
					
	query($sql,$params,false);
}
 
function model($location = false) {

		$file_uri = explode('/',$_SERVER['REQUEST_URI']);
		$file_name = $file_uri[count($file_uri)-1];
		$file_name = explode('?',$file_name);
		$file_name = $file_name[0];
		$file_name = str_replace('.php','.json',$file_name);
		
		if (file_exists('../models/'.$file_name)) {
			if ($location) {
				$model = '../models/'.$file_name;
			} else {
				$model = file_get_contents('../models/'.$file_name);
				
				$model = json_decode($model,true);
				
				foreach ($model as $rule) {
					if (isset($rules)) {
					
						$rules['rules'] = array_merge($rules['rules'], $rule['rules']);
						$rules['messages'] = array_merge($rules['messages'], $rule['messages']);
						
					} else {
					
						$rules['rules'] = $rule['rules'];
						$rules['messages'] = $rule['messages'];
						
					}

				}
				$model = $rules;
				$model = json_encode($model);

			}
			
		} else {
		
			$model = false;
			
		}
		
		return $model;

}
 
function scrub($dirt) {
	
	// Remove white space
	//$dirt = trim($dirt);
	
	// Strip html tags
	//$dirt = strip_tags($dirt);
	
	// Remove wildcard operators
	$dirt = addcslashes($dirt, "%_");
	
	return $dirt;
	
}
 
function sanitize($dirty) {
 
	if (is_array($dirty)) {
	
		foreach ($dirty as $key => $value) {
		
			$dirty[$key] = scrub($value);
		
		}
	
	} else {
	
		$dirty = scrub($dirty);
	
	}

	$clean = $dirty;
	
	return $clean;
}

function validate($method='jquery',$request=false) {
	if ($method == 'jquery') {
	
		$model = model(true);
		if ($model) {
			//$model = substr(substr($model, 0, -1), 1);
			//$model = $model.',';
			
			return $model;
		} else {
			return false;
		}
		
	} elseif ($method == 'php') {
		function checkRule ($rule_name,$rule_value,$value) {
			$result = false;
			if (is_numeric($value)) {
				$value = $value+0;
				$numeric = true;
			} else {
				$numeric = false;
			}
			
			switch ($rule_name) {
				case 'minlength':
					if (strlen($value) < $rule_value) {
						$result = 'minlength';
						
					}
					break;
				case 'maxlength':
					if (strlen($value) > $rule_value) {
						$result = 'maxlength';
						
					}
					break;
				case 'digits':
					if ((!$numeric) || (!is_int($value))) {
						$result = 'digits';
						
					}
					break;
				case 'max':
					if (($value >= $rule_value) && ($numeric)) {
						$result = 'max';
						
					}
					break;
				case 'min':
					if (($value <= $rule_value) && ($numeric)) {
						$result = 'min';
						
					}
					break;
				case 'range':
					if ($numeric) {
						$rule_value = substr(substr($rule_value, 0, -1), 1);
						$rules = explode(',',$rule_value);
						$min = trim(min($rules));
						$max = trim(max($rules));
						
						if (!(($min <= $value) && ($max >= $value))) {
							$result = 'range';
						}
					}
					break;
			}
			
			return $result;
		
		}
		
		$model = model();
		if ($model) {
			$model = json_decode($model,true);
			$check = true;
			
			if ($request) {
				
				$request = sanitize($request);
				
				if (!is_array($request)) {
					systemLog(3,'Non-array value passed where $_REQUEST array was expected.');
					$check = false;
				} else {
					foreach($request as $key => $value) {
						if (array_key_exists($key,$model['rules'])) {
							foreach ($model['rules'][$key] as $rule_name => $rule_value) {
								$failed = checkRule($rule_name,$rule_value,$value);
								if ($failed) {
									message('error',$model['messages'][$key][$rule_name]);
									$check = false;
								}
							}
						}
					}
				
				}
				
			} else {
				message('error','$_REQUEST not sent to validation().',1,3);
				$check = false;
			}
			
			foreach ($model['rules'] as $rule_name => $rule_value) {
				
				if (!array_key_exists($rule_name,$request)) {
					$request[$rule_name] = false;
				} else {
					if (!$check) {
						$request[$rule_name] = false;
					}
				}
			
			}
			
			$check = $request;
			
		} else {
			message('error','Validation model does not exist for this page.',1,3);
			$check = false;
		}
		
		return $check;
		
	} else {
		systemLog(3,'Invalid method passed to function validate().');
	}

}
 
function checkTimeout($activity = true) {
	
	if (!strstr($_SERVER['PHP_SELF'],'ext')) {
	
		if (strtotime($_SESSION['user']['timeout']) >= time()) {
			
			if (($_SESSION['user']['id']<999) && ($activity)) {
				$sql = "UPDATE users 
						SET activity = NOW() 
						WHERE id = :user_id";
						
				$params = array(':user_id' => $_SESSION['user']['id']);
				
				query($sql,$params,false);
				
				$_SESSION['user']['timeout'] = date('Y-m-d H:i:s',time() + USER_TIMEOUT*60);
			}
		} else {
		
			// If a captain is logged in, revert it to a committee member level account
			// instead of logging it out. If a CM level account is inactive, log it out.
			if ( (CM_LOGOUT == 1 && $_SESSION['user']['class'] > 5) || (strtotime($_SESSION['user']['timeout']+USER_TIMEOUT*60) < time() )) 
			{	
				userLog('Session Timeout');
				$_SESSION['messages'][] = '<div class="error login_message">Your session has timed out. Please login again.</div>';
				header('Location: '.PATH.'index.php?action=logout');
				exit();
				
			} else if ($_SESSION['user']['class'] <= 5) {
			
				if ($_SESSION['user']['id']<999) {
					// Mark user as offline
					$sql = "UPDATE users 
							SET online = '0' 
							WHERE id = :user_id";
			
					$params = array(':user_id' => $_SESSION['user']['id']);
			
					query($sql,$params);
				}
	
				userLog('Expired to CM Account');

				session_unset(); 
				session_destroy();
				@session_start(); 
				//$_SESSION['user']['theme'] = ((!MOBILE) ? 'default' : 'mobile');
				
				// Prepare to be logged in as Rules & Regs CM
				$sql = "SELECT *
						FROM users
						WHERE userClass = 6";
					
				$params = NULL;
					
				$result = query($sql,$params);
		
				$row = fetch($result);
		
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
		
				// Change this to broadcast message when implemented. 
				// message('success','Your captain level authentication has expired.');
		
				exit();
			}

		}
		
	}
	
}

function debug($value,$name='variable') {

	$_SESSION['debug']['var'][] = array('name' => $name, 'value' => $value);	

}

function fetch($result, $type = 'assoc') {

	switch ($type) {
		case 'assoc':
			$out = $result->fetch(PDO::FETCH_ASSOC);
			break;
		case 'both':
			$out = $result->fetch(PDO::FETCH_BOTH);
			break;
		case 'obj':
			$out = $result->fetch(PDO::FETCH_OBJ);
			break;
		case 'bound':
			$out = $result->fetch(PDO::FETCH_BOUND);
			break;
		case 'class':
			$out = $result->fetch(PDO::FETCH_CLASS);
			break;
		case 'into':
			$out = $result->fetch(PDO::FETCH_INTO);
			break;
		case 'num':
			$out = $result->fetch(PDO::FETCH_NUM);
			break;
		case 'count':
			$out = $result->rowCount();
			break;
	}
	
	return $out;
	
}

function query($sql, $parameters=false, $logging=true) {

	global $db;
	if ($logging) {
		$time = microtime(true);
	}
    
    
    
	$query = $db->prepare($sql);
	if ($parameters) {
	
		foreach ($parameters as $name => $value) {
		
			$query->bindValue($name, $value);
			
		}
		$sql_debug = str_replace(array_keys($parameters),array_values($parameters),$sql);
	} else {
		$sql_debug = $sql;
	}
	
	
	
	if ($logging) {
	
		$time = microtime(true)-$time;
		$time = $time*100000;
		$rows = 'N/A';
		
		$_SESSION['debug']['sql'][] = array('query' => $sql_debug, 'time' => $time, 'rows' => $rows);
		
		// Insert query text of INSERT or UPDATE queries into database
		if ((strstr($sql_debug,'UPDATE') || strstr($sql_debug,'INSERT')) && (!strstr($sql_debug,'UPDATE users'))) {

			
			$log_sql = "INSERT INTO sqlLog
						(query, ip) 
						VALUES (:query, :ip)";
			$log_query = $db->prepare($log_sql);
			$log_query->bindValue(':query', $sql_debug);
			$log_query->bindValue(':ip', 	$_SERVER['REMOTE_ADDR']);
			$log_query->execute();
			
		}
		
	}
	$query->execute();
    return $query;

}

// Legacy functionality
function mysqlQuery($sql, $parameters=false, &$db, $logging=true) {
	return query($sql, $parameters=false, $logging=true);
}
 
function systemLog($type, $message) {
	if ($type >= SYSTEM_LOGGING) {
		
		
		$user = (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] :  0);
		$time = date('Y-m-d H:i:s');
		$page = $_SERVER['PHP_SELF'];
		
		$sql = "INSERT INTO systemLog
				(type, message, user, time, page) 
				VALUES (:type, :message, :user, :time, :page)";
	
		$params = array(':type' 		=> $type,
						':message' 		=> $message,
						':user' 		=> $user,
						':time' 		=> $time,
						':page' 		=> $page);
						
		query($sql,$params,false);
		
		Return True;
		
	} else {
	
		Return False;
		
	}

} 

function userLog($action, $location = 'N/A') {

		if (isset($_SESSION['user']['name'])) {
			$user_name = $_SESSION['user']['name'];
		} else {
			$user_name = 'N/A';
		}
		
		if (isset($_SESSION['user']['id'])) {
			$user_id = $_SESSION['user']['id'];
		} else {
			$user_id = 'N/A';
		}
		
		if (isset($_SESSION['user']['class'])) {
			$user_class = $_SESSION['user']['class'];
		} else {
			$user_class = 'N/A';
		}
		
		$time = date('Y-m-d H:i:s');
		$ip = $_SERVER["REMOTE_ADDR"];
		
		// Create entry in userLog table
		$sql = "INSERT INTO userLog
		(userID, userName, userClass, action, location, ip, timestamp) 
		VALUES (:user_id', :user_name, :user_class, :action, :location, :ip, :time)";
		
		$sql = "INSERT INTO userLog
		(userID, userName, userClass, action, location, ip, timestamp) 
		VALUES (:user_id, :user_name, :user_class, :action, :location, :ip, :time)";
	
		$params = array(':user_id' 		=> $user_id,
						':user_name' 	=> $user_name,
						':user_class' 	=> $user_class,
						':action' 		=> $action,
						':location' 	=> $location,
						':ip' 			=> $ip,
						':time' 		=> $time);
				
		
		query($sql,$params,false);

} 

function getLocation(){

	$ip = $_SERVER['REMOTE_ADDR'];
	
	$sql = "SELECT *
			FROM equipment
			WHERE ip = :ip";
	
	$params = array(':ip' => $ip);
					
	$result = query($sql,$params,false);
			
	if ($result->rowCount() == 0) {
	// User is in an unknown location
	
		return array('name' => 'Unknown', 'machine' => 'Unknown', 'location' => 'Unknown', 'ip' => $ip, 'subnet' => 'Unknown');
	
	} elseif ($result->rowCount() == 1) {
	// Location found
	
		$row = $result->fetch(PDO::FETCH_ASSOC);
		return array('name' => $row['name'], 'machine' => $row['machine'], 'location' => $row['location'], 'ip' => $row['ip'], 'subnet' => $row['subnet']);
		
	} else {
	// Multiple entires for the same IP found in the database
	
		systemLog(3,'Multiple entires found in the databse for the IP '.$ip);
		
	}
}

function message($type, $message, $log = 0, $priority = 1){

	$type = strtolower($type);
	
	$_SESSION['messages'][] = '<div class="'.$type.'">'.$message.'</div>';
	
	if ($log) {
	
		systemLog($priority, strip_tags($message));
	
	}

}

function formatMessages($message = 0){

	if (($message == 0) && (isset($_SESSION['messages']))) {		
		$message = $_SESSION['messages'];
		unset($_SESSION['messages']);
	}
	if (is_array($message)) {
	$message = array_unique($message);
	$html = '<div>';
	foreach ($message as $key => $value) {
		$html = $html.$value;
	}
	$html = $html.'</div>';
	if ($html != '<div></div>') {
		return $html;
	}
	}
}

function checkPermissions($type='page', $name='default', $valid_users=array()) {
	
	if (isset($_SESSION['user']['name'])) {
			
		if (empty($valid_users)) {
			if ($name == 'default') {
			
				$name = $_SERVER['PHP_SELF'];
				
			}
			
			// Check if permissions have already been queried
			if (!isset($_SESSION['permissions'])) {
			
				$sql = "SELECT *
						FROM permissions";
								
				$result = query($sql,NULL,false);
				
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				
					$_SESSION['permissions'][] = $row;
					
				}
			
			}
			
			// Find the element in the array that contains the permission being checked
			$i=0;
			foreach ($_SESSION['permissions'] as $permission) {
			
				if (($permission['name'] == $name) && ($permission['type'] == $type)) {
					$element = $permission;
					$i++;
				} 
				
			}
			
			// Check how many results were found
			if (!isset($element)) {
				// If no entires were found create a new permissions entry and allow all
				$users = json_encode(array());
				$ips = json_encode(array());
				
				$sql = "INSERT INTO permissions
						(name, type, users, ips)
						VALUES (:name, :type, :users, :ips)";
			
				$params = array(':name' 		=> $name,
								':type' 		=> $type,
								':users' 		=> $users,
								':ips' 			=> $ips);
								
				query($sql,$params,false);
				
				return true;
				
			} elseif ((isset($i)) && ($i > 1)){
				// Error if multiple entires found with the same name and type
				systemLog(3,'Duplicate entries found in permissions table.',1,3);
				return false;
			
			} else {
				// If a single entry was found check if the current user and ip are allowed
				$users = json_decode($element['users'],true);
				$ips = json_decode($element['ips'],true);
				$check = 0;
				
				if (empty($users)) {
				
					$check++;
				
				} elseif (in_array($_SESSION['user']['class'], $users)) {
				
					$check++;
				
				}
				
				if (empty($ips)) {
				
					$check++;
				
				} elseif (in_array($_SERVER['REMOTE_ADDR'], $users)) {
				
					$check++;
				
				}
				
				if (($_SESSION['user']['status'] == 'pending') && ($type == 'page') && (!strstr($_SERVER['REQUEST_URI'],$_SESSION['user']['home'])) && (!strstr($_SERVER['PHP_SELF'],'ext'))) {
					$check = 0;
				}
				
				if ($check == 2) {
					
					return true;
				
				} else {
					
					if ($type == 'page') {
					
						header('Location: '.PATH.$_SESSION['user']['home']);
						exit();
					
					} else {
					
						return false;
						
					}
					
				}
			
			}
			
		} else {
			// This is to preform a hardcoded permissions check. dont use this unless there is a specific reason
			if (in_array($_SESSION['user']['class'], $valid_users)) {
				return true;
			} else {
				return false;
			}
		
		}
	
	} elseif (!strstr($_SERVER['PHP_SELF'],'ext')) {
		
				//$_SESSION['messages'][] = '<div class="error login_message">The page you are attempting to access is restricted. Please login and try again.</div>';
				header('Location: '.PATH.'index.php');
				exit();
			
	}
	
}

function checkUser($allowed) {
	if (in_array($_SESSION['user']['class'], $allowed)) {
		Return True;
	} else {
		Return False;
	}
}

function getUser($args) {

	$options = array('class','home','name');
	foreach ($options as $option) {
		if (isset($args[$option])) {
			$field = $option;
			$value = $args[$option];
		}
	}
	
	if (!isset($_SESSION['types'])) {
		
		$sql = "SELECT *
				FROM userTypes";
						
		$result = query($sql,NULL);
		
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$_SESSION['types'][$row['userClass']] = array('class' => $row['userClass'], 'name' => $row['userType'], 'home' => $row['home']);
		}
		
	}
	
	if (isset($field) && isset($value)) {
		foreach ($_SESSION['types'] as $types) {
		
			if ($types[$field] == $value) {
				
				$return = $types[$args['get']];
				
			}
			
		}
	}
	
	if (!isset($return)) {
		message('error','Invalid arguments sent to getUser function');
		$return = 'Error';
	}
	
	return $return;

}


	
function timeDifference($tm,$tmoff=0,$rcs = 0) {
	if ($tmoff==0){
    $cur_tm = time(); 
	}else{
	$cur_tm=$tmoff;
	}
	$dif = $cur_tm-$tm;
    $pds = array('second','minute','hour','day','week','month','year','decade');
    $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
    for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);
   
    $no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
    if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm);
    return $x;
}

function detectMobile($useragent=false) {

	if (isset($_SERVER['HTTP_USER_AGENT'])) {
	
		$useragent = ((!$useragent) ? strtolower($_SERVER['HTTP_USER_AGENT']) : $useragent);

		$devices = array('generic' => '(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)','webOS' => '(webos|hpwOS)', 'Android' => 'android', 'Blackberry' => '(blackberry|rim tablet)', 'iPhone' => '(iphone|ipod|ipad)', 'Opera' => '(opera mini|opera mobi)', 'Palm' => '(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)', 'Windows' => '(iemobile|ppc|windows phone)');
		$mobile = false;

		foreach ($devices as $device => $keys) {
			if(preg_match("/$keys/i", $useragent)) {
				$mobile = $device;

			}
		}
		
	} else {
		$mobile = false;
	}
 
	return $mobile;
}

function encrypt($string,$key) {
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
} 

function decrypt($string,$key) {
	$string = str_replace(' ','+',$string);
    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
}

function authenticate($r) {
	
	$reponse['error'] = false;
	$r = decrypt($r,AUTH_KEY);
	
	if (substr_count($r, ",") == 4) {
		$r = explode(',',$r);
		if (in_array('error',$r)) {
			$reponse['error'] = 'Authenication server error';
		} else {
		
			$reponse = array('origin_server' => $r[0],'auth_server' => $r[4],'pid' => $r[2],'email' => $r[3],'start' => $r[1],'stop' => microtime(true));
			$reponse['time'] = $reponse['stop'] - $reponse['start'];
			if (($reponse['auth_server'] != AUTH_URL) || ($reponse['origin_server'] != $_SERVER['SERVER_NAME'])) {
				$reponse['error'] = 'Authentication server error';
				systemLog(3,'Attempt to spoof login. Invalid origin or auth server.');
			}
			
			if ($reponse['time'] > 30) {
				$reponse['error'] = 'Authenication request timeout';
			}
		}

	} else {
		$reponse['error'] = 'Invalid authenication response';
	}
	return $reponse;

}

function hexLighter($hex,$factor = 30){ 
    $new_hex = ''; 
     
    $base['R'] = hexdec($hex{0}.$hex{1}); 
    $base['G'] = hexdec($hex{2}.$hex{3}); 
    $base['B'] = hexdec($hex{4}.$hex{5}); 
     
    foreach ($base as $k => $v) 
        { 
        $amount = 255 - $v; 
        $amount = $amount / 100; 
        $amount = round($amount * $factor); 
        $new_decimal = $v + $amount; 
     
        $new_hex_component = dechex($new_decimal); 
        if(strlen($new_hex_component) < 2) 
            { $new_hex_component = "0".$new_hex_component; } 
        $new_hex .= $new_hex_component; 
        } 
         
    return $new_hex;     
}

/*!	\fn getFloorCount($checkonType = 'all')
 *  \brief Calculates total number of people on floor.
 *
 *  \param $checkonType is an initialized string.
 *	\returns floor count as integer
 */
function getFloorCount($checkonType = 'all') {
	$sql = "SELECT sum(transactions.passCount) AS passCount
		FROM transactions,
			passTypes
		WHERE transactions.passExpired = 0
			AND passTypes.id = transactions.passType
			AND transactions.passList != :ATHLETE_LIST";

	$params = array(':ATHLETE_LIST' => ATHLETE_LIST);

	if ($checkonType != 'all') {	
		$sql .= " AND passTypes.checkonType = :checkonType";
		$params[':checkonType'] = $checkonType;
	}	

	$results = query($sql, $params);
	$results = fetch($results);

	if ($results['passCount'] == NULL) {
		return 0;
	}
	return $results['passCount'];
}

// Checks for floor capacity. Updates MOTD if reaching threshold or at capacity
function checkFloorCapacity() {
    $passcountTotal = getFloorCount();

    if($passcountTotal >= FLOOR_LIMIT) {
        $sql = "UPDATE globalVars 
            SET value = :MOTD
            WHERE variable = 'MOTD'";
						
        $passesLeft = FLOOR_CAPACITY - $passcountTotal;
        $params = array(':MOTD' => "<div class = \"floorWarning\">Warning: Floor nearing maximum capacity, $passesLeft number of passes remaining.</div>");
				
        query($sql,$params);
    }
    if($passcountTotal >= FLOOR_CAPACITY) {
        $sql = "UPDATE globalVars 
            SET value = :MOTD
            WHERE variable = 'MOTD'";
						
        $params = array(':MOTD' => "<div class = \"floorClosed\">Warning: Floor Capacity has been reached, floor closed until further notice.</div>");
        
        query($sql,$params);
        
        $sql = "UPDATE globalVars 
            SET value = 0
            WHERE variable = 'FLOOR_OPEN'";
				
        query($sql);
    }    
}
/*!
 *   \brief This function checks whether the current client's IP is associated with the location to be checked.
 *
 *   \param $access_location is a string that corresponds to a valid Access Location. 
 *   \returns TRUE if current client's IP location matches $access_location and FALSE otherwise
 */
function checkLocation ($access_location){
    $sql = "SELECT location FROM equipment WHERE ip = :user_ip;";
    $params = array(':user_ip' => $_SERVER['REMOTE_ADDR']);
    
    $result = query($sql, $params);
    
    if ($result->rowCount() > 0) {
    
        $currentLoc = fetch($result);
        $currentLoc = $currentLoc['location'];
        
        if ($access_location == $currentLoc) {
            return 'TRUE';
        } else {
            return 'FALSE';
        }
    } else {
        return 'FALSE';
    }    
}

/*
 * \brief This function performs maintenance on the database, adding proper casing to various tables and removing HTML encoded characters.
 */
function dbCleanup() {
    $counter = 0;
    
    $tables = array('organizations', 'passListPeople', 'passLists');
    
    foreach ($tables as $table) {
        $sql = "SELECT id, name FROM $table;";     
        $result = query($sql);
    
        while ($row = fetch($result)) {
            $properName = ucname(html_entity_decode($row['name'], ENT_QUOTES));
            
            if ($properName != $row['name']) {
                ++$counter;
                $sql = "UPDATE $table SET name = :properName WHERE id = :id;";
                $params = array(    ':properName'   => $properName,
                                    ':id'           => $row['id']
                                );
                            
                query($sql, $params);
            }                     
        }
        
        if ($table == 'organizations') {
            // Fix THON Chair names.
            $sql = "SELECT id, chair FROM $table;";     
            $result = query($sql);
            
            while ($row = fetch($result)) {
            $properName = ucname(html_entity_decode($row['chair'], ENT_QUOTES));
            
                if ($properName != $row['chair']) {
                    ++$counter;
                    $sql = "UPDATE organizations SET chair = :properName WHERE id = :id;";
                    $params = array(    ':properName'   => $properName,
                                        ':id'           => $row['id']
                                    );
                                
                    query($sql, $params);
                }                     
            }
            
            // Fix THON Chair PSU ID's.
            $sql = "SELECT id, psu FROM $table;";     
            $result = query($sql);
            
            while ($row = fetch($result)) {
            $properName = strtolower($row['psu']);
            
                if ($properName != $row['psu']) {
                    ++$counter;
                    $sql = "UPDATE organizations SET psu = :properName WHERE id = :id;";
                    $params = array(    ':properName'   => $properName,
                                        ':id'           => $row['id']
                                    );
                                
                    query($sql, $params);
                }                     
            }            
        }     
    }

    // Fix user PSU ID's.
    $sql = "SELECT id, psu FROM users;";     
    $result = query($sql);
    
    while ($row = fetch($result)) {
        $properName = strtolower($row['psu']);
    
        if ($properName != $row['psu']) {
            ++$counter;
            $sql = "UPDATE users SET psu = :properName WHERE id = :id;";
            $params = array(    ':properName'   => $properName,
                                ':id'           => $row['id']
                            );
                        
            query($sql, $params);
        }
    }  
    
    return $counter;
}

/*
 * \brief This function deletes all user accounts over 30 days old that are marked as 'pending'.
 */
function purgeUsers() {
    $today = date("Y-m-d H:i:s");
    
    $sql = "DELETE FROM users WHERE DATEDIFF(:today, created) >= 30 AND status = 'pending';";
    $params = array(':today' => $today);
    
    $result = query($sql, $params);
    
    return fetch($result, 'count');
}

function ucname($string) {
    $string =ucwords(strtolower($string));

    foreach (array('-', '\'') as $delimiter) {
      if (strpos($string, $delimiter)!==false) {
        $string =implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
      }
    }
    return $string;
}

/*! \fn getMachineData()
 *  \brief Returns all data for current machine from equipment table.
 */
function getMachineData()
{
	$sql = "SELECT *
			FROM equipment
			WHERE ip = :ip;";

	$params = array(':ip'		=>		$_SERVER['REMOTE_ADDR']);

	$result = query($sql, $params);

	if (fetch($result, 'count') == 0)
	{
		$machineData['location'] = 'Unknown';
		$machineData['name']  = 'Unknown';
		$machineData['id'] 		 =  0;
	}
	else
	{
		$machineData = fetch($result);
		$machineData['location'] = locName($machineData['location']);
	}

	return $machineData;	
}

/*! \fn locName($locID)
 *  \brief Returns name of location where id == $locID
 */
function locName($locID)
{
	$sql = "SELECT location 
			FROM locations 
			WHERE id = :locID;";

	$params = array(':locID'	=>		$locID);

	$result = query($sql, $params);
	
	$location = fetch($result);
	
	return $location['location'];
}

/*! \fn scanBinder($barcode)
 *  \brief Attempts to update a binder's location.
 *
 *	\param $barcode is an initialized integer
 *	\returns true if location update was successful, false otherwise.
 */
function scanBinder($barcode) {
	// Look-Up barcode.
	$sql = "SELECT COUNT(*) AS count FROM binders WHERE barcode = :barcode;";

	$params = array(':barcode'	=> $barcode);

	$result = query($sql, $params);
	$result = fetch($result);

	// If binder doesn't exist, return false and give error message.
	if ($result['count'] == 0) {
		message('error', 'No binder exists with that barcode. Please try again.');

		return false;
	}

	// If binder does exist then look up info.
	$sql = "SELECT id, name, location FROM binders WHERE barcode = :barcode;";

	$result = query($sql, $params);
	$binderInfo = fetch($result);

	// Get user's location.
	$userLocation = getLocation();
	$userLocation = $userLocation['location'];

	// If user's location is unknown, return false and give error message.
	if ($userLocation == 'Unknown') {
		message('error', 'You are not in a valid location to scan binders. Please try again.');

		return false;
	}

	// Update binder to new location.
	$sql = "UPDATE binders SET location = :location WHERE id = :id;";

	$params = array(':location'		=> $userLocation,
					':id'			=> $binderInfo['id']);

	query($sql, $params);

	message('success', '<b>' . $binderInfo['name'] . '</b> location successfully updated from ' . 
			$binderInfo['location'] . ' to <b>' . $userLocation . '</b>');

	return true;
}

function setBinderSlot($currentBinder = BINDER) {
	// Make sure current binder slot is not above max range. 
	$sql = "SELECT id, currentSlot, minSlot, maxSlot FROM binders WHERE id = :currentBinder;";

	$params = array(':currentBinder'	=>	$currentBinder);

	$result = query($sql, $params, false);
	$result = fetch($result);

	// Make sure we're not using slot 'zero'
	if ($result['currentSlot'] % 10 == 0) {
		$result['currentSlot']++;
	}

	if ($result['currentSlot'] <= $result['maxSlot']) {
		// Make sure current slot is not in use. 
		$sql = 	"SELECT COUNT(*) AS count FROM transactions 
					WHERE binder = :binder
					AND   idSlot = :currentSlot
					AND   passExpired = 0";

		$params = array(
						':binder' => $currentBinder,
						':currentSlot' => $result['currentSlot']					
						);

		$slotInUse = query($sql, $params, false);
		$slotInUse = fetch($slotInUse);		

		
		//die(var_dump($result['currentSlot']));

		if ($slotInUse['count'] == 0) {
			// Return binder data.
			$binderData = array(
								'binder'		=>	$currentBinder,
								'binderSlot'	=>	$result['currentSlot']
								);		

			$result['currentSlot']++;

			$sql = "UPDATE binders SET currentSlot = :currentSlot WHERE id = :binder;";			

			$params = array(
							':binder' => $currentBinder,
							':currentSlot' => $result['currentSlot']					
							);

			query($sql, $params, false);

			return $binderData;
		} else {	// Call function recursively.
			// Find all in-use slots for binder.
			$sql = "SELECT idSlot FROM transactions WHERE binder = :currentBinder AND passExpired = 0;";

			$params = array(
							':currentBinder' => $currentBinder					
							);

			$usedSlots = query($sql, $params, false);
			$usedSlots = $usedSlots->fetchAll(PDO::FETCH_COLUMN,0);

			$slot = $result['maxSlot'] + 1;
			//die(var_dump($slot));

			// Find the smallest unused slot.
			for($i = $result['minSlot']; $i <= $result['maxSlot']; $i++) {
				// If $i is not in the array set $slot to its value.
				if ($i % 10 != 0 && !in_array($i, $usedSlots)) {
					if ($slot > $i) {
						$slot = $i;						
					}					
				}
			}
			// If no unused slot was found, update currentSlot for Binder to maxSlot + 1 and
			// recursively call function.
			if ($slot == $result['maxSlot'] + 1) {
				$sql = "UPDATE binders SET currentSlot = :currentSlot WHERE id = :binder;";

				$params = array(
							':binder' => $currentBinder,
							':currentSlot' => $slot					
							);

				query($sql, $params, false);

				return setBinderSlot();
			} else {
				// If Slot was found, return binderData.
				$binderData = array(
								'binder'		=>	$currentBinder,
								'binderSlot'	=>	$slot
								);		

				$slot++;

				$sql = "UPDATE binders SET currentSlot = :currentSlot WHERE id = :binder;";

				$params = array(
								':binder' => $currentBinder,
								':currentSlot' => $slot					
								);

				query($sql, $params, false);

				return $binderData;
			}			
		}

	} else {
		// Set current binder to minimum slot and call function recursively on next binder.
		$sql = "UPDATE binders SET currentSlot = :minSlot WHERE id = :binder;";

		$params = array(
						':minSlot'	=>	$result['minSlot'],
						':binder'	=>	$currentBinder
						);

		query($sql, $params, false);

		$currentBinder++;

		if ($currentBinder == BINDER) {
			message('error', "All Binders are Full.");
			return false;
		}

		query($sql, $params, false);

		// Find max binder.
		$sql = "SELECT MAX(id) AS max FROM binders WHERE auto = 1;";
		$maxBinder = query($sql);
		$maxBinder = fetch($maxBinder);

		if ($currentBinder > $maxBinder['max']) {
			$currentBinder = 1;
		}
		//die(var_dump($currentBinder));

		return setBinderSlot($currentBinder);
	}

}
?>
