<?php

class Stream_Replies_Model extends MY_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::Model();
        
        $this->cache_replies();
    }
    
    public function cache_replies($group_id = null)
    {
        $replies_results = $this->db->get('stream_replies');
        $this->replies = array();
        foreach ($replies_results->result() as $reply)
        {
        	$this->replies[$reply->created_at] = $reply;
        }
    }
    
    // Reply Methods
	function reply($stream_id, $user_id, $content) 
	{	
		return $this->insert($stream_id, $user_id, $content);
	}
    
    // Basic Methods
    
	function insert($stream_post_id, $user_id, $content)
	{
		$insert = array
		(
			'stream_post_id'=> $stream_post_id,
			'user_id'		=> $user_id,
			'content'		=> $content,
			'created_at' 	=> time(),
			'updated_at'	=> time()
		);
	
		$this->db->insert('stream_replies', $insert);
		$id = $this->db->insert_id();
		
		$this->cache_replies();
		
		//load users that are involved in this thread
		//$user_id = $this->db->select('user_id')->where('id', $stream_post_id)->get('stream_posts')->row()->user_id;
		$user_ids = $this->db->query('SELECT DISTINCT(user_id) FROM stream_replies WHERE stream_post_id = ? AND user_id != ?', array($stream_post_id, $user_id));
		$poster = $this->db->where('id', $user_id)->get('users')->row();
		$post   = $this->db->where('id', $stream_post_id)->get('stream_posts')->row();
		$group  = $this->db->where('id', $post->group_id)->get('groups')->row();
		$message = serialize(array(
			'posted_by' => $poster->first_name.' '.$poster->last_name,
			'subject' => $poster->first_name.' '.$poster->last_name.' followed up on '.($post->type != 'prayer' ? $post->subject : 'Prayer'),
			'created_at' => time(),
			'link' => site_url($this->groups_model->get_url($post->group_id).'/p/'.$post->id)
		));
		$short_message = $poster->first_name.' '.$poster->last_name.' followed up on <a href="'.site_url($this->groups_model->get_url($post->group_id).'/p/'.$post->id).'">'.($post->type != 'prayer' ? $post->subject : 'Prayer').'</a> on <a href="'.site_url($this->groups_model->get_url($post->group_id)).'">'.$group->name.'</a>.';
		foreach ($user_ids->result() as $member)
		{
			if ($member->user_id != $post->user_id)
			{
				$this->notifications_model->create($member->user_id, $group->id, $post->type, ($poster->first_name.' '.$poster->last_name.' followed up on '.($post->type != 'prayer' ? $post->subject : 'Prayer')), $message, $short_message);
			}
		}
		
		return $id;
	}

	function rate($stream_reply_id, $user_id, $rating)
	{
		$this->db->where('stream_reply_id', $stream_reply_id);
		$this->db->where('user_id', $user_id);
		$this->db->from('stream_reply_ratings');
		
		if ( $this->db->count_all_results() == 0 )
		{
			$data = array('rating' => $rating, 'stream_reply_id' => $stream_reply_id, 'user_id' => $user_id);
		
			$this->db->insert('stream_reply_ratings', $data);
		}
	}
	
	function rating($stream_reply_id)
	{
		return $this->db->select_avg('rating', 'rating')->where('stream_reply_id', $stream_reply_id)->get('stream_reply_ratings')->row()->rating;
	}

	function update($id, $stream_post_id = NULL, $user_id = NULL, $content = NULL)
	{
		$update = array
		(
			'stream_post_id'=> $stream_post_id,
			'user_id'		=> $user_id,
			'content'		=> $content,
			'rating'		=> $rating,
			'updated_at'	=> time()
		);
		
		// Remove NULL values
		$update = array_filter($update);
	
		$this->db->update('stream_replies', $update, array('id' => $group_id));
		
		$this->cache_replies();
		
		return true;
	}
	
	function item($id) 
	{
		$this->do_select();
		
		$result = $this->db->where('id', $id)->get('stream_replies');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function items($stream_post_id, $limit = 25, $page = 1) 
	{
		/*$this->do_select();

		if ( $stream_post_id )
		{
			$this->db->where('stream_post_id', $stream_post_id);
		}
		
		if ( $limit && $page )
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		} 
		else if ($limit)
		{
			$this->db->limit($limit, ($page - 1) * $limit);
		}

		$result = $this->db->get('stream_replies');*/
		
		$result = array();
		
		//$i = 0;
		foreach ($this->replies as $reply)
		{
			if ($reply->stream_post_id == $stream_post_id)
			{
				array_push($result, $reply);
				//$i++;
			}
		}
		
		if ($limit !== 0)
		{
			$num    = count($result);
			$offset = $num - $limit;
			if ($offset < 0)
			{
				$offset = 0;
			}
			
			$result = array_slice($result, $offset, $limit);
		}
		
		return $result;
	}
	
	function delete($id) {
		$this->db->delete('stream_replies', array('id' => $id));
		
		$this->cache_replies();
		
		return true;
	}
}