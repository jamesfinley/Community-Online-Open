<?php

class services_model extends MY_Model {

	function __construct()
	{
		parent::Model();
		$this->load->model('videos_model');
	}
	
	function create($big_idea, $series_title, $schedule_id, $start_at, $end_at, $videos, $content, $twitter_hash, $status = 'published')
	{
		$this->db->insert('services', array(
			'schedule_id'  => $schedule_id,
			'created_at'   => time(),
			'start_at'     => $start_at,
			'end_at'       => $end_at,
			'twitter_hash' => $twitter_hash,
			'big_idea'     => $big_idea,
			'series_title' => $series_title,
			'videos'       => $videos,
			'content'      => $content,
			'status'       => $status
		));
		
		return $this->db->insert_id();
	}
	
	function update($id, $big_idea, $series_title, $schedule_id, $start_at, $end_at, $videos, $twitter_hash, $status = 'published'/*, $content*/)
	{
		if ($this->item($id) !== false)
		{
			$this->db->where('id', $id)->update('services', array(
				'schedule_id'  => $schedule_id,
				'start_at'     => $start_at,
				'end_at'       => $end_at,
				'big_idea'     => $big_idea,
				'twitter_hash' => $twitter_hash,
				'series_title' => $series_title,
				'status'       => $status/*,
				'videos'       => $videos,
				'content'      => $content*/
			));
			return true;
		}
		return false;
	}
	
	function delete($id)
	{
		if ($this->item($id) !== false)
		{
			$this->db->where('id', $id)->delete('services');
			return true;
		}
		return false;
	}
	
	function item($id)
	{
		$result = $this->db->where('services.id', $id)->join('schedule', 'services.schedule_id = schedule.id')->select('services.*, schedule.day_of_week, schedule.time, schedule.time + services.start_at AS service_time')->get('services');
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		return false;
	}
	
	function item_array($id)
	{	
		$result = $this->db->where('services.id', $id)->join('schedule', 'services.schedule_id = schedule.id')->select('services.*, schedule.day_of_week, schedule.time, schedule.time + services.start_at AS service_time')->get('services');
		if ($result->num_rows() === 1)
		{
			return $result->row_array();
		}
		return false;
	}
	
	function items($page = 1, $order_by = 'start_at', $order_dir = 'DESC', $include_old = false)
	{
		$result = $this->db->order_by($order_by, $order_dir)->limit(15, ($page - 1) * 15)->join('schedule', 'services.schedule_id = schedule.id')->select('services.*, schedule.day_of_week, schedule.time, schedule.time + services.start_at AS service_time')->get('services');
		return $result;
	}
	
	function count_items($order_by = 'start_at', $order_dir = 'DESC', $include_old = false)
	{
		$result = $this->db->order_by($order_by, $order_dir)->limit(15, ($page - 1) * 15)->select('id')->get('services');
		return $result->num_rows();
	}
	
	function videos_for_service($id)
	{
		$service = $this->item($id);
		
		if ($service !== false)
		{
			$videos = unserialize($service->videos);
			foreach ($videos as $key=>$video)
			{
				$video = $this->videos_model->item_array($video);
				$videos[$key] = $video;
			}
			return $videos;
		}
		return false;
	}
	
	function next_service()
	{
		// Current time + 15 minutes
		$start_time = time() + 900;
		
		// Just the end time
		$end_time = time();
	
		$result = $this->db->query('SELECT * FROM (SELECT services.*, schedule.time + services.start_at AS service_time, schedule.time + services.start_at + services.end_at AS service_end FROM services, schedule WHERE services.schedule_id = schedule.id) AS services WHERE status = "published" AND service_time <= ? AND service_end > ? LIMIT 1', array($start_time, $end_time));
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		return false;
	}
	
}