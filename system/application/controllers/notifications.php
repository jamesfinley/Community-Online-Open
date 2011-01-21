<?php

/*class Notifications extends MY_Controller 
{	
	public $email_address = 'example@example.com';
	public $email_name = 'CCC';

	function index()
	{		
		$notifications = $this->notification_model->get_unsent(time());
	
		$email_notifications = array();
		$user_id = 0;
		$email_address = '';
	
		// Prepare Emails
		foreach ($notifications as $notification)
		{
			if ( $notification->email != 't.krush@gmail.com' )
			{
				continue;
			}
		
			if ( $notification->user_id != $user_id )
			{
				$this->email_notifications($email_address, $email_notifications);
				$email_notifications = FALSE;
			
				$user_id = $notification->user_id;
				$email_address = $notification->email;
			}
			
			$email_notifications[] = $notification;
		}
				
		$this->email_notifications($email_address, $email_notifications);
		
		$this->notification_model->auto_delete();
	}
	
	function email_notifications($email_address, $notifications)
	{
		if ( count($notifications) > 0 )
		{		
			$message = $this->load->view('notifications/email', array('notifications'=>$notifications), TRUE);
			
			$this->load->library('email');

			$config['mailtype'] = 'html';

			$this->email->initialize($config);

			$this->email->from($this->email_address, $this->email_name);
			$this->email->to($email_address); 
			
			$this->email->subject('Community Online: Notifications');
			$this->email->message($message);	
			
			$this->email->send();
			
			$ids = array();
			
			foreach($notifications as $notification)
			{
				$ids[] = $notification->id;
			}
			
			$this->notification_model->mark_sent($ids);
		}
	}
}*/

class Notifications extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('notifications_model');
		
		$this->notifications_model->auto_delete();
	}
	
	function send_emails($count = 100)
	{
		//load notifications
		$notifications = $this->notifications_model->outbox($count);
		
		foreach ($notifications->result() as $notification)
		{
			$type     = $notification->type;
			$user_id  = $notification->user_id;
			$group_id = $notification->group_id;
			$group    = $this->groups_model->item($group_id);
			$user     = $this->users->item($user_id);
			if ($user)
			{
				//see if user wants digest from this group
				$receive_digest = $this->notifications_model->receive_digest($user_id, $group_id);
				if ($receive_digest === false)
				{
					//see if user wants emails from this group and this type
					$receive_emails = $this->notifications_model->setting($user_id, $group_id, $type);
					if ($receive_emails)
					{
						//send email
						$message = $this->load->view('notifications/email', array('notification' => $notification), true);
						//$this->load->view('notifications/email', array('notification' => $notification));
						
						$this->load->library('email');
			
						$config['mailtype'] = 'html';
						
						$this->email->initialize($config);
			
						$this->email->from('no-reply@communitychristian.org', 'Community Online');
						$this->email->to($user->email); 
						
						$this->email->subject('Community Online: '.$notification->subject);
						$this->email->message($message);	
						
						$this->email->send();
						
						$this->notifications_model->sent($notification->id);
					}
				}
			}
		}
		
		//$this->notifications_model->auto_delete();
	}
	
	function send_digest_emails($count = 15)
	{
		$digests = $this->db->where('notification_type', 'digest')->where('receive_emails', 1)->get('notification_settings');
		
		$i = 0;
		foreach ($digests->result() as $digest)
		{
			$group = $this->groups_model->item($digest->group_id);
			$user  = $this->users->item($digest->user_id);
			$notifications = $this->notifications_model->digest_outbox($digest->user_id, $digest->group_id);
			if ($notifications->num_rows() > 0 && $i < $count)
			{
				$message = $this->load->view('notifications/digest', array(	
					'group' => $group,
					'notifications' => $notifications
				), true);
				$this->load->view('notifications/digest', array(	
					'group' => $group,
					'notifications' => $notifications
				));
				
				$this->load->library('email');
	
				$config['mailtype'] = 'html';
				
				$this->email->initialize($config);
	
				$this->email->from('no-reply@communitychristian.org', 'Community Online');
				$this->email->to($user->email); 
				
				$this->email->subject('Community Online: Your Daily Digest for '.$group->name);
				$this->email->message($message);	
				
				$this->email->send();
				
				foreach ($notifications->result() as $notification)
				{
					$this->notifications_model->sent($notification->id);
				}
				$i++;
			}
		}
	}
	
	function mark_as_read($id)
	{
		if ($this->account !== false)
		{
			$this->notifications_model->unread($this->account->id, $id);
		}
		header('Content-type: application/json');
		echo json_encode(array('success' => 'awesome!'));
	}
	function mark_all_read()
	{
		if ($this->account !== false)
		{
			if ($_POST['ids'])
			{
				$ids = explode(',', $_POST['ids']);
				foreach($ids as $id)
				{
					$this->notifications_model->unread($this->account->id, $id);
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode(array('success' => 'awesome!'));
	}
	
	function unread_notifications()
	{
		if ($this->account !== false)
		{
			$notifications = $this->notifications_model->unread($this->account->id);
			$array = array('results' => array(), 'count' => $notifications->num_rows());
			foreach ($notifications->result() as $notification)
			{
				$notification->message = $notification->message ? unserialize($notification->message) : '';
				array_push($array['results'], array(
					'id' => $notification->id,
					'short_message' => preg_replace('/\<a/', '<strong', preg_replace('/\<\/a\>/', '</strong>', $notification->short_message)),
					'created_at' => ago($notification->created_at),
					'link' => $notification->message ? $notification->message['link'] : ''
				));
			}
			header('Content-type: application/json');
			echo json_encode($array);
		}
	}
	
}