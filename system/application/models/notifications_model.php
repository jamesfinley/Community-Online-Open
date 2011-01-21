<?php

class Notifications_Model extends Model {
	
	function create($user_ids, $group_id, $type, $subject, $message, $short_message)
	{
		if (is_array($user_ids) === false)
		{
			$user_ids = array($user_ids);
		}
		$digest_at = time() < strtotime('today') + 57600 ? strtotime('today') + 57600 : strtotime('tomorrow') + 57600;
		foreach ($user_ids as $user_id)
		{
			$this->db->insert('notifications', array(
				'user_id'       => $user_id,
				'group_id'      => $group_id,
				'type'          => $type,
				'subject'       => $subject,
				'message'       => $message,
				'short_message' => $short_message,
				'email_sent_at' => 0,
				'delete_at'     => 0,
				'digest_at'     => $digest_at,
				'is_unread'     => 1,
				'created_at'    => time(),
				'updated_at'    => time()
			));
		}
	}
	
	function outbox($limit = 15, $user_id = null)
	{
		if ($user_id !== null)
		{
			$this->db->where('user_id', $user_id);
		}
		if ($limit)
		{
			$this->db->limit($limit);
		}
		
		$items = $this->db->where('is_unread', 1)->where('email_sent_at', 0)->where('created_at <= '.time())->order_by('created_at', 'ASC')->get('notifications');
		return $items;
	}
	
	function sent($notification_id)
	{
		$this->db->where('id', $notification_id)->update('notifications', array(
			'email_sent_at' => time(),
			'updated_at'    => time()
		));
	}
	
	function digest_outbox($user_id, $group_id = null)
	{
		if ($group_id !== null)
		{
			$this->db->where('group_id', $group_id);
		}
		return $this->db->where('digest_at <= '.time())->where('is_unread', 1)->where('email_sent_at', 0)->where('user_id', $user_id)->where('created_at <= '.time())->order_by('created_at', 'ASC')->get('notifications');
	}
	
	function unread($user_id, $notification_id = null)
	{
		if ($notification_id === null)
		{
			$items = $this->db->where('user_id', $user_id)->where('is_unread', 1)->where('created_at <= '.time())->order_by('created_at', 'DESC')->get('notifications');
			return $items;
		}
		else
		{
			$this->db->where('user_id', $user_id)->where('id', $notification_id)->update('notifications', array(
				'is_unread'  => 0,
				'delete_at'  => time() + 86400,
				'updated_at' => time()
			));
		}
	}
	
	function setting($user_id, $group_id, $type, $receive_emails = null)
	{
		if ($receive_emails !== null)
		{
			$this->db->where('user_id', $user_id)->where('group_id', $group_id)->where('notification_type', $type)->delete('notification_settings');
			$this->db->insert('notification_settings', array(
				'user_id'           => $user_id,
				'group_id'          => $group_id,
				'notification_type' => $type,
				'receive_emails'    => $receive_emails ? 1 : 0
			));
		}
		else
		{
			if ($group_id !== null)
			{
				$this->db->where('group_id', $group_id);
			}
			$check = $this->db->where('user_id', $user_id)->where('notification_type', $type)->get('notification_settings');
			if ($check->num_rows())
			{
				return $check->row()->receive_emails ? true : false;
			}
			else
			{
				return $type === 'digest' ? false : true;
			}
		}
	}
	
	function receive_digest($user_id, $group_id)
	{
		$check = $this->setting($user_id, $group_id, 'digest');
		return $check;
	}
	
	function auto_delete()
	{
		$this->db->where('delete_at < '.time().' AND delete_at != 0')->delete('notifications');
	}
	
}