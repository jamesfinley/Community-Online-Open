<?php

class Settings_Model extends MY_Model 
{
    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
    function save($key, $value)
    {
    	$item = $this->item(NULL, $key);
    	
    	if ( $item )
    	{
    		$this->update($item->id, NULL, $value);
    		return $item->id;
    	}
    	
    	return $this->insert($key, $value);
    }
    
	function insert($key, $value)
	{
		$insert = array
		(
			'key' 		=> $key,
			'value' 	=> serialize($value),
			'created_at'=> time(),
			'updated_at'=> time()
		);
	
		$this->db->insert('settings', $insert);
		
		return $this->db->insert_id();
	}

	function update($id, $key = NULL, $value = NULL)
	{
		$update = array
		(
			'key'			=> $key,
			'value'			=> serialize($value),
			'updated_at'	=> time()
		);
		
		// Remove NULL values
		$update = array_filter($update);
	
		$result = $this->db->update('settings', $update, array('id' => $id));
				
		return $result;
	}
	
	function value($key)
	{
		$item = $this->item(NULL, $key);
		
		if ( $item )
		{
			return unserialize($item -> value);
		}
		
		return FALSE;
	}
	
	function item($id = NULL, $key = NULL) 
	{
		$this->do_select();

		if ( $id )
		{
			$this->db->where('id', $id);
		}
		
		if ( $key )
		{
			$this->db->where('key', $key);
		}
				
		$result = $this->db->get('settings');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function items($key = NULL, $limit = 25, $page = 1) 
	{
		$this->do_select();

		if ( $key )
		{
			$this->db->where('key', $key);
		}
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}
				
		$result = $this->db->order_by('updated_at', 'DESC')->get('settings');
		
		return $result;
	}
	
	function delete($id) {
		$this->db->delete('settings', array('id' => $id));
		
		return true;
	}
}