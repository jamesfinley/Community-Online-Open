<?php

class Big_Idea_Model extends MY_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
	function insert($series_title, $category, $short, $long, $d_x, $d_y, $v_x, $v_y, $color, $text_color, $border_color, $image_path, $begin_at)
	{
		$insert = array
		(
			'series_title' 			=> $series_title,
			'category'				=> $category,
			'short_description' 	=> $short,
			'long_description' 		=> $long, 
			'description_x'			=> $d_x,
			'description_y' 		=> $d_y,
			'videos_x'				=> $v_x,
			'videos_y'		 		=> $v_y,
			'background_color' 		=> $color, 
			'border_color' 			=> $border_color,
			'text_color'			=> $text_color,
			'background_image_path' => $image_path,
			'begin_at'				=> $begin_at,
			'created_at' 			=> time(),
			'updated_at'			=> time()
		);
	
		$this->db->insert('big_idea', $insert);
		
		return $this->db->insert_id();
	}

	function update($id, $series_title = NULL, $category = NULL, $short = NULL, $long = NULL, $d_x = NULL, $d_y = NULL, $v_x = NULL, $v_y = NULL, $color = NULL, $border_color = NULL, $text_color = NULL, $image_path = NULL, $begin_at = NULL)
	{
		$update = array
		(
			'series_title' 			=> $series_title,
			'category'				=> $category,
			'short_description' 	=> $short,
			'long_description' 		=> $long, 
			'description_x'			=> $d_x,
			'description_y' 		=> $d_y,
			'videos_x'				=> $v_x,
			'videos_y' 				=> $v_y,
			'background_color' 		=> $color,
			'border_color'			=> $border_color,
			'text_color'			=> $text_color,
			'background_image_path' => $image_path,
			'begin_at'				=> $begin_at,
			'updated_at'			=> time()
		);
		
		// Remove NULL values
		$update = array_filter($update);
	
		$this->db->update('big_idea', $update, array('id' => $id));
		
		return true;
	}
	
	function adult_idea()
	{
		return $this->item(null, 'adults');
		//return $this->item(NULL, 'adults', NULL, strtotime('+1 Sunday'));
	}
	
	function kid_idea()
	{
		return $this->item(null, 'kids');
		//return $this->item(NULL, 'kids', NULL, strtotime('+1 Sunday'));
	}
	
	function student_idea()
	{
		return $this->item(null, 'students');
		//return $this->item(NULL, 'students', NULL, strtotime('+1 Sunday'));
	}
	
	function item($id = NULL, $category = NULL, $begin_at = NULL, $end_at = NULL) 
	{
		$this->do_select('big_idea.*, f.path AS image');

		if ( $id )
		{
			$this->db->where('big_idea.id', $id);
		}
		
		if ( $category )
		{
			$this->db->where('category', $category);
		}
		
		if ( $begin_at )
		{
			$this->db->where('begin_at >=', $begin_at);
		}
		
		if ( $end_at )
		{
			$this->db->where('end_at <=', $end_at);
		}
		
		$this->db->order_by('created_at DESC');
		
		$this->db->limit(1);
		
		$this->db->join('files AS f', "f.foreign_id = big_idea.id AND f.foreign_type = 'big_idea_banner'", 'left outer');
		
		$result = $this->db->get('big_idea');
								
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}

	function image($id)
	{
		$CI =& get_instance();
		
		$item = $CI->files_model->item($id);
		
		return base_url('resources/series/'.$item->path);
	}
	
	function items($begin_at = NULL, $end_at = NULL, $limit = 25, $page = 1) 
	{
		$this->do_select();
		
		if ( $begin_at )
		{
			$this->db->where('begin_at >=', $begin_at);
		}
		
		if ( $end_at )
		{
			$this->db->where('end_at <=', $end_at);
		}		
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}
		
		$this->db->order_by('created_at DESC');
		
		$result = $this->db->get('big_idea');
				
		return $result;
	}
	
	function delete($id) {
		$this->db->delete('big_idea', array('id' => $id));
		
		return true;
	}
	
	function videos_for_tab($id)
	{
		$DB = $this->load->database('joomla', true);
		if ($DB) {
			$videos = $DB->query('SELECT * FROM jos_3cvideo_file WHERE `tabid`='.$id.' AND featured=1 AND published=1 AND ( publish_up = \'0000-00-00 00:00:00\' OR publish_up <= NOW() ) AND ( publish_down = \'0000-00-00 00:00:00\' OR publish_down >= NOW() ) AND access <= 0 ORDER BY `modified` DESC LIMIT 3');
		}
		else
		{
			$videos = false;
		}
		$DB = $this->load->database('default', true);
		return $videos;
	}
}