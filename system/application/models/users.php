<?php

class users extends MY_Model {
	
	function __construct()
	{
		parent::Model();
		
		//check for logged session
		$this->user_account = false;
		if ($this->session->userdata('uid') && $this->session->userdata('password'))
		{
			$check = $this->db->where('uid', $this->session->userdata('uid'))->where('password', $this->session->userdata('password'))->limit(1)->select('uid, email, first_name, last_name')->get('users');
			if ($check->num_rows() === 1)
			{
				$this->user_account = $check->row();
			}
		}
		    
	    $this->cache_users();
	    $this->cache_roles();
	}
	
	private function cache_users()
	{
	    $users_results = $this->db->get('users');
	    $this->users = array();
	    foreach ($users_results->result() as $user)
	    {
	    	$this->users[$user->id] = $user;
	    }
	}
	
	private function cache_roles()
	{
		$this->roles = $this->db->get('roles')->result();
	}
	
	function generateResetHash($email)
	{
		$this->db->where('email', $email);
		
		if ( $this->db->count_all_results('users') )
		{
			$this->load->helper('string');
	
			$hash = random_string('alnum', 10);
	
			$this->db->update('users', array('reset_hash' => $hash), array('email' => $email));
			
			return site_url('reset?hash='.$hash);		
		}
		else
		{
			return FALSE;
		}
	}

	function insert($email, $password, $first_name, $last_name)
	{
		$insert = array
		(
			'email' 		=> $email,
			'password'		=> md5($password),
			'first_name' 	=> $first_name,
			'last_name'		=> $last_name
		);
			
		$this->db->insert('users', $insert);
		$id = $this->db->insert_id();
		
		$this->cache_users();
		
		return $id;
	}
	
	function update($id, $email = NULL, $password = NULL, $first_name = NULL, $last_name = NULL)
	{
		$update = array
		(
			'first_name'	=> $first_name,
			'last_name'		=> $last_name,
			'email'			=> $email
		);
		
		if ( $password )
		{
			$update['password'] = md5($password);
 		}
		
		// Remove NULL values
		$update = array_filter($update);
	
		$result = $this->db->update('users', $update, array('id' => $id));
		
		$this->cache_users();
				
		return $result;
	}
	
	function online($group_id)
	{
		//$this->do_select();
		
		if ($group_id) {
			$this->db->where('id IN (SELECT user_id FROM groups_users WHERE group_id = '.$group_id.')', null, false);
		}

		return $this->db->where('last_on >', strtotime('-15 minutes'))->select('id, CONCAT(users.first_name, " ", users.last_name) AS full_name', false)->get('users');
	}
	
	function login($email, $password)
	{
		$result = $this->db->where('email', $email)->where('password', $password)->get('users');
		if ($result->num_rows() === 1)
		{
			return $result->row()->id;
		}
		return false;
	}
	
	function create_with_facebook($uid)
	{
		$this->db->insert('facebook_connect', array('uid' => $uid));
		$this->db->insert('users', array('uid' => 'f'.$uid));
		
		return true;
	}
	
	function account($uid = null)
	{
		if ($uid)
		{
			$account = $this->db->where('uid', $uid)->limit(1)->select('uid, email, first_name, last_name')->get('users');
			if ($account->num_rows() === 1)
			{
				return $account->row();
			}
			return false;
		}
		else {
			return $this->user_account;
		}
	}
	
	function mark_as_online($id)
	{
		$this->db->where('id', $id);
		$this->db->update('users', array('last_on' => time()));
	}
	
	function users_in_groups($campus_id, $groups, $type = NULL, $limit = 25, $page = 1)
	{
		if ( $groups )
		{
			$this->db->join('groups_users', 'groups_users.user_id = users.id');
			$this->db->where_in('groups_users.group_id', $groups);
			$this->db->group_by('users.id');
		}		

		return $this->items($campus_id, $type, $limit, $page);
	}
	
