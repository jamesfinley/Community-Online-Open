<?php

class Stream_Posts_Model extends MY_Model 
{
    function __construct()
    {
        // Call the Model constructor
        parent::Model();
    }
    
    // Prayer Methods
	function post_prayer($user_id, $group_id, $content) 
	{	
		if (!$content) return false;
		return $this->insert($group_id, $user_id, 'prayer', NULL, $content);
	}
	
    // Discussion Methods
	function post_discussion($user_id, $group_id, $subject, $content) 
	{	
		if (!$content || !$subject) return false;
		return $this->insert($group_id, $user_id, 'discussion', $subject, $content);
	}
	
    // Event Methods
	function post_event($user_id, $group_id, $subject, $content, $event_date, $latitude = NULL, $longitude = NULL) 
	{			
		if (!$content || !$subject || !$event_date) return false;
		if ( $this->users->is_facilitator($user_id, $group_id, true) ) 
		{
			return $this->insert($group_id, $user_id, 'event', $subject, $content, $event_date, $latitude, $longitude);
		}
		
		return FALSE;
	}
	
    // Contribution Methods
	function post_contribution($user_id, $group_id, $subject, $content, $latitude = NULL, $longitude = NULL) 
	{
		if (!$content || !$subject) return false;
		if ( $this->users->is_facilitator($user_id, $group_id, true) ) 
		{
			return $this->insert(2, $user_id, 'contribution', $subject, $content, NULL, $latitude, $longitude);
		}
		
		return FALSE;
	}	
	
    // News Methods
	function post_news($user_id, $group_id, $subject, $content) 
	{			
		if (!$content || !$subject) return false;
		if ( $this->users->is_facilitator($user_id, $group_id, true) ) 
		{
			return $this->insert($group_id, $user_id, 'news', $subject, $content);
		}
		
		return FALSE;
	}
    
	// News Methods
	function post_qna($user_id, $group_id, $subject, $content) 
	{			
		if (!$content || !$subject) return false;
		if ( $this->users->is_facilitator($user_id, $group_id, true) ) 
		{
			return $this->insert($group_id, $user_id, 'qna', $subject, $content);
		}
		
		return FALSE;
	}    
    
    // Basic Methods
    
	function insert($group_id, $user_id, $type, $subject, $content, $event_date = NULL, $latitude = NULL, $longitude = NULL)
	{
		$insert = array
		(
			'group_id' 		=> $group_id,
			'user_id'		=> $user_id,
			'type'			=> $type,
			'subject'		=> $subject,
			'content'		=> $content,
			'created_at' 	=> time(),
			'updated_at'	=> time()
		);
		
		$insert['slug'] = $type.($type != 'prayer' ? '-'.url_title($subject, 'underscore', true) : '');
		
		if ( $type == 'event' )
		{
			$insert['event_date'] = $event_date;
		}
		
		if ( ($type == 'event' || $type == 'contribution') && $longitude && $latitude )
		{
			$insert['longitude'] = $longitude;
			$insert['latitude'] = $latitude;
		}
				
		$CI =& get_instance();
		
		// Notify Facilitators that apprentice posted to stream.
		if ( $CI->users->is_apprentice($user_id, $group_id) )
		{
			$CI->users->begin();
			$CI->users->select('users.id');
			
			$users = $CI->users->items_with_role(array('facilitator'), $group_id)->result();
			
			$CI->users->end();
					
			foreach($users as $user)
			{
				//$CI->notification_model->insert($user -> id, 'apprentice_posted', $user_id);
			}		
		}
		
		// Notify User that there post was created
		//$CI->notification_model->insert($user_id, 'item_posted');

			
		$this->db->insert('stream_posts', $insert);
		$id = $this->db->insert_id();
		
		$this->db->where('id', $id)->update('stream_posts', array(
			'slug' => $id.'-'.$type.($type != 'prayer' ? '-'.url_title($subject, 'underscore', true) : '')
		));
		
		return $id;
	}

