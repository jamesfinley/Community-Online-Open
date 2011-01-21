<?php

class schedule_model extends MY_Model {
	
	function create($day_of_week, $time)
	{
		preg_match("/([0-9]?[0-9])([0-9]{2})/", $time, $matches);
		$hours   = $matches[1];
		$minutes = $matches[2];
		$time    = ($hours * 3600) + ($minutes * 60);
		
		$this->db->insert('schedule', array(
			'day_of_week' => $day_of_week,
			'time'        => $time
		));
		
		return $this->db->insert_id();
	}
	
	function update($id, $day_of_week, $time)
	{
		if ($this->item($id) !== false)
		{
			preg_match("/([0-9]?[0-9])([0-9]{2})/", $time, $matches);
			$hours   = $matches[1];
			$minutes = $matches[2];
			$time    = ($hours * 3600) + ($minutes * 60);
			
			$this->db->where('id', $id)->update('schedule', array(
				'day_of_week' => $day_of_week,
				'time'        => $time
			));
			return true;
		}
		return false;
	}
	
	function delete($id)
	{
		if ($this->item($id) !== false)
		{
			$this->db->where('id', $id)->delete('schedule');
			return true;
		}
		return false;
	}
	
	function item($id)
	{
		$this->do_select();
	
		$result = $this->db->where('id', $id)->get('schedule');
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		return false;
	}
	
	function items($page = 1)
	{
		$this->do_select();

		$result = $this->db->order_by('day_of_week')->limit(15, ($page - 1) * 15)->get('schedule');
		return $result;
	}
	
}