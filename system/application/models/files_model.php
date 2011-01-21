<?php

class Files_Model extends MY_Model 
{
    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
    function can_upload($name)
    {    
    	if (empty($_FILES[$name]['name']))
    	{
    		return FALSE;
    	}
    	
    	if (is_array($_FILES[$name]['name']))
    	{
    		$length = count($_FILES[$name]['name']);
    		
    		$can = FALSE;
    		
			foreach($_FILES[$name]['name'] as $name)
			{
				if ($name)
				{
					$can = TRUE;
				}
			} 

			if ( $can == FALSE)
			{
    			return FALSE;
    		}
       	}
    	
    	return TRUE;
    }
    
    function upload($name, $foreign_id, $foreign_type, $directory = NULL, &$error = NULL, $allowed_types = 'gif|jpg|png|pdf|txt|doc|ppt|ics|xml')
    {
		$this->load->helper('array');
	
		$file = $_FILES[$name];
	    
    	if ( isset($_FILES[$name]) )
    	{   			
			$config['upload_path'] = $directory ? $directory : $this->directory();
			$config['allowed_types'] = $allowed_types;
			
			$this->load->helper('string');
				
			//print_r($_FILES[$name]);
			if ( is_array($_FILES[$name]['name']) )
			{
				$files = rotate($_FILES[$name]);
			   	
    			foreach($files as $file)
    			{
	  				$this->load->library('upload', $config);
					
					if ( ! $this->upload->do_upload($file))
					{
						$error .= $this->upload->display_errors();
					}	
					else
					{
						$data = $this->upload->data();
						$this->insert($data['file_name'], $foreign_id, $foreign_type);
					}  				
    			}
    		}
    		else
    		{
    			$ext  = $_FILES[$name]['name'];
    			$ext  = explode('.', $ext);
    			$ext  = $ext[count($ext) - 1];
    			$fname = $foreign_id;
    			$config['file_name'] = $fname;
    			
				$this->load->library('upload', $config);
					
				if ( ! $this->upload->do_upload($name))
				{
					$error = $this->upload->display_errors();
				}	
				else
				{
					$data = $this->upload->data();
					
					if ($foreign_type == 'post_image')
					{
						$this->db->where('id', $foreign_id)->update('stream_posts', array(
							'image' => $data['file_name']
						));
					}
					else
					{
						$this->insert($data['file_name'], $foreign_id, $foreign_type);
					}
				}
    		}
    	}
    	
    	return FALSE;
    }
    
	function insert($path, $foreign_id, $foreign_type)
	{
		$insert = array
		(
			'path' 			=> $path,
			'foreign_id' 	=> $foreign_id,
			'foreign_type'	=> $foreign_type,
			'created_at' 	=> time(),
			'updated_at'	=> time()
		);
	
		$this->db->insert('files', $insert);
		
		return $this->db->insert_id();
	}

	function update($id, $path = NULL, $foreign_id = NULL, $foreign_type = NULL)
	{
		$update = array
		(
			'path'			=> $path,
			'foreign_id' 	=> $foreign_id,
			'foreign_type' 	=> $foreign_type,
			'updated_at'	=> time()
		);
		
		// Remove NULL values
		$update = array_filter($update);
	
		$result = $this->db->update('files', $update, array('id' => $id));
				
		return $result;
	}
	
	function pathWithType($foreignType, $foreignID)
	{
		return $this->path($this->item(NULL, $foreignID, $foreignType)->path);
	}
	
	function pathWithID($id)
	{
		return $this->path($this->item($id)->path);
	}
	
	function path($string)
	{		
		$path = $this->directory() . '/' . $string;
		
		if ( file_exists($path) )
		{
			return $path;
		}
		
		return FALSE;
	}
	
	function directory()
	{
		$path = APPPATH.'/files';
	
		// Add Groups Superfolder
		if ( ! is_dir($path) )
		{
			mkdir($path);
		}
		
		return $path;		
	}
	
	function item($id = NULL, $foreign_id = NULL, $foreign_type = NULL) 
	{
		$this->do_select();

		if ( $id )
		{
			$this->db->where('id', $id);
		}
		
		if ( $foreign_id )
		{
			$this->db->where('foreign_id', $foreign_id);
		}
		
		if ( $foreign_type )
		{
			$this->db->where('foreign_type', $foreign_type);
		}
				
		$result = $this->db->get('files');
				
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function items($foreign_id = NULL, $foreign_type = NULL, $limit = 25, $page = 1) 
	{
		$this->do_select();

		if ( $foreign_id )
		{
			$this->db->where('foreign_id', $foreign_id);
		}
		
		if ( $foreign_type )
		{
			$this->db->where('foreign_type', $foreign_type);
		}
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}
				
		$result = $this->db->order_by('created_at', 'DESC')->get('files');
		
		return $result;
	}
	
	function delete($id) 
	{
		// Remove File
		$path = $this->pathWithID($id);
		unlink($path);
		
		// Remove File Entry from Database
		$this->db->delete('files', array('id' => $id));
		
		return true;
	}
}