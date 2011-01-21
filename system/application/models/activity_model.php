<?php

class Activity_Model extends Model {
	var $types = array
	(
		'new_user'
	);
	
    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
	function insert($user_id, $type, $created_at = NULL)
	{
		if (in_array($type, $this->types)) {
			$insert = array
			(
				'user_id' 		=> $user_id,
				'type' 			=> $type,
				'created_at' 	=> $created_at || time()
			);
		
			$this->db->insert('activity', $insert);
		}
	}
	
	
}