<?php

class Categories_Model extends MY_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
	function insert($group_id, $name)
	{
		$insert = array
		(
			'group_id' 		=> $group_id,
			'name' 			=> $name,
			'created_at' 	=> time(),
			'updated_at'	=> time()
		);
	
		$this->db->insert('group_categories', $insert);
		
		return $this->db->insert_id();
	}

	function update($category_id, $group_id = NULL, $name = NULL)
	{
		$update = array
		(
			'group_id'	 	=> $group_id,
			'name' 			=> $name,
			'updated_at'	=> time()
		);
		
		// Remove NULL values
		$update = array_filter($update);
	
		$this->db->update('group_categories', $update, array('id', $category_id));
		
		return true;
	}
	
	function item($category_id) 
	{
		$this->do_select();

		if ( $category_id )
		{
			$this->db->where('id', $category_id);
		}
		
		$result = $this->db->get('group_categories');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function items($group_id, $limit = 25, $page = 1) 
	{
		$this->do_select();

		if ( $group_id )
		{
			$this->db->where('group_id', $category_id);
		}
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}
		
		$result = $this->db->get('group_categories');
		
		return $result;
	}
	
	function delete($category_id) {
		$this->db->delete('group_categories', array('id' => $category_id));
		
		return true;
	}
}