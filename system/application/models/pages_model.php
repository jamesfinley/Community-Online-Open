<?php

class Pages_Model extends MY_Model 
{    
	function insert($group_id, $title, $slug, $content, $show_in_sidebar = 1)
	{
		$insert = array
		(
			'group_id' 			=> $group_id,
			'title' 			=> $title,
			'slug'	 			=> $slug,
			'content'			=> $content,
			'show_in_sidebar'	=> $show_in_sidebar,
			'created_at' 		=> time(),
			'updated_at'		=> time()
		);
	
		$this->db->insert('pages', $insert);
		
		return $this->db->insert_id();
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

	function update($id, $group_id = NULL, $slug = NULL, $title = NULL, $content = NULL, $show_in_sidebar = NULL)
	{
		$update = array
		(
			'title'				=> $title,
			'slug'				=> $slug,
			'content'			=> $content,
			'show_in_sidebar'	=> $show_in_sidebar ? 1 : 0,
			'updated_at'		=> time()
		);
		
		// Remove NULL values
		$update = $this->_filter($update);
	
		$result = $this->db->update('pages', $update, array('id' => $id));
				
		return $result;
	}
	
	function item($id = NULL, $group_id = NULL) 
	{
		$this->do_select();

		if ( $id )
		{
			$this->db->where('id', $id);
		}
		
		if ( $group_id )
		{
			$this->db->where('group_id', $group_id);
		}
				
		$result = $this->db->get('pages');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
		
	function item_by_slug($slug, $group_id)
	{
		$this->do_select();

		$this->db->where('group_id', $group_id)->where('slug', $slug);
				
		$result = $this->db->get('pages');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function items($group_id = NULL, $limit = 25, $page = 1) 
	{
		$this->do_select();

		if ( $group_id !== null )
		{
			$this->db->where('group_id', $group_id);
		}

		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}
				
		$result = $this->db->order_by('title')->get('pages');
		
		return $result;
	}
	
	function items_in_sidebar($group_id)
	{
		return $this->db->where('group_id', $group_id)->where('show_in_sidebar', 1)->order_by('title')->get('pages');
	}	
	
	function delete($id) {
		$this->db->delete('pages', array('id' => $id));
		
		return true;
	}
}