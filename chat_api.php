<?php
error_reporting(E_ALL);

define('BASEPATH', TRUE);
require('system/application/config/database.php');

$link = mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
mysql_select_db($db['default']['database']);

$_user_cache = array();

function query() 
{
	$args = func_get_args();

	if ( $args > 0 )
	{	
		$sql = array_shift($args);
		$pos = 0;

	    foreach ($args as $val) 
	    {
	        $pos += strpos(substr($sql, $pos, strlen($sql)-$pos), '?');

		    if ($pos !== false) {
		    	$replacement = "'" . mysql_real_escape_string($val) . "'";
		        $sql = substr_replace($sql, $replacement, $pos, strlen('?'));
		    	$pos += strlen($replacement);
		    }
		    else
		    {
		    	break;
		    }
		}

		return mysql_query($sql);
	}
    
    return FALSE;
}

function get_users($service_id, $group_id) {
	global $_user_cache;

	include_once 'facebook_connect/facebook.php';
	$fb = new Facebook('abaaa7839c1bec3e9e75b0bb33c527e6', '72e9626687bd4877e420ef4f59182a27');

	$request = query('SELECT users.*, roles.type FROM users JOIN chat_messages ON chat_messages.user_id = users.id LEFT JOIN roles ON roles.`group_id`  = chat_messages.`group_id` && roles.user_id = users.id WHERE chat_messages.service_id = ? AND chat_messages.group_id = ? GROUP BY users.id', $service_id, $group_id);

	while ($user = mysql_fetch_object($request))
	{	
		$id = $user->id;
		$username = '';

		/*if ( $user->uid && substr($user->uid, 0, 1) == 'f' ) 
		{
			$user->uid = str_replace('f', '', $user->uid);
			$user_details = $fb->api_client->users_getInfo($user->uid, 'last_name, first_name');
			$username = $user_details[0]['first_name'].' '.$user_details[0]['last_name'];
		}
		else 
		{*/
			$username = $user->first_name.' '.$user->last_name;
		//}

		$_user_cache[$id] = array
		(
			'name' => $username,
			'is_facilitator' => ($user->type == 'faciliator' || $user->type == 'apprentice') ? TRUE : FALSE,
			'is_guest' => $user->type == 'guest' ? TRUE : FALSE,
		);
	}
}

function changeGroup($group_id, $user_id)
{
	$group_id = mysql_real_escape_string($group_id);
	$user_id = mysql_real_escape_string($user_id);

	// Is the user a guest of any groups
	$request = query("SELECT group_id FROM roles WHERE type='guest' AND user_id = ?", $user_id);

	$is_guest = FALSE;	

	if ( mysql_num_rows($request) )
	{	
		$is_guest = TRUE;

		// It is a guest a group

		$id = mysql_fetch_object($request)->group_id;

		//echo $id." ".$group_id;

		// If the group the same as the request group 
		if ( $id != $group_id )
		{
			$is_guest = FALSE;
			query("DELETE FROM roles WHERE type = 'guest' AND user_id = ?", $user_id);
		}
	}

	// Is the group a group that the user is a member of?
	$request = query("SELECT * FROM groups_users WHERE group_id = ? AND user_id = ?", $group_id, $user_id);

	if ( $is_guest == FALSE && ! mysql_num_rows($request) )
	{		
		// echo 'inserted new role';

		query("INSERT INTO roles VALUES (NULL, ?, ?, NULL, 'guest', ?, ?)", $user_id, $group_id, time(), time());
	}
}

function username($user_id)
{
	global $_user_cache;

	if ( empty($_user_cache) )
	{
		get_users($service_id, $group_id);
	}

	if (isset($_user_cache[$user_id]))
	{
		return $_user_cache[$user_id]['name'];
	}

	return false;		
}

function faciliator_online($group_id)
{
	$group_id = mysql_real_escape_string($group_id);

	$timestamp = strtotime('-15 minutes');
	$sql = "SELECT users.* FROM users JOIN groups_users ON users.id = groups_users.user_id AND groups_users.group_id = '{$group_id}' JOIN roles ON roles.user_id = groups_users.user_id AND (roles.type = 'facilitator' OR roles.type = 'apprentice')  WHERE users.last_on > {$timestamp}";

	$results = query("SELECT users.* FROM users JOIN groups_users ON users.id = groups_users.user_id AND groups_users.group_id = ? JOIN roles ON roles.user_id = groups_users.user_id AND (roles.type = 'facilitator' OR roles.type = 'apprentice')  WHERE users.last_on > ?", $group_id, $timestamp);

	return mysql_num_rows($results) > 0 ? TRUE : FALSE;
}

function is_faciliator($user_id)
{
	global $_user_cache;

	if (isset($_user_cache[$user_id]))
	{
		return $_user_cache[$user_id]['is_facilitator'];
	}

	return false;		
}

function is_guest($user_id)
{
	global $_user_cache;

	if (isset($_user_cache[$user_id]))
	{
		return $_user_cache[$user_id]['is_guest'];
	}

	return false;		
}