	function update($id, $group_id = NULL, $user_id = NULL, $type = NULL, $subject = NULL, $content = NULL, $event_date = NULL, $latitude = NULL, $longitude = NULL)
	{
		$update = array
		(
			'group_id' 		=> $group_id,
			'user_id'		=> $user_id,
			'type'			=> $type,
			'subject'		=> $subject,
			'content'		=> $content,
			'updated_at'	=> time()
		);
		
		if ( $type == 'event' )
		{
			$update['event_date'] = $event_date;
		}
		
		if ( ($type == 'event' || $type == 'contribution') && $longitude && $latitude )
		{
			$insert['longitude'] = $longitude;
			$insert['latitude'] = $latitude;
		}
		
		// Remove NULL values
		$update = array_filter($update);
	
		$this->db->update('stream_posts', $update, array('id' => $group_id));
		
		return true;
	}
	
	function stick($group_id, $id)
	{
		$this->db->where('is_sticky', 1)->update('stream_posts', array(
			'is_sticky' => 0
		));
		
		$this->db->where('id', $id)->update('stream_posts', array(
			'is_sticky' => 1
		));
		
		return true;
	}
	
	function unstick($id)
	{
		$this->db->where('id', $id)->update('stream_posts', array(
			'is_sticky' => 0
		));
		
		return true;
	}
	
