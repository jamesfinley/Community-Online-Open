<?php

class Notes_Model extends MY_Model 
{
    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
    function save($service_id, $user_id, $content)
    {	
    	$item = $this->item(NULL, $service_id, $user_id);

    	if ( $item )
    	{
    		$this->update($item->id, $content);
    		return $item -> id;
    	}
    	
    	return $this->insert($service_id, $user_id, $content);
    }
    
	function insert($service_id, $user_id, $content)
	{
		$insert = array
		(
			'service_id' 	=> $service_id,
			'user_id' 		=> $user_id,
			'content'		=> $content,
			'created_at' 	=> time(),
			'updated_at'	=> time()
		);
	
		$this->db->insert('notes', $insert);
		
		return $this->db->insert_id();
	}

	function update($id, $content = NULL)
	{
		$update = array
		(
			'content'	=> $content,
			'updated_at'	=> time()
		);
		
		// Remove NULL values
		$update = array_filter($update);
	
		$result = $this->db->update('notes', $update, array('id' => $id));
				
		return $result;
	}
	
	function item($id = NULL, $service_id = NULL, $user_id = NULL) 
	{
		$this->do_select('notes.*, big_idea, series_title');

		if ( $id )
		{
			$this->db->where('notes.id', $id);
		}
		
		if ( $service_id )
		{
			$this->db->where('service_id', $service_id);
		}
		
		if ( $user_id )
		{
			$this->db->where('user_id', $user_id);
		}
		
		$this->db->join('services', 'services.id = notes.service_id');
		
		$result = $this->db->get('notes');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function items($service_id = NULL, $user_id = NULL, $limit = 25, $page = 1) 
	{
		$this->do_select("notes.*, big_idea, series_title");

		if ( $service_id )
		{
			$this->db->where('service_id', $service_id);
		}
		if ( $user_id )
		{
			$this->db->where('user_id', $user_id);
		}
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}
		
		$this->db->join('services', 'notes.service_id = services.id');
		
		$result = $this->db->order_by('updated_at', 'DESC')->get('notes');
		
		return $result;
	}
	
	function delete($id) {
		$this->db->delete('notes', array('id' => $id));
		
		return true;
	}
}