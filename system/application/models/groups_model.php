<?php

class Groups_Model extends MY_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        
        $this->cache_groups();
        $this->cache_users_in_groups();
    }
    
    private function cache_groups()
    {
	    $groups_results = $this->db->get('groups');
	    $this->groups = array();
	    foreach ($groups_results->result() as $group)
	    {
	    	$this->groups[$group->id] = $group;
	    }
    }
    
    private function cache_users_in_groups()
    {
    	$this->users_in_groups = array();
    	$results = $this->db->where('approved', 1)->get('groups_users')->result();
    	foreach ($results as $result)
    	{
    		if (!isset($this->users_in_groups[$result->group_id]))
    		{
    			$this->users_in_groups[$result->group_id] = array();
    		}
    		array_push($this->users_in_groups[$result->group_id], $result->user_id);
    	}
    }
    
	function insert($campus_id, $type, $name, $slug, $description, $service_times, $address, $city, $zip_code, $state, $country, $longitude, $latitude)
	{
		$insert = array
		(
			'campus_id' 	=> $campus_id,
			'type'			=> $type,
			'name' 			=> $name,
			'slug'			=> $slug,
			'description'	=> $description,
			'service_times'	=> $service_times,
			'address'		=> $address,
			'city'			=> $city,
			'zip_code'		=> $zip_code,
			'state'			=> $state,
			'country'		=> $country,
			'longitude'		=> $longitude,
			'latitude'		=> $latitude,
			'created_at' 	=> time(),
			'updated_at'	=> time()
		);
		
		$path = group_path($slug);
	
		$this->db->insert('groups', $insert);
		$id = $this->db->insert_id();
		
		$this->cache_groups();
		
		return $id;
	}
	
	function get_url($id)
	{
		$group = $group = $this->groups[$id] ? $this->groups[$id] : false;
		
		if ($group !== false)
		{
			switch ($group->type)
			{
				case 'campus':
					return 'locations/'.$group->slug;
					break;
				case 'ministry':
					return 'ministries/'.$group->slug;
					break;
				case 'small group':
					return 'groups/'.$group->slug;
					break;
				case 'master':
					return 'groups/'.$group->slug;
					break;
			}
		}
		return '';
	}
	
	function images($slug)
	{
		$this->load->helper('directory');
		
		$group = $this->item(null, $slug);
				
		if ( $group )
		{				
			$path = $this->group_path($slug);		
					
			$directory = directory_map($path, TRUE);
					
			$filtered = array_filter($directory, array($this, 'is_jpeg'));
			
			if (count($filtered))
			{
				$mapped = array_map(array($this, 'absolute_url'), $filtered, array_fill(0 , count($filtered), $slug));
				
				return $mapped;
			}
			else
			{
				return false;
			}
		}
		
		return array();
	}
	
	function absolute_url($path, $slug)
	{
		$file = ltrim($path, '/');
				
		return site_url(array('system', 'application', 'groups', $slug, $file));
	}
	
	function is_jpeg($file)
	{
		$ext = 'jpg';
	
		return substr($file, -strlen($ext)) === $ext ? true : false;
	}
	
	function group_path($slug)
	{
		$path = APPPATH.'/groups';
	
		// Add Groups Superfolder
		if ( ! is_dir($path) )
		{
			mkdir($path);
		}
		
		$path .= '/'.$slug;
		
		if ( ! is_dir($path) )
		{
			mkdir($path);
		}
		
		return $path;	
	}
	
	function _filter($array)
	{
		$newarray = array();
		
		foreach ($array as $key => $val)
		{
			if ($val !== null)
			{
				$newarray[$key] = $val;
			}
		}
		
		return $newarray;
	}

	function update($group_id, $campus_id = NULL, $type = NULL, $name = NULL, $slug = NULL, $description = NULL, $service_times = NULL, $address = NULL, $city = NULL, $zip_code = NULL, $state = NULL, $country = NULL, $longitude = NULL, $latitude = NULL)
	{
		$update = array
		(
			'campus_id' 	=> $campus_id,
			'type'			=> $type,
			'name' 			=> $name,
			'slug'			=> $slug,
			'description' 	=> $description,
			'service_times'	=> $service_times,
			'address'		=> $address,
			'city'			=> $city,
			'zip_code'		=> $zip_code,
			'state'			=> $state,
			'country'		=> $country,
			'longitude'		=> $longitude,
			'latitude'		=> $latitude,
			'updated_at'	=> time()
		);
				
		// Remove NULL values
		$update = $this->_filter($update);
	
		$this->db->update('groups', $update, array('id' => $group_id));
		
		$this->cache_groups();
		
		return true;
	}
	
	function hide($id, $should_hide, $type)
	{
		$update = array
		(
			'hide_'.$type 	=> $should_hide,
			'updated_at'	=> time()
		);
	
		$this->db->update('groups', $update, array('id' => $id));
		
		return TRUE;
	}
	
	function item($group_id = null, $slug = null) 
	{
		/*$this->do_select();

		if ($slug)
		{
			$result = $this->db->where('slug', $slug)->get('groups');
		}
		else
		{
			$result = $this->db->where('id', $group_id)->get('groups');
		}
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}*/
		foreach ($this->groups as $group)
		{
			if ($slug && $group->slug == $slug)
			{
				return $group;
			}
			elseif ($group_id && $group->id == $group_id)
			{
				return $group;
			}
		}
		
		return false;
	}
	
	function small_group($user_id, $is_guest = FALSE)
	{	
		$this->db->where('type','guest');
		$this->db->where('user_id', $user_id);
		$result = $this->db->select('*, group_id AS id')->get('roles');
		
		if ($result->num_rows())
		{
			return $result->row();
		}	
	
		$this->db->select('groups.*');
		$this->db->join('groups_users', 'groups_users.group_id = groups.id');
		
		$this->db->where('approved', 1)->where('groups_users.user_id', $user_id);
		$this->db->where('groups.type', 'small group');
		$result = $this->db->limit(1)->get('groups');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function groups_online($campus_id, $type = NULL , $limit = 25, $page = 1)
	{
		// SELECT groups.name, users.first_name, users.last_name FROM groups JOIN groups_users ON groups_users.group_id = groups.id JOIN users ON groups_users.user_id = users.id JOIN roles ON roles.user_id = users.id AND roles.type = 'apprentice' OR roles.type = 'facilitator' WHERE users.last_on > 1279729483 GROUP BY groups.id
		
		$this->db->join('groups_users', 'groups_users.group_id = groups.id');
		$this->db->join('users', 'groups_users.user_id = users.id')->where('groups_users.approved', 1);
		$this->db->join('roles', 'roles.user_id = users.id AND roles.type = \'apprentice\' OR roles.type = \'facilitator\'');
		
		// Last 15 minutes
		$this->db->where('users.last_on > ', time() - 900);
		$this->db->where('groups.type = "small group"');
		$this->db->group_by('groups.id');
	
		return $this->items($campus_id, $type, $limit, $page);
	}
	
	function groups_with_users($campus_id, $users, $type = NULL, $limit = 25, $page = 1)
	{
		if ( $users )
		{
			$this->db->join('groups_users', 'groups_users.group_id = groups.id')->where('groups_users.approved', 1);
			if (is_array($users))
			{
				$this->db->where_in('groups_users.user_id', $users);
			}
			else
			{
				$this->db->where('groups_users.user_id', $users);
			}
			$this->db->group_by('groups.id');
		}		

		return $this->items($campus_id, $type, $limit, $page);
	}
	
	function items($campus_id = 0, $type = NULL, $limit = 25, $page = 1) 
	{
	
		$this->do_select();

		if ( $campus_id )
		{
			$this->db->where('groups.campus_id', $campus_id);
		}
		
		if ( $type )
		{
			$this->db->where('groups.type', $type);
		}
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else if ($limit)
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}
		
		$this->db->order_by('groups.name');

		$result = $this->db->get('groups');
		
		return $result;
	}
		
	function belongs_to_type($user_id, $type, $group_id = NULL)
	{
		if ( $group_id )
		{
			$this->db->where('groups.id', $group_id);
		}
	
		$this->db->from('groups');
		$this->db->join('groups_users', 'groups_users.group_id = groups.id');
		$this->db->where('groups_users.user_id', $user_id)->where('groups_users.approved', 1);
		$this->db->where('groups.type', $type);
		
		return $this->db->count_all_results() ? TRUE : FALSE;
	}
	
	function belongs_to_group($id, $user_id)
	{
		/*$this->db->from('groups');
		$this->db->join('groups_users', 'groups_users.group_id = groups.id');
		$this->db->where('groups_users.user_id', $user_id);
		$this->db->where('groups.id', $id);
		
		return $this->db->count_all_results() ? TRUE : FALSE;*/
		
		//echo $id.' '.$user_id;
		
		return isset($this->users_in_groups[$id]) ? (in_array($user_id, $this->users_in_groups[$id]) ? true : false) : false;
		
	}
	
	function unassign_user($user_id, $group_id)
	{
		$type = $this->item($group_id)->type;
	
		if ( $type == NULL )
		{
			return FALSE;
		}
		
	  	if ( ! $this->belongs_to_group($group_id, $user_id) )
	  	{
	  		return FALSE;
	  	}
	
		$insert = array
		(
			'group_id' => $group_id,
			'user_id' => $user_id
		);	
	
		$this->db->delete('groups_users', $where);
				
		// Notify Admins, Facilitators, and Apprentices that a user left the group.
		$CI =& get_instance();
		
		$CI->users->begin();
		$CI->users->select('users.id');
		
		$users = $CI->users->items_with_role(array('admin', 'facilitator', 'apprentice'), $group_id)->result();
		
		$CI->users->end();
				
		foreach($users as $user)
		{
			$CI->notification_model->insert($user -> id, 'unassigned_user', $user_id);
		}
		
		
		return TRUE;
	}
	
	function assign_user($user_id, $group_id) 
	{
		$group = $this->item($group_id);
		$type  = $group->type;
	
		if ( $type == NULL )
		{
			return FALSE;
		}
		
	  	if ( $this->belongs_to_group($group_id, $user_id) )
	  	{
	  		return FALSE;
	  	}
	
		/*if ( $type != 'ministry' && $type != 'small group' && $this->belongs_to_type($user_id, $type) )
		{
			return FALSE;
		}*/
		
		if ($group->requires_member_approval)
		{
		
			$insert = array
			(
				'group_id' => $group_id,
				'user_id' => $user_id,
				'approved' => 0
			);		
			
			$this->db->insert('groups_users', $insert);
			
			$CI =& get_instance();
			$users = $CI->users->items_with_role(array('facilitator', 'apprentice'), $group_id)->result();
			$user  = $CI->users->item($user_id);
			
			$message = serialize(array(
				'posted_by' => $user->first_name.' '.$user->last_name,
				'subject' => $user->first_name.' '.$user->last_name.' wants to join your group '.$group->name,
				'created_at' => time(),
				'link' => site_url($this->groups_model->get_url($group->id).'/settings/members')
			));
			$short_message = $user->first_name.' '.$user->last_name.' wants to join your group <a href="'.site_url($CI->groups_model->get_url($group->id)).'">'.$group->name.'</a>. Click to <a href="'.site_url($CI->groups_model->get_url($group->id).'/settings/members').'">approve</a>.';
			foreach ($users as $member)
			{
				$this->notifications_model->create($member->user_id, $group->id, 'user needs approval', ($user->first_name.' '.$user->last_name.' wants to join your group '.$group->name), $message, $short_message);
			}
			
			return FALSE;
		}

		// Notify Admins, Facilitators, and Apprentices that a user joined the group.
		$CI =& get_instance();
		
		$CI->users->begin();
		$CI->users->select('users.id');
		
		$users = $CI->users->items_with_role(array('admin', 'facilitator', 'apprentice'), $group_id)->result();
		
		$CI->users->end();
				
		foreach($users as $user)
		{
			//$CI->notification_model->insert($user -> id, 'assigned_user', $user_id);
		}
	
		$insert = array
		(
			'group_id' => $group_id,
			'user_id' => $user_id
		);		
		
		$this->db->insert('groups_users', $insert);
		
		return true;
	}
	
	function delete($group_id) 
	{
		$this->db->delete('groups', array('id' => $group_id));
		$this->db->delete('group_categories', array('group_id' => $group_id));		
		$this->db->delete('roles', array('group_id' => $group_id));
		
		$this->cache_groups();
		
		return true;
	}
}