	function item($stream_post_id) 
	{
		//$this->do_select();
		
		$result = $this->db->where('id', $stream_post_id)->select('*, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = stream_posts.id) AS reply_count')->get('stream_posts');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		$result = $this->db->where('slug', $stream_post_id)->select('*, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = stream_posts.id) AS reply_count')->get('stream_posts');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function items_with_slug($slug, $type, $limit = 25, $page = 1) 
	{
		$id = $this->groups_model->item(NULL, $slug)->id;
		
		return $this->items($id, $type, $limit, $page);
	}
	
	function sticky_item_in_group($group_id)
	{
		$result = $this->db->where('is_sticky', 1)->where('group_id', $group_id)->select('*, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = stream_posts.id) AS reply_count')->get('stream_posts');
		
		if ($result->num_rows() === 1)
		{
			return $result->row();
		}
		
		return false;
	}
	
	function count_items($group_id, $type = NULL, $include_shared = true) 
	{
		if ($include_shared)
		{
			if (is_array($group_id))
			{
				$group_list = implode(',', $group_id);
			}
			$query_part1 = 'SELECT p.id, p.group_id, p.slug, p.bitly, p.user_id, p.type, p.subject, p.content, p.event_date, p.updated_at, "" AS shared_by_id, p.created_at, "" AS originally_posted_at, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = p.id) AS reply_count FROM stream_posts p WHERE group_id'.(is_array($group_id) ? ' IN ('.$group_list.')' : ' = '.$group_id);
			if ($type != 'all' && $type)
			{
				if (is_array($type))
				{
					$string = '';
					foreach ($type as $t)
					{
						$string .= ($string ? ',' : '').'\''.$t.'\'';
					}
					$type = $string;
					$query_part1 .= ' AND type IN ('.$type.')';
				}
				else
				{
					$query_part1 .= ' AND type = "'.$type.'"';
				}
			}
			$query_part1 .= ' ORDER BY created_at DESC';
			
			$query_part2 = 'SELECT p.id, p.group_id, p.slug, p.bitly, p.user_id, p.type, p.subject, p.content, p.event_date, p.updated_at, sp.user_id AS shared_by_id, sp.created_at AS created_at, p.created_at AS originally_posted_at, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = p.id) AS reply_count FROM stream_posts p, shared_stream_posts sp WHERE p.id = sp.stream_post_id AND sp.group_id'.(is_array($group_id) ? ' IN ('.$group_list.')' : ' = '.$group_id);
			if ($type != 'all' && $type)
			{
				if (is_array($type))
				{
					$string = '';
					foreach ($type as $t)
					{
						$string .= ($string ? ',' : '').'\''.$t.'\'';
					}
					$type = $string;
					$query_part1 .= ' AND type IN ('.$type.')';
				}
				else
				{
					$query_part2 .= ' AND p.type = "'.$type.'"';
				}
			}
			$query_part2 .= ' ORDER BY created_at DESC';
			
			$query = '('.$query_part1.') UNION ('.$query_part2.') ORDER BY created_at DESC';
			$results = $this->db->query($query);
			
			return $results->num_rows();
		}
		else
		{
			$this->do_select();
			
			if ( $group_id )
			{
				if (is_array($group_id))
				{
					$this->db->where_in('group_id', $group_id);
				}
				else
				{
					$this->db->where('group_id', $group_id);
				}
			}
			
			if ( $type != 'all' && $type )
			{
				if ( $type == 'none')
				{
					$this->db->where_not_in('type', array('news', 'events', 'discussion', 'prayers', 'qna'));
				}
				else if ( is_array($type) )
				{
					$this->db->where_in('type', $type);
				}
				else
				{
					$this->db->where('type', $type);
				}
			}
			
			$this->db->order_by('created_at', 'DESC');
			
			$result = $this->db->select('stream_posts.*, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = stream_posts.id) AS reply_count')->get('stream_posts');
			
			return $result->num_rows();
		}
	}
	
	function items($group_id, $type = NULL, $page = 1, $limit = 25, $include_shared = true, $include_sticky = false) 
	{
		if ($include_shared)
		{
			if (is_array($group_id))
			{
				$group_list = implode(',', $group_id);
			}
			$query_part1 = 'SELECT p.id, p.group_id, p.slug, p.bitly, p.user_id, p.type, p.subject, p.content, p.event_date, p.image, p.updated_at, "" AS shared_by_id, p.created_at, "" AS originally_posted_at, "" AS shared_group_id, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = p.id) AS reply_count FROM stream_posts p WHERE '.($include_sticky === false ? 'is_sticky = 0 AND ' : '').'group_id'.(is_array($group_id) ? ' IN ('.$group_list.')' : ' = '.$group_id);
			$type_as_array = false;
			if ($type != 'all' && $type)
			{
				if (is_array($type))
				{
					$string = '';
					foreach ($type as $t)
					{
						$string .= ($string ? ',' : '').'\''.$t.'\'';
					}
					$type = $string;
					$type_as_array = true;
					$query_part1 .= ' AND type IN ('.$type.')';
				}
				else
				{
					$query_part1 .= ' AND type = "'.$type.'"';
				}
			}
			$query_part1 .= ' ORDER BY created_at DESC';
			
			$query_part2 = 'SELECT p.id, p.group_id, p.slug, p.bitly, p.user_id, p.type, p.subject, p.content, p.event_date, p.image, p.updated_at, sp.user_id AS shared_by_id, sp.created_at AS created_at, p.created_at AS originally_posted_at, sp.group_id AS shared_group_id, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = p.id) AS reply_count FROM stream_posts p, shared_stream_posts sp WHERE '.($include_sticky === false ? 'is_sticky = 0 AND ' : '').'p.id = sp.stream_post_id AND sp.group_id'.(is_array($group_id) ? ' IN ('.$group_list.')' : ' = '.$group_id);
			if ($type != 'all' && $type)
			{
				if ($type_as_array || is_array($type))
				{
					if (!$type_as_array)
					{
						$string = '';
						foreach ($type as $t)
						{
							$string .= ($string ? ',' : '').'\''.$t.'\'';
						}
						$type = $string;
					}
					$query_part2 .= ' AND type IN ('.$type.')';
				}
				else
				{
					$query_part2 .= ' AND p.type = "'.$type.'"';
				}
			}
			$query_part2 .= ' ORDER BY created_at DESC';
			
			$query = '('.$query_part1.') UNION ('.$query_part2.') ORDER BY created_at DESC';
			
			if ($limit)
			{
				$query .= ' LIMIT '.(($page - 1) * $limit).', '.$limit;
			}
			$results = $this->db->query($query);
			return $results;
		}
		else
		{
			$this->do_select();
			
			if ( $group_id )
			{
				if (is_array($group_id))
				{
					$this->db->where_in('group_id', $group_id);
				}
				else
				{
					$this->db->where('group_id', $group_id);
				}
			}
			
			if ( $type != 'all' && $type )
			{
				if ( $type == 'none')
				{
					$this->db->where_not_in('type', array('news', 'events', 'discussion', 'prayers', 'qna'));
				}
				else if ( is_array($type) )
				{
					$this->db->where_in('type', $type);
				}
				else
				{
					$this->db->where('type', $type);
				}
			}
			
			if ( $limit && $page )
			{
				$this->db->limit($limit, ($page - 1) * $limit);
			} 
			else if ($limit)
			{
				$this->db->limit($limit, ($page - 1) * $limit);
			}
			
			$this->db->order_by('created_at', 'DESC');
			
			if ($include_sticky === false)
			{
				$this->db->where('is_sticky', 0);
			}
			
			$result = $this->db->select('stream_posts.*, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = stream_posts.id) AS reply_count')->get('stream_posts');
			
			return $result;
		}
	}
	
	function personal_stream($user_id, $types = null, $page = 1, $limit = 25, $include_shared = true)
	{
		$users_groups = $this->db->where('user_id', $user_id)->get('groups_users');
		$group_id = array();
		foreach ($users_groups->result() as $users_group)
		{
			array_push($group_id, $users_group->group_id);
		}
		if (count($group_id) === 0) {
			array_push($group_id, 1);
		}
		
		if ($include_shared)
		{
			if (is_array($group_id))
			{
				$group_list = implode(',', $group_id);
			}
			if (is_array($types))
			{
				for ($i=0; $i<count($types); $i++)
				{
					$types[$i] = "'".$types[$i]."'";
				}
				$types = implode(',', $types);
			}
			$query_part1 = 'SELECT p.id, p.group_id, p.slug, p.bitly, p.user_id, p.type, p.subject, p.content, p.event_date, p.image, p.updated_at, "" AS shared_by_id, p.created_at, "" AS originally_posted_at, "" as shared_group_id, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = p.id) AS reply_count FROM stream_posts p WHERE group_id'.(is_array($group_id) ? ' IN ('.$group_list.')' : ' = '.$group_id).($types ? ' AND type IN ('.$types.')' : '');
			/*if ($type != 'all' && $types)
			{
				if (is_array($type))
				{
					
				}
				else
				{
					$query_part1 .= ' AND type = "'.$type.'"';
				}
			}*/
			
			$query_part2 = 'SELECT p.id, p.group_id, p.slug, p.bitly, p.user_id, p.type, p.subject, p.content, p.event_date, p.image, p.updated_at, sp.user_id AS shared_by_id, sp.created_at AS created_at, p.created_at AS originally_posted_at, sp.group_id AS shared_group_id, (SELECT COUNT(id) AS reply_count FROM stream_replies WHERE stream_post_id = p.id) AS reply_count FROM stream_posts p, shared_stream_posts sp WHERE p.id = sp.stream_post_id AND sp.group_id'.(is_array($group_id) ? ' IN ('.$group_list.')' : ' = '.$group_id).($types ? ' AND type IN ('.$types.')' : '');
			/*if ($type != 'all' && $type)
			{
				if (is_array($type))
				{
					
				}
				else
				{
					$query_part2 .= ' AND p.type = "'.$type.'"';
				}
			}*/
			
			$query = '('.$query_part1.') UNION ('.$query_part2.') ORDER BY created_at DESC';
			
			if ($limit)
			{
				$query .= ' LIMIT '.(($page - 1) * $limit).', '.$limit;
			}
			$results = $this->db->query($query);
			return $results;
		}
	}
	
	function delete($stream_post_id, $remove_replies = true) 
	{
		$item = $this->item($stream_post_id);
		$this->db->delete('stream_posts', array('id' => $item->id));
		
		if ($remove_replies)
		{
			$this->db->delete('stream_replies', array('stream_post_id' => $item->id));
		}
		
		return true;
	}
	
	function attending_event($stream_post_id, $user_id)
	{
		$item = $this->item($stream_post_id);
		$check = $this->db->where('stream_post_id', $item->id)->where('user_id', $user_id)->get('stream_events_attending');
		if ($check->num_rows())
		{
			return true;
		}
		return false;
	}
	
	function attend_event($stream_post_id, $user_id)
	{
		$item = $this->item($stream_post_id);
		
		$check = $this->db->where('stream_post_id', $item->id)->where('user_id', $user_id)->get('stream_events_attending');
		if ($check->num_rows() === 0)
		{
			$this->db->insert('stream_events_attending', array(
				'stream_post_id' => $item->id,
				'user_id' => $user_id,
				'created_at' => time()
			));
		}
		return true;
	}
	
	function not_attend_event($stream_post_id, $user_id)
	{
		$item = $this->item($stream_post_id);
		$check = $this->db->where('stream_post_id', $item->id)->where('user_id', $user_id)->delete('stream_events_attending');
		return true;
	}
	
	function whos_attending($stream_post_id)
	{
		$item = $this->item($stream_post_id);
		$items = $this->db->where('stream_post_id', $item->id)->get('stream_events_attending');
		return $items;
	}
}