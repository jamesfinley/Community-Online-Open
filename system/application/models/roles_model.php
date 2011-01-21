<?php

class Roles_Model extends MY_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
	function insert($user_id, $group_id, $parent_id, $type)
	{
		$insert = array
		(
			'user_id'	 	=> $user_id,
			'group_id'		=> $group_id,
			'parent_id'		=> $parent_id,
			'type'			=> $type,
			'created_at' 	=> time(),
			'updated_at'	=> time()
		);
	
		$this->db->insert('roles', $insert);
		
		return $this->db->insert_id();
	}

	function update($id, $user_id, $group_id, $parent_id, $type)
	{
		$update = array
		(
			'user_id' 		=> $user_id,
			'group_id'		=> $group_id,
			'parent_id'		=> $parent_id,
			'type'			=> $type,
			'updated_at'	=> time()
		);
		
		// Remove NULL values
		$update = array_filter($update);
	
		$this->db->update('roles', $update, array('id' => $id));
		
		return true;
	}
	
	function item($id = null) 
	{
		$this->do_select();
	
		if ($id)
		{
			$result = $this->db->where('id', $id)->get('roles');
		}
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function items($user_id, $group_id, $parent_id, $type, $limit = 25, $page = 1) 
	{
		$this->do_select();

		if ( $user_id )
		{
			$this->db->where('user_id', $user_id);
		}
		
		if ( $group_id )
		{
			$this->db->where('group_id', $group_id);
		}

		if ( $parent_id )
		{
			$this->db->where('parent_id', $parent_id);
		}

		if ( $type )
		{
			$this->db->where('type', $type);
		}
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else if ($limit)
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}

		$result = $this->db->get('roles');
		
		return $result;
	}
	
	function delete($id, $user_id = NULL, $group_id = NULL, $parent_id = NULL, $type = NULL) 
	{		
		if ( $id )
		{
			$this->db->where('id', $id);
		}	
		
		if ( $user_id )
		{
			$this->db->where('user_id', $user_id);
		}
		
		if ( $group_id )
		{
			$this->db->where('group_id', $group_id);
		}

		if ( $parent_id )
		{
			$this->db->where('parent_id', $parent_id);
		}

		if ( $type )
		{
			$this->db->where('type', $type);
		}		
	
		$this->db->delete('roles');
		
		return true;
	}
}