	function items_with_ids($ids, $limit = 25, $page = 1)
	{
		if ( is_array($ids) )
		{
			$this->db->where_in('id', $ids);
		}
		
		return $this->items($limit, $page);
	}
	
	function items($limit = 25, $page = 1) 
	{
	
		$this->do_select();
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else if ($limit)
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}
		
		$this->db->order_by('first_name, last_name');

		$result = $this->db->get('users');
		
		return $result;
	}
	
	function items_in_group($group_id, $limit = 25, $page = 1)
	{
		$this->db->join('groups_users AS gu', 'gu.user_id = users.id');
		$this->db->where('gu.group_id', $group_id);
		
		return $this->items($limit, $page);
	}
		
	function items_with_role($type, $group_id = 0, $limit = 25, $page = 1) 
	{
		$this->do_select();
		
		if ( $type )
		{
			$this->db->join('roles', 'roles.user_id = users.id');

			if ( is_array($type) )
			{
				$this->db->where_in('roles.type', $type);
			}
			else
			{
				$this->db->where('roles.type', $type);			
			}
			
			if ( $group_id )
			{
				$this->db->where('roles.group_id', $group_id);
			}
		}
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else if ($limit)
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}

		$result = $this->db->get('users');
		
		return $result;
	}	
	
	function item($id = NULL, $hash = NULL)
	{
		/*$this->do_select();

		$result = $this->db->where('id', $id)->get('users');
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}*/

		foreach ($this->users as $user)
		{
			if ($user->id == $id)
			{
				return $user;
			}
			else if ( isset($hash) && $user->reset_hash == $hash )
			{
				return $user;
			}
		}
		return false;
	}
	
	function fullname($id)
	{
		$item = $this->item($id);
								
		return $item ? ($item->first_name . " " . $item->last_name) : '';
	}
	
	function is_pastor($id, $group_id = NULL)
	{
		return $this->is_role($id, $group_id, 'pastor');
	}
	
	function pastor($group_id)
	{
		$this->db->join('groups_users', 'groups_users.user_id = users.id');
		$this->db->join('roles', 'roles.user_id = users.id');
		$this->db->where('groups_users.group_id', $group_id);
		$this->db->where('roles.type', 'pastor');
		$this->db->group_by('users.id');
		$result = $this->db->get('users');

		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;	
	}
	
	function facilitator($group_id)
	{
		$this->db->select('users.*, roles.id AS role_id');
		$this->db->join('groups_users', 'groups_users.user_id = users.id');
		$this->db->join('roles', 'roles.user_id = users.id');
		$this->db->where('groups_users.group_id', $group_id);
		$this->db->where('roles.type', 'facilitator');
		$this->db->group_by('users.id');
		$result = $this->db->get('users');

		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;		
	}
	
	function is_admin($id)
	{
		return $this->is_role($id, 0, 'admin');
	}
	
	function is_facilitator($id, $group_id, $includes_apprentice = FALSE)
	{
		if ( $includes_apprentice )
		{
			if ($this->is_apprentice($id, $group_id))
			{
				return TRUE;
			}
		}
		
		return $this->is_role($id, $group_id, 'facilitator');
	}
	
	function is_apprentice($id, $group_id)
	{
		return $this->is_role($id, $group_id, 'apprentice');
	}
	
	function is_role($id, $group_id = NULL, $type)
	{
		/*$this->db->where('user_id', $id);
		
		if ( $group_id )
		{
			$this->db->where('group_id', $group_id);
		}
		
		$this->db->where('type', $type);
		$this->db->from('roles');
		$count = $this->db->count_all_results();
		
		return $count > 0 ? true : false;*/
		foreach($this->roles as $role)
		{
			if ($role->user_id == $id && (($group_id !== null && $role->group_id == $group_id) || $group_id === null) && $role->type == $type)
			{
				return true;
			}
		}
		return false;
	}
	
	function in_group($user_id, $group_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->where('group_id', $group_id);
		$this->db->from('groups_users');
		$count = $this->db->count_all_results();
		
		return $count > 0 ? true : false;	
	}
}