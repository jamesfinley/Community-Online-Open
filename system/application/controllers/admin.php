<?php

function generatePassword($length=9, $strength=0) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

class admin extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->require_role('admin', '');
		
		//get models
		$this->load->model('facebook_connect');
		$this->load->model('services_model');
		$this->load->model('schedule_model');
		$this->load->model('videos_model');
		$this->load->model('groups_model');
	}
	
	function video_list() //this outputs the JSON for use in the Add/Edit Services views
	{
		$video = $this->videos_model->items();
		header('Content-type: text/javascript');
		echo 'var files = '.json_encode($video->result_array()).';';
	}
	
	function users()
	{
		//load vars
		$this->load->vars(array(
			'title'    => 'Users',
			'page'     => 'users',
			'users'	   => $this->users->items()
		));	
	
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/view_users');
		$this->load->view('admin/footer');	
	}
	
	function add_user()
	{
		if ( $_POST )
		{
			$id = $this->users->insert($_POST['email'], $_POST['password'], $_POST['first_name'], $_POST['last_name']);		

			$this->session->set_flashdata('message', 'Added user.');			
		
			if ( isset($_POST['campus_pastor']) && $_POST['campus_pastor'] > 0 ) 
			{
				$this->roles_model->insert($id, $_POST['campus_pastor'], NULL, 'pastor');
			}
		
			redirect('admin/users/edit/'.$id);
			return;		
		}	
	
		$this->groups_model->begin();
		$this->groups_model->select('id, name');
		$campuses = $this->groups_model->items(NULL, 'campus')->result();
		$this->groups_model->end();	
	
		//load vars
		$this->load->vars(array(
			'title'       => 'Add User',
			'page'        => 'users',
			'campuses'	  => $campuses
		));
			
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/add_user');
		$this->load->view('admin/footer');	
	}
	
	function edit_user($id)
	{
		if ( $_POST )
		{
			$this->users->update($id, $_POST['email'], $_POST['password'], $_POST['first_name'], $_POST['last_name']);

			$this->session->set_flashdata('message', 'Updated user.');			
				
			if ( isset($_POST['campus_pastor']) ) 
			{
				if ( $_POST['campus_pastor'] == -1) 
				{
					$this->roles_model->delete(NULL, $id, NULL, NULL, 'pastor');
				}
				else if ($_POST['campus_pastor'] > 0)
				{
					if ( $this->users->is_pastor($id) )
					{
						$this->roles_model->delete(NULL, $id, NULL, NULL, 'pastor');
					}
				
					$this->roles_model->insert($id, $_POST['campus_pastor'], NULL, 'pastor');
				}
			}	
				
			redirect('admin/users/edit/'.$id);
			return;		
		}	
		
		$this->groups_model->begin();
		$this->groups_model->select('id, name');
		$campuses = $this->groups_model->items(NULL, 'campus')->result();
		$this->groups_model->end();
	
		//load vars
		$this->load->vars(array(
			'title'       => 'Edit User',
			'page'        => 'users',
			'campuses'	  => $campuses,
			'user'		  => $this->users->item($id),
			
		));
			
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/edit_user');
		$this->load->view('admin/footer');		
	}
	
	
	function settings()
	{
		if ( isset($_POST['twitter_search']) )
		{
			$this->settings_model->save('twitter_search', $_POST['twitter_search']);
		}
	
		//load vars
		$this->load->vars(array(
			'title'    => 'Settings',
			'page'     => 'settings',
			'twitter_query' => $this->settings_model->value('twitter_search')
		));	
	
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/settings');
		$this->load->view('admin/footer');		
	}
	
	function index()
	{
		if ($_POST)
		{
			//get data
			$big_idea     = $_POST['big_idea'];
			$series_title = $_POST['series_title'];
			$schedule_id  = $_POST['schedule_id'];
			$start_at     = strtotime($_POST['date']);
			$end_at       = $_POST['end_at'];
			$videos       = serialize($_POST['video']);
			$content_i    = count($_POST['content']);
			$status       = $_POST['status'];
			while ($content_i--) {
				$_POST['content'][$content_i] = unserialize(str_replace("'", '"', $_POST['content'][$content_i]));
			}
			$content      = serialize($_POST['content']);
			
			$twitter_hash = $_POST['twitter_hash'];
			
			//create service and get id
			$id           = $this->services_model->create($big_idea, $series_title, $schedule_id, $start_at, $end_at, $videos, $content, $twitter_hash, $status);
			$this->session->set_flashdata('message', 'Service added.');
			
			//redirect to edit service
			redirect('admin/service/'.$id);
		}
		
		//load data
		$schedule = $this->schedule_model->items(1);
		
		//load vars
		$this->load->vars(array(
			'title'    => 'Add Service',
			'page'     => 'services',
			'scripts'  => array(
				'/resources/js/php.default.min.js',
				'/resources/js/jquery.swfobject.min.js',
				'/resources/js/jquery.placeholder.js',
				'/admin/video_list',
				'/resources/js/admin_services.js'
			),
			'schedule' => $schedule
		));
		
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/add_service');
		$this->load->view('admin/footer');
	}
	
	function edit_service($id)
	{
		//load data
		$service = $this->services_model->item($id);
		
		if ($service !== false)
		{
			if ($_POST)
			{
				//get data
				$big_idea     = $_POST['big_idea'];
				$series_title = $_POST['series_title'];
				$schedule_id  = $_POST['schedule_id'];
				$start_at     = strtotime($_POST['date']);
				$end_at       = $_POST['end_at'];
				$videos       = serialize($_POST['video']);
				/*$content_i    = count($_POST['content']);
				while ($content_i--) {
					$_POST['content'][$content_i] = unserialize(str_replace("'", '"', $_POST['content'][$content_i]));
				}
				$content      = serialize($_POST['content']);*/
				$status       = $_POST['status'];

				$twitter_hash = $_POST['twitter_hash'];
				
				//create service and get id
				$this->services_model->update($id, $big_idea, $series_title, $schedule_id, $start_at, $end_at, $videos, $twitter_hash, $status/*, $content*/);
				$this->session->set_flashdata('message', 'Updated service.');
				
				//redirect to edit service
				redirect('admin/service/'.$id);
				
				$service = $this->services_model->item($id);
			}
			
			$schedule = $this->schedule_model->items(1);
			$videos   = $this->videos_model->items(1);
			
			//load vars
			$this->load->vars(array(
				'title'   => 'Edit Service &raquo; '.$service->big_idea.' ('.$service->series_title.')',
				'page'    => 'services',
				'scripts' => array(
					'/resources/js/php.default.min.js',
					'/resources/js/jquery.swfobject.min.js',
					'/resources/js/jquery.placeholder.js',
					'/admin/video_list',
					'/resources/js/admin_services.js'
				),
				'schedule' => $schedule,
				'service' => $service
			));
			
			//load view
			$this->load->view('admin/header');
			$this->load->view('admin/edit_service');
			$this->load->view('admin/footer');
		}
		else
		{
			show_error('Unable to find service.');
		}
	}
	
	function big_idea($page = 1)
	{
		//load data
		$big_ideas = $this->big_idea_model->items();

		//load vars
		$this->load->vars(array(
			'title'       => 'View Services'.($page != 1 ? ' (Page '.$page.')' : ''),
			'page'        => 'big_idea',
			'scripts'     => array(
				'/resources/js/admin_services.js'
			),
			'page_number' => $page,
			'big_ideas'    => $big_ideas
		));
		
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/view_big_ideas');
		$this->load->view('admin/footer');
	}
	
	function add_big_idea()
	{	
		$this->load->helper('form');

		//load vars
		$this->load->vars(array(
			'title'       => 'Add Big Idea',
			'page'        => 'big_idea'
		));
		
		if ( $_POST )
		{
			$path = 'user_images';
		
			if ( ! is_dir($path) )
			{
				mkdir($path);
			}
			
			$path = 'user_images/big_idea/';

			if ( ! is_dir($path) )
			{
				mkdir($path);
			}
			
			$bi = $_POST;
			$bi['begin_at'] = strtotime($bi['begin_at']);

			$id = $this->big_idea_model->insert($bi['series_title'], $bi['category'], $bi['short_description'], $bi['long_description'], $bi['description_x'], $bi['description_y'], $bi['videos_x'], $bi['videos_y'], $bi['background_color'], $bi['border_color'], $bi['text_color'], '', $bi['begin_at']);

			$image_err = NULL;
			$files_err = NULL;
			
			if ( $this->files_model->can_upload('background_image') )
			{
				$this->files_model->upload('background_image', $id, 'big_idea_banner', $path, $image_err);
			}

			if ( $this->files_model->can_upload('file') )
			{									
				$this->files_model->upload('file', $id, 'big_idea_file', $path, $files_err);	
			}
			
			$error = $image_err . $files_err;
			
			$message = $error ? $error : 'Updated big idea.';
			$this->session->set_flashdata('message', $message);
				
			if ( $error )
			{
				$this->session->set_flashdata('message', $error);	

				redirect('admin/big_idea/add');
				return;			
			}	
				
			$this->session->set_flashdata('message', 'Added big idea.');			
				
			redirect('admin/big_idea/edit/'.$id);
			return;		
		}

		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/add_big_idea');
		$this->load->view('admin/footer');	
	}
	
	function edit_big_idea($id)
	{
		$this->load->helper('form');
				
		$big_idea = $this->big_idea_model->item($id);
		
		if ( $big_idea )
		{
			if ( $_POST )
			{

				$path = 'user_images';
			
				if ( ! is_dir($path) )
				{
					mkdir($path);
				}
				
				$path = 'user_images/big_idea/';
	
				if ( ! is_dir($path) )
				{
					mkdir($path);
				}	
				
				$bi = $_POST;
				$bi['begin_at'] = strtotime($bi['begin_at']);
	
				$this->big_idea_model->update($id, $bi['series_title'], $bi['category'], $bi['short_description'], $bi['long_description'], $bi['description_x'], $bi['description_y'], $bi['videos_x'], $bi['videos_y'], $bi['background_color'], $bi['border_color'], $bi['text_color'], '', $bi['begin_at']);	

				$this->files_model->can_upload('background_image');
				$this->files_model->can_upload('file');

				$image_err = NULL;
				$files_err = NULL;
				
				if ( $this->files_model->can_upload('background_image') )
				{
					$this->files_model->upload('background_image', $id, 'big_idea_banner', $path, $image_err);
				}

				if ( $this->files_model->can_upload('file') )
				{									
					$this->files_model->upload('file', $id, 'big_idea_file', $path, $files_err);	
				}
				
				$error = $image_err . $files_err;
				
				$message = $error ? $error : 'Updated big idea.';
				$this->session->set_flashdata('message', $message);
												
				redirect('admin/big_idea/edit/'.$big_idea->id);
				return;
			}
		
			//load vars
			$this->load->vars(array(
				'title'       => 'Edit Big Idea &raquo',
				'page'        => 'big_idea',
				'big_idea'     => $big_idea,
			));
			
			//load view
			$this->load->view('admin/header');
			$this->load->view('admin/edit_big_idea');
			$this->load->view('admin/footer');
		}
		else
		{
			show_error('Unable to find big_idea.');
		}
	}

	
	function services($page = 1)
	{
		//load data
		$services = $this->services_model->items($page);
		
		//load vars
		$this->load->vars(array(
			'title'       => 'View Services'.($page != 1 ? ' (Page '.$page.')' : ''),
			'page'        => 'services',
			'scripts'     => array(
				'/resources/js/admin_services.js'
			),
			'page_number' => $page,
			'services'    => $services
		));
		
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/view_services');
		$this->load->view('admin/footer');
	}
	
	function add_schedule()
	{
		if ($_POST)
		{
			$time = $_POST['start_time'];
			preg_match('/^([0-9]?[0-9]):([0-9]{2})$/', $time, $matches);
			if ($matches)
			{
				$time = $matches[1].$matches[2];
			}
			else
			{
				preg_match('/^([0-9]?[0-9]):([0-9]{2})[ ]?([apAP][mM])$/', $time, $matches);
				if ($matches)
				{
					if (($matches[3] == 'PM' || $matches[3] == 'pm') && $matches[1] != 12)
					{
						$matches[1] += 12;
					}
					$time = $matches[1].$matches[2];
				}
				else {
					$time = null;
				}
			}
			if ($time !== null)
			{
				$id = $this->schedule_model->create($_POST['day_of_week'], $time);
				$this->session->set_flashdata('message', 'Added scheduled time.');
				
				redirect('admin/schedule/edit/'.$id);
			}
			else {
				
			}
		}
		
		//load vars
		$this->load->vars(array(
			'title'       => 'Add Service Time',
			'page'        => 'schedule'
		));
		
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/add_schedule');
		$this->load->view('admin/footer');
	}
	
	function edit_schedule($id)
	{
		if ($_POST)
		{
			$time = $_POST['start_time'];
			preg_match('/^([0-9]?[0-9]):([0-9]{2})$/', $time, $matches);
			if ($matches)
			{
				$time = $matches[1].$matches[2];
			}
			else
			{
				preg_match('/^([0-9]?[0-9]):([0-9]{2})[ ]?([apAP][mM])$/', $time, $matches);
				if ($matches)
				{
					if (($matches[3] == 'PM' || $matches[3] == 'pm') && $matches[1] != 12)
					{
						$matches[1] += 12;
					}
					$time = $matches[1].$matches[2];
				}
				else {
					$time = null;
				}
			}
			if ($time !== null)
			{
				$this->schedule_model->update($id, $_POST['day_of_week'], $time);
				$this->session->set_flashdata('message', 'Updated schedule.');
				
				redirect('admin/schedule');
			}
			else {
				
			}
		}
		
		//load data
		$service = $this->schedule_model->item($id);
		
		if ($service !== false)
		{
			$service->time = $service->time / 60;
			$hours         = floor($service->time / 60);
			$minutes       = $service->time - (floor($service->time / 60) * 60);
			$service->time = $hours.($minutes < 10 ? '0' : '').$minutes;
			
			preg_match('/([0-9]?[0-9])([0-9]{2})/', $service->time, $matches);
			if ($matches[1] > 11) {
				$time = ($matches[1] != 12 ? $matches[1] - 12 : $matches[1]).$matches[2].' PM';
			}
			else {
				$time = $matches[1].$matches[2].' AM';
			}
			$time = preg_replace('/([0-9]?[0-9])([0-9]{2})/', '$1:$2', $time);
			
			switch ($service->day_of_week) {
				case 0:
					$day = 'Sunday';
					break;
				case 1:
					$day = 'Monday';
					break;
				case 2:
					$day = 'Tuesday';
					break;
				case 3:
					$day = 'Wednesday';
					break;
				case 4:
					$day = 'Thursday';
					break;
				case 5:
					$day = 'Friday';
					break;
				case 6:
					$day = 'Saturday';
					break;
			}
			
			//load vars
			$this->load->vars(array(
				'title'       => 'Edit Service Time &raquo; '.$time.' on '.$day,
				'page'        => 'schedule',
				'service'     => $service,
				'time'        => $time,
				'day_of_week' => $day
			));
			
			//load view
			$this->load->view('admin/header');
			$this->load->view('admin/edit_schedule');
			$this->load->view('admin/footer');
		}
		else
		{
			show_error('Unable to find service time.');
		}
	}
	
	function schedule($page = 1)
	{
		//load data
		$schedule = $this->schedule_model->items($page);
		
		//load vars
		$this->load->vars(array(
			'title'       => 'Schedule'.($page != 1 ? ' (Page '.$page.')' : ''),
			'page'        => 'schedule',
			'scripts'     => array(
				'/resources/js/admin_services.js'
			),
			'page_number' => $page,
			'schedule'    => $schedule
		));
		
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/view_schedule');
		$this->load->view('admin/footer');
	}
	
	function add_group()
	{
		if ($_POST)
		{
			$g = $_POST;

			$id = $this->groups_model->insert(NULL, $g['type'], $g['name'], $g['slug'], $g['description'], $g['service_times'], $g['address'], $g['city'], $g['zip_code'], $g['state'], $g['country'], $g['longitude'], $g['latitude']);		
			
			$this->session->set_flashdata('message', 'Added group.');			
			
			redirect('admin/groups/view/'.$id);
			return;		
		}
		
		//load vars
		$this->load->vars(array(
			'title'       => 'Add Groups',
			'page'        => 'groups',
			'scripts'     => array(
				//'/resources/js/admin_services.js'
			)
		));
		
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/add_group');
		$this->load->view('admin/footer');
	}
	
	function view_group($id)
	{
		//load data
		$group = $this->groups_model->item($id);
		if ($group !== false) 
		{
			if ($_POST)
			{
				$g = $_POST;

				if ( isset($g['facilitator']) && $g['facilitator'] > 0 )
				{
					$facilitator = $this->users->facilitator($group->id);

					if ( $facilitator && $facilitator->id != $g['facilitator'] )
					{
						$this->roles_model->delete($facilitator->role_id);
					}
					else if ( ! $facilitator )
					{
						$this->roles_model->insert($g['facilitator'], $group->id, NULL, 'facilitator');	
					}
				}

				$this->groups_model->update($id, NULL, $g['type'], $g['name'], $g['slug'], $g['description'], $g['service_times'], $g['address'], $g['city'], $g['zip_code'], $g['state'], $g['country'], $g['longitude'], $g['latitude']);		
				
				$hide = isset($_POST['hide_news']) ? TRUE : FALSE;
				$this->groups_model->hide($id, $hide, 'news');
	
				$hide = isset($_POST['hide_events']) ? TRUE : FALSE;	
				$this->groups_model->hide($id, $hide, 'events');
	
				$hide = isset($_POST['hide_discussion']) ? TRUE : FALSE;
				$this->groups_model->hide($id, $hide, 'discussion');
	
				$hide = isset($_POST['hide_prayers']) ? TRUE : FALSE;
				$this->groups_model->hide($id, $hide, 'prayers');
	
				$hide = isset($_POST['hide_qna']) ? TRUE : FALSE;
				$this->groups_model->hide($id, $hide, 'qna');
				
				$this->session->set_flashdata('message', 'Updated group.');			
				
				redirect('admin/groups/view/'.$id);
				return;		
			}
				
			$facilitator = $this->users->facilitator($group->id);	
			$facilitator_id = $facilitator ? $facilitator->id : FALSE;
												
			//load vars
			$this->load->vars(array(
				'title'       => 'View Group &raquo; '.$group->name,
				'page'        => 'groups',
				'group'       => $group,
				'users'		  => $this->users->items_in_group($group->id)->result(),
				'facilitator_id' => $facilitator_id
			));
			
			//load view
			$this->load->view('admin/header');
			$this->load->view('admin/view_group');
			$this->load->view('admin/footer');
		}
		else
		{
			show_error('Unable to find group.');
		}
	}
	
	function groups($page = 1)
	{
		//load data
		$groups = $this->groups_model->items($page);
		
		//load vars
		$this->load->vars(array(
			'title'       => 'Groups'.($page != 1 ? ' (Page '.$page.')' : ''),
			'page'        => 'groups',
			'scripts'     => array(
				//'/resources/js/admin_services.js'
			),
			'page_number' => $page,
			'groups'      => $groups
		));
		
		//load view
		$this->load->view('admin/header');
		$this->load->view('admin/view_groups');
		$this->load->view('admin/footer');
	}
	
}