function get_user($user_id) {
	global $_user_cache;

	if (isset($_user_cache[$user_id]))
	{
		return $_user_cache[$user_id];
	}

	return false;
}

$service_id = NULL;
$group_id = NULL;
$since_id = isset($_GET['since_id']) ? $_GET['since_id'] : 0;
$reply_id = isset($_GET['reply_id']) ? $_GET['reply_id'] : 0;

if ( isset($_GET['service_id'], $_GET['group_id']) )
{
	$service_id = $_GET['service_id'];
	$group_id = $_GET['group_id'];
}
else if ( isset($_POST['service_id'], $_POST['group_id']) )
{
	$service_id = $_POST['service_id'];
	$group_id = $_POST['group_id'];
}

if ( $service_id && $group_id )
{
	switch ($_GET['action'])
	{
		case 'init':
			$data = array('timestamp' => time());
			break;
		case 'post':			
			query("INSERT INTO chat_messages VALUES(NULL, ?, ?, ?, ?, 0, ?)", $service_id, $group_id, $_POST['user'], time(), $_POST['message']);

			$data = array('message' => 'successfully posted', 'code' => 200, 'id' => mysql_insert_id());
			break;
		case 'reply':
			query("INSERT INTO chat_messages VALUES(NULL, ?, ?, ?, ?, ?, ?)", $service_id, $group_id, $_POST['user'], time(), $_POST['id'], $_POST['message']);

			$data = array('message' => 'successfully posted', 'code' => 200, 'id' => mysql_insert_id());
			break;
		case 'messages_and_replies':
			get_users($service_id, $group_id);

			$results = query("SELECT * FROM chat_messages WHERE service_id = ? AND group_id = ? AND id > ? AND reply_to = 0 ORDER BY id", $service_id, $group_id, $since_id);

			$messages  = array();
			$last_id   = 0;
			while ($row = mysql_fetch_object($results)) {
				$messages[] = array('id' => $row->id, 'uid' => $row->user_id, 'name' => username($row->user_id), 'is_faciliator' => is_faciliator($row->user_id), 'is_guest' => is_guest($row->user_id), 'text' => stripslashes($row->text), 'date' => gmdate('F d, Y g:i:s a', $row->created_at - 21600));
				$last_id = $row->id+1;
			}

			$results   = query("SELECT * FROM chat_messages WHERE service_id = ? AND group_id = ? AND reply_to != 0 AND id > ? ORDER BY id ASC", $service_id, $group_id, $reply_id);

			$replies   = array();
			$last_id   = 0;
			while ($row = mysql_fetch_object($results)) {
				$replies[] = array('reply_to' => $row->reply_to, 'id' => $row->id, 'uid' => $row->user_id, 'name' => username($row->user_id), 'is_faciliator' => is_faciliator($row->user_id), 'is_guest' => is_guest($row->user_id), 'text' => stripslashes($row->text), 'date' => gmdate('F d, Y g:i:s a', $row->created_at - 21600));
				$last_id = $row->id+1;
			}
			$data      = array('timestamp' => $last_id, 'items' => array('messages' => $messages, 'replies' => $replies));
			break;
		default:
			$data = array('error' => 'improper action');
			break;
	}

	$group = query('SELECT name FROM groups WHERE id = ?', $group_id);
	$group = mysql_fetch_object($group);
	$data['group_name'] = $group->name;

	//$session = unserialize(stripcslashes($_COOKIE['ci_session']));
	$user_id  = $_COOKIE['co_user_id'];
	$email    = $_COOKIE['co_email'];
	$password = $_COOKIE['co_password'];

	// If User if logged in
	if ( isset($user_id) )
	{
		$request = query("SELECT users.id, CONCAT(users.first_name, ' ', users.last_name) AS full_name FROM users WHERE id = ?", $user_id);

		$row = mysql_fetch_object($request);

		// Hey We have a valid user
		if ( $row )
		{
			// Updates last_on status
			query("UPDATE users SET last_on = ? WHERE id = ?", time(), $row->id);

			$data['user_name'] = $row -> full_name;

			changeGroup($group_id, $row->id);

			$request = query("SELECT roles.type FROM users JOIN roles ON roles.group_id = ? AND roles.user_id = users.id WHERE user_id = ?", $group_id, $row->id);
			$row = mysql_fetch_object($request);

			if ( $row )
			{
				$data['is_member'] = $row->type == 'guest' ? FALSE : TRUE;
			}
			else
			{
				$data['is_member'] = TRUE;
			}
			
			$data['group_id'] = $group_id;
		}
		else
		{
			$data['error'] = 'Invalid user info';
		}
	}

	$data['facilitators_online'] = faciliator_online($group_id);

	$hash = md5(serialize($data));
	$data['hash'] = $hash;
}
else
{
	$data = array('error' => 'Service or Group ID\'s not present.');
}

header('Content-type: application/json');
echo json_encode($data);

mysql_close($link);