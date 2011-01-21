<?php

class Watch extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	function save_note($service_id)
	{
		$content = $this->input->post('content');
		$user_id = $this->account->id;
			
		if ( ! $user_id )
		{
			show_404('page');
			return;
		}
			
		$this->notes_model->save($service_id, $user_id, $content);
	}
	
	function view_note($service_id)
	{
		$user_id = $this->account->id;
									
		if ( ! $user_id )
		{
			show_404('page');
			return;
		}

		$item = $this->notes_model->item(NULL, $service_id, $user_id);

		echo json_encode($item);
	}
	
	function index()
	{	
		$service = $this->services_model->next_service();
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
						
		if ($service) {
			$note = null;
			if ($this->account !== null)
			{
				$note = $this->notes_model->item(NULL, $service->id, $this->account->id);
				if ($note)
				{
					$note = $note->content;
				}
			}

			$query = $this->settings_model->value('twitter_search') . ' ' . '#'.$service->twitter_hash;
		
			$data = array(
				//'account' => $this->facebook_connect->account(),
				'title'			  => 'Live Service',
				'account'         => $this->account,
				'notes'			  => $note ? $note : '',
				'groups_for_user' => $groups_for_user,
				'service' 		  => $service,
				'query'			  => $query,
				'group'			  => $this->account ? $this->groups_model->small_group($this->account->id) : null,
				'css_files'       => array(
					'/resources/css/layout.css',
					'/resources/css/celebrate.css',
					'/resources/css/dateselector.jf.css'
				),
				'js_files'        => array(
					'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
					'/resources/js/jquery.swfobject.min.js',
					'/resources/js/ccc.js',
					'/resources/js/celebrate.js',
					'/resources/js/iscroll-min.js',
					'/resources/js/dateselector.jf.js'
				)
			);
			
			$this->load->vars($data);
			$this->load->view('general/head');
			$this->load->view('watch/live');
			$this->load->view('general/foot');
		}
		else {
			redirect('pages/video');
			$data = array(
				//'account' => $this->facebook_connect->account()
				'account'         => $this->account,
				'groups_for_user' => $groups_for_user,
				'query'			  => NULL,
				'group'			  => NULL,
				'css_files'       => array(
					'/resources/css/layout.css',
					'/resources/css/celebrate.css',
					'/resources/css/dateselector.jf.css'
				),
				'js_files'        => array(
					'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
					'/resources/js/jquery.swfobject.min.js',
					'/resources/js/ccc.js',
					'/resources/js/celebrate.js',
					'/resources/js/dateselector.jf.js'
				)
			);
			
			$this->load->vars($data);
			$this->load->view('general/head');
			$this->load->view('watch/noservice');
			$this->load->view('general/foot');
		}
	}
	
	function show($id)
	{
		$service = $this->services_model->item($id);
		
		if ($service) {
			if ($service->status == 'draft' && !$this->has_role('admin'))
			{
				show_error('This service doesn\'t exist. Please go back to <a href="'.site_url('watch').'">Watch</a> to find a service.');
			}
			
			// Service has expired over 2 hours ago
			if ($service->service_time - time() < 7200)
			{
				show_error('This service is expired.');
			}
			
			// The service wont start for an hour
			elseif ($service->service_time - time() > 3600)
			{
				show_error('This service doesn\'t start for more than an hour.');
			}
			
			// The service wont start for x minutes
			elseif ($service->service_time - time() > 1800)
			{
				show_error('This service doesn\'t start for another '.round(($service->service_time - time()) / 60).' minutes.');
			}
			else
			{
				$groups_for_user = null;
				if ($this->account !== null)
				{
					$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
				}
				$data = array(
					//'account' => $this->facebook_connect->account(),
					'account'         => $this->account,
					'groups_for_user' => $groups_for_user,
					'service'         => $service,
					'css_files'       => array(
						'/resources/css/layout.css',
						'/resources/css/celebrate.css',
						'/resources/css/dateselector.jf.css'
					),
					'js_files'        => array(
						'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
						'/resources/js/jquery.swfobject.min.js',
						'/resources/js/ccc.js',
						'/resources/js/celebrate.js',
						'/resources/js/iscroll-min.js',
						'/resources/js/dateselector.jf.js'
					)
				);
				$this->load->vars($data);
				$this->load->view('general/head');
				$this->load->view('watch/live');
				$this->load->view('general/foot');
			}
		}
		else
		{
			show_error('This service doesn\'t exist. Please go back to <a href="'.site_url('watch').'">Watch</a> to find a service.');
		}
	}
	
}