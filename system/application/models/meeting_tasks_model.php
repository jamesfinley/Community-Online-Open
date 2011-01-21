<?php

class Meeting_Tasks_Model extends Model {
	
	function item($id)
	{
		$task = $this->db->where('id', $id)->get('meeting_tasks');
		if ($task->num_rows() === 1)
		{
			return $task->row();
		}
		return false;
	}
	
	function items($meeting_id)
	{
		return $this->db->where('meeting_id', $meeting_id)->join('users', 'meeting_tasks.user_id = users.id')->get('meeting_tasks');
	}
	
	function create($user_id, $meeting_id, $short_title, $description)
	{
		$data = array(
			'user_id'     => $user_id,
			'meeting_id'  => $meeting_id,
			'short_title' => $short_title,
			'description' => $description,
			'status'      => 'pending'
		);
		$this->db->insert('meeting_tasks', $data);
		
		return $this->db->insert_id();
	}
	
	function update($id, $short_title, $description)
	{
		if ($this->item($id) !== false)
		{
			$data = array(
				'short_title' => $short_title,
				'description' => $description
			);
			$this->db->where('id', $id)->update('meeting_tasks', $data);
			
			return true;
		}
		return false;
	}
	
	function accept($id)
	{
		if ($this->item($id) !== false)
		{
			$data = array(
				'status' => 'accepted'
			);
			$this->db->where('id', $id)->update('meeting_tasks', $data);
			
			return true;
		}
		return false;
	}
	
	function decline($id)
	{
		if ($this->item($id) !== false)
		{
			$data = array(
				'status' => 'declined'
			);
			$this->db->where('id', $id)->update('meeting_tasks', $data);
			
			return true;
		}
		return false;
	}
	
	function delete($id)
	{
		if ($this->item($id) !== false)
		{
			return true;
		}
		return false;
	}
	
}