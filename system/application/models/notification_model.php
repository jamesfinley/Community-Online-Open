<?php

class Notification_Model extends Model {
	
	var $storage_length = 84600;

    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
	function insert($user_ids, $type, $userinfo = NULL, $digest_at = NULL)
	{
		$digest_at = $digest_at ? $digest_at : time();

		$user_ids = is_array($user_ids) ? $user_ids : array($user_ids);

		foreach($user_ids as $user_id)
		{
			$insert = array
			(
				'user_id' 		=> $user_id,
				'type' 			=> $type,
				'created_at' 	=> time(),
				'userinfo'		=> serialize($userinfo),
				'digest_at'		=> $digest_at
			);
			
			$digest_at = $digest_at ? $digest_at : time();
		
			$this->db->insert('notifications', $insert);
		}
		
		return TRUE;
	}
	
	function message($notification, $language = 'english')
	{	
		$this->load->library('language');
		$this->language->load('notifications', $language);
		$this->language->line('notification_new_user');

		$data = array();
		
		$line = '';
		$notification_data = $this->userinfo($notification);

		switch ($notification->type)
		{
			case 'item_posted':
				$line = $this->language->line('notification_'.$notification->type);
				$data['name'] = $notification_data['name'];
			break;
			
			default:
				$line = $this->language->line('notification_undefined');
				$data['type'] = $notification->type;
			break;
		}

		foreach($data as $key => $value)
		{
			$line = str_replace('{'.$key.'}', $value, $line);
		}

		return $line;
	}

	function userinfo($notification) 
	{
		return unserialize($notification->userinfo);	
	}

    function get_unsent( $digest_at = FALSE, $user_id = NULL, $type = NULL )
    {
     	if ( $user_id )
    	{
    		$this->db->where('user_id', $user_id);
    	}

    	if ( $type ) 
    	{ 
    		$this->db->where('type', $type);
    	}
    	
    	if ( $digest_at )
    	{
    		$this->db->where('digest_at <', $digest_at);
    	}
    	
      	$this->db->where('is_emailed', false);
    
    	$this->db->select('n.id, user_id, type, u.email, CONCAT(u.first_name," ", u.last_name) AS full_name, userinfo', FALSE);
    	
    	$this->db->join('users AS u', 'user_id = u.id');
    	
    	$this->db->order_by('user_id, created_at');
    	
		$query = $this->db->get('notifications AS n');
		return $query->result();   
    }
    
    function get_unread($user_id = NULL, $type = NULL)
    {
    	if ( $user_id )
    	{
    		$this->db->where('user_id', $user_id);
    	}
    	        	
    	if ($type) 
    	{
    		$this->db->where('type', $type);
    	}
    	
      	$this->db->where('is_read', false);
    
    	$this->db->select('n.id, user_id, type, userinfo', FALSE);
    	
		$query = $this->db->get('notifications AS n');
		return $query->result();
    }
        
    function email_setting($user_id, $type, $prevent = true) 
    {
    	$where = array
    	(
    		'user_id' => $user_id,
    		'type' => $type
    	);
    	
    	$result = $this->db->get_where('notification_settings', $where)->row();
    	
    	if ($result) {
    		$data = array('prevent_email' => $prevent);
    	
    		$this->db->update('notification_settings', $data, $where);
    	} else {
    		$where['prevent_email'] = $prevent;
    	
    		$this->db->insert('notification_settings', $where);
    	}
    }
    
    function mark_sent($notification_ids)
    {    	
    	if (is_array($notification_ids))
    	{
    		$this->db->where_in('id', $notification_ids);
    	} else 
    	{
    		$where['id'] = $notification_id;
    	}
    	
    	$data 	= array
    	(
    		'is_emailed' => true
    	);
   	
    	$this->db->update('notifications', $data, $where);   
    }
    
    function mark_read($user_id, $notification_ids)
    {
    	$where 	= array('user_id' => $user_id);
    	
    	if (is_array($notification_ids))
    	{
    		$this->db->where_in('id', $notification_ids);
    	} else 
    	{
    		$where['id'] = $notification_id;
    	}
    	
    	$data 	= array
    	(
    		'is_read' => true,
    		'delete_at' => time() + $this->storage_length
    	);
   	
    	$this->db->update('notifications', $data, $where);
    }
    
    function auto_delete() 
    {
    	$sql = "DELETE FROM notifications WHERE delete_at < ?";
    
    	$this->db->query($sql, time());

    	$this->db->delete('notifications', array('delete_at <' => time()));
    }
}