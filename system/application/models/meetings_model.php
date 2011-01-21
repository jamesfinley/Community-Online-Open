<?php

class Meetings_Model extends Model {
	
	function item($id)
	{
		$meeting = $this->db->where('id', $id)->get('meetings');
		if ($meeting->num_rows() === 1)
		{
			return $meeting->row();
		}
		return false;
	}
	
	function latest_item_in_group($group_id)
	{
		$meeting = $this->db->limit(1)->order_by('date', 'asc')->where('date', '>= '.(time() - 86400))->get('meetings');
		if ($meeting->num_rows() === 1)
		{
			return $meeting->row();
		}
		return false;
	}
	
	function items($group_id, $limit = 25, $page = 1)
	{
		return $this->db->where('group_id', $group_id)->limit($limit, ($page - 1) * $limit)->get('meetings');
	}
	
	function create($group_id, $user_id, $title, $date, $location, $latitude, $longitude, $description)
	{
		$data = array(
			'group_id'    => $group_id,
			'user_id'     => $user_id,
			'title'       => $title,
			'date'        => $date,
			'location'    => $location,
			'latitude'    => $latitude,
			'longitude'   => $longitude,
			'description' => $description
		);
		$this->db->insert('meetings', $data);
		
		return $this->db->insert_id();
	}
	
	function update($id, $title, $date, $location, $latitude, $longitude, $description)
	{
		if ($this->item($id) !== false) {
			$data = array(
				'title'       => $title,
				'date'        => $date,
				'location'    => $location,
				'latitude'    => $latitude,
				'longitude'   => $longitude,
				'description' => $description
			);
			$this->db->where('id', $id)->update('meetings', $data);
			
			return true;
		}
		return false;
	}
	
	function delete($id)
	{
		if ($this->item($id) !== false) {
			return true;
		}
		return false;
	}
	
}