<?php

class videos_model extends MY_Model {
	
	function create($location, $title, $length, $type, $description)
	{
		$this->db->insert('videos', array(
			'location'    => $location,
			'title'       => $title,
			'length'      => $length,
			'type'        => $type,
			'description' => $description,
			'content'     => $content
		));
		
		return $this->db->insert_id();
	}
	
	function update($id, $location, $title, $length, $type, $description)
	{
		if ($this->item($id) !== false)
		{
			$this->db->where('id', $id)->update('videos', array(
				'location'    => $location,
				'title'       => $title,
				'length'      => $length,
				'type'        => $type,
				'description' => $description,
				'content'     => $content
			));
			return true;
		}
		return false;
	}
	
	function delete($id)
	{
		if ($this->item($id) !== false)
		{
			$this->db->where('id', $id)->delete('videos');
			return true;
		}
		return false;
	}
	
	function item($id)
	{
		$this->do_select();

		$result = $this->db->where('id', $id)->get('videos');
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		return false;
	}
	
	function item_array($id)
	{
		$this->do_select();

		$result = $this->db->where('id', $id)->get('videos');
		if ($result->num_rows() === 1)
		{
			return $result->row_array();
		}
		return false;
	}
	
	function items()
	{
		$this->do_select();

		$videos = $this->db->order_by('id')->get('videos');
		return $videos;
	}
	
}