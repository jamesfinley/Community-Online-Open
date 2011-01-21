<?php

class Connect extends MY_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		
	}
	
	// Ajax
	function api_share_post($post_id, $group_id)
	{
		header('Content-type: application/javascript');
		if ($this->is_logged_in() === true)
		{
			if ($this->db->where('stream_post_id', $post_id)->where('group_id', $group_id)->get('shared_stream_posts')->num_rows())
			{
				echo '{success:false}';
			}
			else
			{
				$this->db->insert('shared_stream_posts', array(
					'stream_post_id' => $post_id,
					'group_id'       => $group_id,
					'user_id'        => $this->account->id,
					'created_at'     => time()
				));
				
				$post = $this->stream_posts_model->item($post_id);
				$group = $this->groups_model->item($post->group_id);
				
				//send notifications to all members
				$members = $this->db->where('group_id', $group_id)->get('groups_users');
				$short_message = $this->account->first_name.' '.$this->account->last_name.' shared a <a href="'.site_url($this->groups_model->get_url($group->id).'/p/'.$post->id).'">'.$post->type.'</a> to <a href="'.site_url($this->groups_model->get_url($group->id)).'">'.$group->name.'</a>.';
				$message = serialize(array(
					'shared_by' => $this->account->first_name.' '.$this->account->last_name,
					'subject' => $post->type === 'prayer' ? 'Prayer Shared by '.$this->account->first_name.' '.$this->account->last_name : $post->subject,
					'content' => $post->content,
					'created_at' => time(),
					'link' => site_url($this->groups_model->get_url($group->id).'/p/'.$post->id)
				));
				
				foreach ($members->result() as $member)
				{
					if ($member->user_id !== $this->account->id)
					{
						$this->notifications_model->create($member->user_id, $group_id, $post->type, ($post->type === 'prayer' ? 'Prayer Shared by '.$this->account->first_name.' '.$this->account->last_name : $post->subject), $message, $short_message);
					}
				}
				
				echo '{success:true}';
			}
		}
		else
		{
			echo '{success:false}';
		}
	}
	function item_list_ajax($slug, $type, $page)
	{	
/*
		$items = $this->stream_posts_model->items_with_slug($slug, $type, 25, $page)->result();
		
		echo json_encode($items);
*/
		$this->load->helper('date');

		$group = $this->groups_model->item(null, $slug);
	
		if ( $group )
		{
			// get campuses associated with a master group
			$campuses = null;
			if ($group->type === 'master')
			{
				$campuses = $this->db->where('type', 'campus')->where('campus_id', $group->id)->get('groups');
				if ($campuses->num_rows() === 0)
				{
					$campuses = null;
				}
			}
			
			// get church and small groups associated with a campus
			$church       = null;
			$small_groups = null;
			if ($group->type === 'campus')
			{
				$church       = $this->db->where('type', 'master')->where('id', $group->campus_id)->get('groups');
				if ($church->num_rows() === 1)
				{
					$church   = $church->row(0);
				}
				else
				{
					$church   = null;
				}
				if ($church !== null)
				{
					$campuses = $this->db->where('type', 'campus')->where('campus_id', $church->id)->where('id', '!='.$group->id)->get('groups');
					if ($campuses->num_rows() === 0)
					{
						$campuses = null;
					}
				}
				$small_groups = $this->db->where('type', 'small group')->where('campus_id', $group->id)->limit(15)->get('groups');
				if ($small_groups->num_rows() === 0)
				{
					$small_groups = null;
				}
			}
			
			// get campus associated with a small group
			$campus = null;
			if ($group->type === 'small group')
			{
				$campus       = $this->db->where('id', $group->campus_id)->get('groups');
				if ($campus->num_rows() === 1)
				{
					$campus   = $campus->row(0);
				}
				else
				{
					$campus   = null;
				}
				if ($campus !== null)
				{
					$church       = $this->db->where('type', 'master')->where('id', $campus->id)->get('groups');
					if ($church->num_rows() === 1)
					{
						$church   = $church->row(0);
					}
					else
					{
						$church   = null;
					}
				}
			}
			
			$types = array('none');
			
			if ($group->hide_news == 0) $types[] = 'news';
			if ($group->hide_events == 0) $types[] = 'event';
			if ($group->hide_contributions == 0) $types[] = 'contribution';
			if ($group->hide_discussion == 0) $types[] = 'discussion';
			if ($group->hide_prayers == 0) $types[] = 'prayer';
			if ($group->hide_qna == 0) $types[] = 'qna';
			
			$groups_for_user = null;
			if ($this->account !== null)
			{
				$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
			}
	
			$this->load->vars(array(
				'message'         => $this->session->flashdata('message'),
				'error'           => $this->session->flashdata('error'),
				'types'			  => $types,
				'account'         => $this->account,
				'groups_for_user' => $groups_for_user,
				'group'           => $group,
				'campuses'        => $campuses,
				'church'          => $church,
				'campus'          => $campus,
				'streams'	      => $this->stream_posts_model->items($group->id, $type, $page, 25)->result(),
				'small_groups'    => $small_groups
			));
			
			$this->load->view('connect/group/stream');
		}
		else
		{
			show_error('The group "'.$slug.'" does not exist.');
		}
	}	
		
	function join_group_api($group_id, $user_id)
	{
		$this->groups_model->assign_user($user_id, $group_id);
	}
	function group_images($slug)
	{
		$images = $this->groups_model->images($slug);
		
		echo json_encode($images);
	}
	
	function groups_online($group_id = 0)
	{
		$this->groups_model->begin();
		$this->groups_model->select(array("CONCAT(users.first_name, ' ', users.last_name) AS facilitator_name", 'roles.type AS facilitator_type', 'groups.id AS group_id', 'groups.name AS group_name'), FALSE);
		
		$online = $this->groups_model->groups_online($group_id)->result();
		$this->groups_model->end();
		
		echo json_encode($online);
	}
	
	function users_online($group_id = 0)
	{
		/*$this->users->begin();
		$this->users->select('id, CONCAT(users.first_name, " ", users.last_name) AS full_name');*/
		$users = $this->users->online($group_id);
		//$this->users->end();  POOPY POOPY STICK
				
		echo json_encode($users->result());
	}
	
	function find($search_type, $group_type)
	{
		if ($search_type === 'geolocation') {
			$latitude  = $_POST['latitude'];
			$longitude = $_POST['longitude'];
			$miles     = $_POST['miles'];
			$limit     = $_POST['limit'];
			
			$groups    = $this->db->query('SELECT *, acos(SIN( PI()* '.$latitude.' /180 )*SIN( PI()*latitude/180 ))+(cos(PI()* '.$latitude.' /180)*COS( PI()*latitude/180) *COS(PI()*longitude/180-PI()* '.$longitude.' /180))* 3963.191 AS distance FROM groups WHERE 1=1 AND 3963.191 * ACOS( (SIN(PI()* '.$latitude.' /180)*SIN(PI() * latitude/180)) + (COS(PI()* '.$latitude.' /180)*cos(PI()*latitude/180)*COS(PI() * longitude/180-PI()* '.$longitude.' /180))) <= '.$miles.' ORDER BY 3963.191 * ACOS((SIN(PI()* '.$latitude.' /180)*SIN(PI()*latitude/180)) + (COS(PI()* '.$latitude.' /180)*cos(PI()*latitude/180)*COS(PI() * longitude/180-PI()* '.$longitude.' /180))) LIMIT '.$limit);
			
			header('Content-type: application/json');
			echo json_encode($groups->result_array());
		}
	}
	
	function group_settings($slug)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$message = NULL;
		
		if (!$this->groups_model->belongs_to_group($group->id, $this->account->id)) redirect($this->groups_model->get_url($group->id));
		
		if ($_POST)
		{
			if (isset($_POST['receive_digest']))
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'digest', 1);
			}
			else
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'digest', 0);
			}
			if (isset($_POST['receive_news']))
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'news', 1);
			}
			else
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'news', 0);
			}
			if (isset($_POST['receive_events']))
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'event', 1);
			}
			else
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'event', 0);
			}
			if (isset($_POST['receive_contributions']))
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'contribution', 1);
			}
			else
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'contribution', 0);
			}
			if (isset($_POST['receive_discussions']))
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'discussion', 1);
			}
			else
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'discussion', 0);
			}
			if (isset($_POST['receive_prayers']))
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'prayer', 1);
			}
			else
			{
				$this->notifications_model->setting($this->account->id, $group->id, 'prayer', 0);
			}
			
			$this->session->set_flashdata('message', 'Your settings have been updated.');
			redirect(current_url());
		}
		
		// get campuses associated with a master group
		$campuses = null;
		if ($group->type === 'master')
		{
			$campuses = $this->db->where('type', 'campus')->where('campus_id', $group->id)->get('groups');
			if ($campuses->num_rows() === 0)
			{
				$campuses = null;
			}
		}
		
		// get church and small groups associated with a campus
		$church       = null;
		$small_groups = null;
		if ($group->type === 'campus')
		{
			$church       = $this->groups_model->item($group->campus_id);//$this->db->where('type', 'master')->where('id', $group->campus_id)->get('groups');
			if ($church !== false && $church->type == 'master')
			{
				//$church   = $church->row(0);
			}
			else
			{
				$church   = null;
			}
			if ($church !== null)
			{
				$campuses = $this->db->where('type', 'campus')->where('campus_id', $church->id)->where('id', '!='.$group->id)->get('groups');
				if ($campuses->num_rows() === 0)
				{
					$campuses = null;
				}
			}
			$small_groups = $this->db->where('type', 'small group')->where('campus_id', $group->id)->limit(15)->get('groups');
			if ($small_groups->num_rows() === 0)
			{
				$small_groups = null;
			}
		}
		
		// get campus associated with a small group
		$campus = null;
		if ($group->type === 'small group')
		{
			$campus       = $this->groups_model->item($group->campus_id);
			if ($campus === false) $campus = null;
			if ($campus !== null)
			{
				$church       = $this->groups_model->item($campus->id);
				if ($church !== false && $church->type == 'master')
				{
					$church   = $church->row(0);
				}
				else
				{
					$church   = null;
				}
			}
		}

		$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		$pages = $this->pages_model->items_in_sidebar($group->type === 'master' ? 0 : $group->id);
		
		$data = array(
			'title'			  => $group->name . ' &raquo; Email Settings',
			'message'		  => $this->session->flashdata('message'),
			'account'		  => $this->account,
			'group'			  => $group,
			'groups_for_user' => $groups_for_user,
			'pages'			  => $pages,
			'campuses'        => $campuses,
			'church'          => $church,
			'campus'          => $campus,
			'small_groups'    => $small_groups,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/connect.js',
				'/resources/js/group_page.js',
				'/resources/js/dateselector.jf.js'
			)
		);
		
		$data['receive_digest']        = $this->notifications_model->receive_digest($this->account->id, $group->id);
		$data['receive_news']          = $this->notifications_model->setting($this->account->id, $group->id, 'news');
		$data['receive_events']        = $this->notifications_model->setting($this->account->id, $group->id, 'event');
		$data['receive_contributions'] = $this->notifications_model->setting($this->account->id, $group->id, 'contribution');
		$data['receive_discussions']   = $this->notifications_model->setting($this->account->id, $group->id, 'discussion');
		$data['receive_prayers']       = $this->notifications_model->setting($this->account->id, $group->id, 'prayer');
		
		$this->load->vars($data);
		$this->load->view('general/head');
		$this->load->view('connect/group/group_settings');
		$this->load->view('general/foot');
	}
	
	function view_pages_in_settings()
	{
	
	}
	
	function add_page_in_settings()
	{
	
	}
	
	function edit_page_in_settings()
	{
	
	}
	
	function user_settings()
	{
	
	}
	
	function settings($slug)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$id = $group->id;
		$message = NULL;

		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}


		if ($this->has_role('facilitator', $group->id) === false)
		{
			show_error('You are not a facilitator of this group');
		}
	
		
		if ( $_POST )
		{
			$this->groups_model->update($id, NULL, NULL, $_POST['name'], $_POST['slug'], $_POST['description'], $_POST['service_times']);
		
			/*$hide = isset($_POST['hide_news']) ? TRUE : FALSE;
			$this->groups_model->hide($id, $hide, 'news');

			$hide = isset($_POST['hide_events']) ? TRUE : FALSE;	
			$this->groups_model->hide($id, $hide, 'events');

			$hide = isset($_POST['hide_discussion']) ? TRUE : FALSE;
			$this->groups_model->hide($id, $hide, 'discussion');

			$hide = isset($_POST['hide_prayers']) ? TRUE : FALSE;
			$this->groups_model->hide($id, $hide, 'prayers');

			$hide = isset($_POST['hide_qna']) ? TRUE : FALSE;
			$this->groups_model->hide($id, $hide, 'qna');*/
			
			$this->session->set_flashdata('message', 'Updated group.');
			redirect($this->groups_model->get_url($group->id).'/settings');
		}
		
		$this->load->vars(array(
			'message'         => $message ? $message : $this->session->flashdata('message'),
			'error'           => $this->session->flashdata('error'),
			'account'         => $this->account,
			'group'           => $this->groups_model->item($id),
			'groups_for_user' => $groups_for_user,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/connect.js',
				'/resources/js/group_page.js',
				'/resources/js/dateselector.jf.js'
			)
		));
		
		//$this->load->view('connect/group/head');
		$this->load->view('general/head');
		$this->load->view('connect/group/settings');
		$this->load->view('general/foot');
	}
	
	function page_settings($slug)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$id = $group->id;
		$message = NULL;
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		
		
		if ($this->has_role('facilitator', $group->id) === false)
		{
			show_error('You are not a facilitator of this group');
		}
		
		$pages = $this->pages_model->items($group->id);
		
		$this->load->vars(array(
			'title'			  => $group->name . ' &raquo; Group Settings',
			'message'         => $message ? $message : $this->session->flashdata('message'),
			'error'           => $this->session->flashdata('error'),
			'account'         => $this->account,
			'group'           => $this->groups_model->item($id),
			'groups_for_user' => $groups_for_user,
			'pages'			  => $pages,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/connect.js',
				'/resources/js/group_page.js',
				'/resources/js/dateselector.jf.js'
			)
		));
		
		$this->load->view('general/head');
		$this->load->view('connect/group/page_settings');
		$this->load->view('general/foot');
	}
	
	function member_settings($slug)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$id = $group->id;
		$message = NULL;
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		
		if ($this->has_role('facilitator', $group->id) === false)
		{
			show_error('You are not a facilitator of this group');
		}
		
		if ($_POST) {
			foreach ($_POST as $key=>$value)
			{
				if (strpos($key, 'member-') !== false) {
					$memberID = str_replace('member-', '', $key);
					$this->db->where('user_id', $memberID)->where('group_id', $group->id)->update('groups_users', array('approved' => 1));
					
					$member = $this->users->item($memberID);
					
					$message = serialize(array(
						'posted_by' => $this->account->first_name.' '.$this->account->last_name,
						'subject' => 'You have been approved to join group '.$group->name,
						'created_at' => time(),
						'link' => site_url($this->groups_model->get_url($group->id))
					));
					$short_message = 'You have been approved to join group <a href="'.site_url($this->groups_model->get_url($group->id)).'">'.$group->name.'</a>.';
					$this->notifications_model->create($member->id, $group->id, 'user approved', ('You have been approved to join group '.$group->name), $message, $short_message);
					
					$message = null;
				}
			}
		}
		
		//$pages = $this->pages_model->items($group->id);
		$users = $this->db->query('SELECT u.*, gu.approved FROM users u, groups_users gu WHERE u.id = gu.user_id AND gu.group_id = ?', $id);
		
		$this->load->vars(array(
			'title'			  => $group->name . ' &raquo; Member Settings',
			'message'         => $message ? $message : $this->session->flashdata('message'),
			'error'           => $this->session->flashdata('error'),
			'account'         => $this->account,
			'group'           => $this->groups_model->item($id),
			'groups_for_user' => $groups_for_user,
			//'pages'			  => $pages,
			'users'			  => $users,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/connect.js',
				'/resources/js/group_page.js',
				'/resources/js/dateselector.jf.js'
			)
		));
		
		$this->load->view('general/head');
		$this->load->view('connect/group/member_settings');
		$this->load->view('general/foot');
	}
	
	function member_settings_make_facilitator($slug, $member_id)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$id = $group->id;
		$message = NULL;
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		
		if ($this->has_role('facilitator', $group->id) === false)
		{
			show_error('You are not a facilitator of this group');
		}
		
		if ($this->groups_model->belongs_to_group($group->id, $member_id) === false || $this->users->is_facilitator($member_id, $group->id) === true)
		{
			redirect($this->groups_model->get_url($group->id).'/settings/members');
		}
		$this->db->insert('roles', array(
			'user_id'    => $member_id,
			'group_id'   => $group->id,
			'type'       => 'facilitator',
			'parent_id'  => $this->account->id,
			'created_at' => time(),
			'updated_at' => time()
		));
		redirect($this->groups_model->get_url($group->id).'/settings/members');
	}
	
	function member_settings_make_apprentice_facilitator($slug, $member_id)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$id = $group->id;
		$message = NULL;
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		
		if ($this->has_role('facilitator', $group->id) === false)
		{
			show_error('You are not a facilitator of this group');
		}
		
		if ($this->groups_model->belongs_to_group($group->id, $member_id) === false || $this->users->is_apprentice($member_id, $group->id) === true)
		{
			redirect($this->groups_model->get_url($group->id).'/settings/members');
		}
		$this->db->insert('roles', array(
			'user_id'    => $member_id,
			'group_id'   => $group->id,
			'type'       => 'apprentice',
			'parent_id'  => $this->account->id,
			'created_at' => time(),
			'updated_at' => time()
		));
		redirect($this->groups_model->get_url($group->id).'/settings/members');
	}
	
	function add_page_settings($slug)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$id = $group->id;
		$message = NULL;
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		
		if ($this->has_role('facilitator', $group->id) === false)
		{
			show_error('You are not a facilitator of this group');
		}
		
		$error = null;
		if ($_POST)
		{
			if ($_POST['title'] && $_POST['slug'])
			{
				$id = $this->pages_model->insert($group->id, $_POST['title'], $_POST['slug'], $_POST['content'], $_POST['show_in_sidebar']);
				$this->session->set_flashdata('message', 'Your page has been added.');
				redirect($this->groups_model->get_url($group->id).'/settings/pages/'.$id);
			}
			else
			{
				$error = 'Title and Slug are required fields';
				$this->session->set_flashdata('error', $error);
			}
		}
		
		$this->load->vars(array(
			'title'			  => $group->name . ' &raquo; Page Settings',
			'message'         => $message ? $message : $this->session->flashdata('message'),
			'error'           => $error ? $error : $this->session->flashdata('error'),
			'account'         => $this->account,
			'group'           => $this->groups_model->item($id),
			'groups_for_user' => $groups_for_user,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/connect.js',
				'/resources/js/group_page.js',
				'/resources/js/dateselector.jf.js',
				'/wymeditor/jquery.wymeditor.min.js'
			)
		));
		
		$this->load->view('general/head');
		$this->load->view('connect/group/add_page_settings');
		$this->load->view('general/foot');
	}
	
	function edit_page_settings($slug, $page_id)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$id = $group->id;
		$message = NULL;
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		
		
		if ($this->has_role('facilitator', $group->id) === false)
		{
			show_error('You are not a facilitator of this group');
		}
		
		if ($_POST)
		{
			$this->pages_model->update($page_id, $group->id, $_POST['slug'], $_POST['title'], $_POST['content'], $_POST['show_in_sidebar']);
			$message = 'Updated Group.';
		}
		
		$page = $this->pages_model->item($page_id, $group->id);
		if ($page !== false) {
			$this->load->vars(array(
				'title'			  => $group->name . ' &raquo; Page Settings',
				'message'         => $message ? $message : $this->session->flashdata('message'),
				'error'           => $this->session->flashdata('error'),
				'account'         => $this->account,
				'group'           => $this->groups_model->item($id),
				'groups_for_user' => $groups_for_user,
				'page'			  => $page,
				'css_files'       => array(
					'/resources/css/layout.css',
					'/resources/css/group_page.css',
					'/resources/css/dateselector.jf.css'
				),
				'js_files'        => array(
					'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
					'/resources/js/ccc.js',
					'/resources/js/connect.js',
					'/resources/js/group_page.js',
					'/resources/js/dateselector.jf.js',
					'/wymeditor/jquery.wymeditor.min.js'
				)
			));
			
			$this->load->view('general/head');
			$this->load->view('connect/group/edit_page_settings');
			$this->load->view('general/foot');
		}
		else {
			redirect($this->groups_model->get_url($group->id));
		}
	}
	
	function delete_page_settings($slug, $page_id)
	{
		$this->require_login();
		
		$group = $this->groups_model->item(null, $slug);
		$id = $group->id;
		$message = NULL;
		
		if ($this->has_role('facilitator', $group->id) === false)
		{
			show_error('You are not a facilitator of this group');
		}
		
		$page = $this->pages_model->item($page_id, $group->id);
		
		$this->pages_model->delete($page_id);
		
		$this->session->set_flashdata('message', 'Your page has been deleted.');
		redirect($this->groups_model->get_url($group->id).'/settings/pages');
	}
	
	function attending_event($slug, $post_id)
	{
		if ($this->account === null)
		{
			redirect(getenv("HTTP_REFERER"));
		}
		
		$group  = $this->groups_model->item(null, $slug);
		$stream = $this->stream_posts_model->item($post_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			show_error('The group "'.$slug.'" does not exist.');
		}
		elseif ($stream === false || $stream->group_id !== $group->id)
		{
			show_error('Could not find stream post.');
		}
		else
		{
			$this->stream_posts_model->attend_event($post_id, $this->account->id);
			
			//send notification to user that created event
			$member = $this->users->item($stream->user_id);
			if ($member)
			{
				$short_message = $this->account->first_name.' '.$this->account->last_name.' is attending your <a href="'.site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id)).'">event</a> in <a href="'.site_url($this->groups_model->get_url($group->id)).'">'.$group->name.'</a>.';
				$message = serialize(array(
					'special' => 'attending_event',
					'posted_by' => $this->account->first_name.' '.$this->account->last_name,
					'subject' => $this->account->first_name.' '.$this->account->last_name.' is attending '.$stream->subject,
					'content' => $stream->content,
					'created_at' => $stream->created_at,
					'link' => site_url($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id))
				));
				
				if ($member->id !== $this->account->id)
				{
					$this->notifications_model->create($member->id, $group->id, 'event', $this->account->first_name.' '.$this->account->last_name.' is attending '.$stream->subject, $message, $short_message);
				}
			}
			redirect(getenv("HTTP_REFERER"));
		}
	}
	
	function not_attending_event($slug, $post_id)
	{
		if ($this->account === null)
		{
			redirect(getenv("HTTP_REFERER"));
		}
		
		$group  = $this->groups_model->item(null, $slug);
		$stream = $this->stream_posts_model->item($post_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			show_error('The group "'.$slug.'" does not exist.');
		}
		elseif ($stream === false || $stream->group_id !== $group->id)
		{
			show_error('Could not find stream post.');
		}
		else
		{
			$this->stream_posts_model->not_attend_event($post_id, $this->account->id);
			redirect(getenv("HTTP_REFERER"));
		}
	}
	
	function delete_post($slug, $post_id)
	{
		$group  = $this->groups_model->item(null, $slug);
		$stream = $this->stream_posts_model->item($post_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			
		}
		elseif ($stream === false || $stream->group_id !== $group->id)
		{
			
		}
		elseif ($this->account !== null)
		{
			if ($stream->user_id === $this->account->id || $this->has_role('facilitator', $group->id))
			{
				$this->stream_posts_model->delete($post_id);
			}
		}
		redirect(getenv("HTTP_REFERER"));
	}
	
	function stick_post($slug, $post_id)
	{
		$group  = $this->groups_model->item(null, $slug);
		$stream = $this->stream_posts_model->item($post_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			
		}
		elseif ($stream === false || $stream->group_id !== $group->id)
		{
			
		}
		elseif ($this->account !== null && $this->has_role('facilitator', $group->id))
		{
			$this->stream_posts_model->stick($group->id, $post_id);
		}
		redirect(getenv("HTTP_REFERER"));
	}
	
	function unstick_post($slug, $post_id)
	{
		$group  = $this->groups_model->item(null, $slug);
		$stream = $this->stream_posts_model->item($post_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			
		}
		elseif ($stream === false || $stream->group_id !== $group->id)
		{
			
		}
		elseif ($this->account !== null && $this->has_role('facilitator', $group->id))
		{
			$this->stream_posts_model->unstick($post_id);
		}
		redirect(getenv("HTTP_REFERER"));
	}
	
	function delete_response($slug, $response_id)
	{
		$group  = $this->groups_model->item(null, $slug);
		$response = $this->stream_replies_model->item($response_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			
		}
		elseif ($this->account !== null);
		{
			if ($response->user_id === $this->account->id || $this->has_role('facilitator', $group->id))
			{
				$this->stream_replies_model->delete($response_id);
			}
		}
		redirect(getenv("HTTP_REFERER"));
	}
	
	function remove_shared($slug, $post_id)
	{
		$group  = $this->groups_model->item(null, $slug);
		$stream = $this->stream_posts_model->item($post_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			
		}
		elseif ($stream === false)
		{
			
		}
		elseif ($this->account !== null);
		{
			if ($stream->user_id === $this->account->id || $this->has_role('facilitator', $group->id))
			{
				$this->db->where(array(
					'stream_post_id' => $post_id,
					'group_id'       => $group->id
				))->delete('shared_stream_posts');
			}
		}
		redirect(getenv("HTTP_REFERER"));
	}
	
	function show_post($slug, $post_id)
	{
		$group  = $this->groups_model->item(null, $slug);
		$stream = $this->stream_posts_model->item($post_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			show_error('The group "'.$slug.'" does not exist.');
		}
		elseif ($stream === false || $stream->group_id !== $group->id)
		{
			redirect($this->groups_model->get_url($group->id));
			//show_error('Could not find stream post.');
		}
		elseif (($this->account === null && $group->is_public == 0) || ($group->is_public == 0 && $this->account !== null && !$this->groups_model->belongs_to_group($group->id, $this->account->id)))
		{
			show_error('This post is private.');
		}
		else
		{
			if (isset($_POST) && isset($_POST['stream_post_id']))
			{
				$post_id = $_POST['stream_post_id'];
				$content = $_POST['content'];
				
				$this->stream_replies_model->reply($post_id, $this->account->id, $content);
				$this->session->set_flashdata('message', 'Your reply has been posted.');
				
				//send notifications to posted
				$post = $this->stream_posts_model->item($post_id);
				if ($post->user_id !== $this->account->id)
				{
					$this->notifications_model->create($post->user_id, $group->id, $post->type, $this->account->first_name.' '.$this->account->last_name.' Replied to Your '.ucfirst($post->type).' in '.$group->name, serialize(array(
						'special' => 'reply',
						'posted_by' => $this->account->first_name.' '.$this->account->last_name,
						'subject' => $this->account->first_name.' '.$this->account->last_name.' Replied to Your '.ucfirst($post->type),
						'content' => $content,
						'type' => $post->type,
						'created_at' => time(),
						'link' => site_url($this->groups_model->get_url($post->group_id).'/p/'.$post->id)
					)), $this->account->first_name.' '.$this->account->last_name.' replied to your <a href="'.site_url($this->groups_model->get_url($post->group_id).'/p/'.$post->id).'">'.$post->type.'</a> in <a href="'.site_url($this->groups_model->get_url($post->group_id)).'">'.$group->name.'</a>');
				}
				
				redirect($this->groups_model->get_url($group->id).'/p/'.$post_id);
			}
			
			// get campuses associated with a master group
			$campuses = null;
			if ($group->type === 'master')
			{
				$campuses = $this->db->where('type', 'campus')->where('campus_id', $group->id)->get('groups');
				if ($campuses->num_rows() === 0)
				{
					$campuses = null;
				}
			}
			
			// get church and small groups associated with a campus
			$church       = null;
			$small_groups = null;
			if ($group->type === 'campus')
			{
				$church       = $this->db->where('type', 'master')->where('id', $group->campus_id)->get('groups');
				if ($church->num_rows() === 1)
				{
					$church   = $church->row(0);
				}
				else
				{
					$church   = null;
				}
				if ($church !== null)
				{
					$campuses = $this->db->where('type', 'campus')->where('campus_id', $church->id)->where('id', '!='.$group->id)->get('groups');
					if ($campuses->num_rows() === 0)
					{
						$campuses = null;
					}
				}
				$small_groups = $this->db->where('type', 'small group')->where('campus_id', $group->id)->limit(15)->get('groups');
				if ($small_groups->num_rows() === 0)
				{
					$small_groups = null;
				}
			}
			
			// get campus associated with a small group
			$campus = null;
			if ($group->type === 'small group')
			{
				$campus       = $this->db->where('id', $group->campus_id)->get('groups');
				if ($campus->num_rows() === 1)
				{
					$campus   = $campus->row(0);
				}
				else
				{
					$campus   = null;
				}
				if ($campus !== null)
				{
					$church       = $this->db->where('type', 'master')->where('id', $campus->id)->get('groups');
					if ($church->num_rows() === 1)
					{
						$church   = $church->row(0);
					}
					else
					{
						$church   = null;
					}
				}
			}
			
			$groups_for_user = null;
			if ($this->account !== null)
			{
				$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
			}
			
			$this->load->vars(array(
				'title'			  => $group->name . ' &raquo; ' . $stream->subject,
				'message'         => $this->session->flashdata('message'),
				'error'           => $this->session->flashdata('error'),
				'account'         => $this->account,
				'groups_for_user' => $groups_for_user,
				'group'           => $group,
				'campuses'        => $campuses,
				'church'          => $church,
				'campus'          => $campus,
				'small_groups'    => $small_groups,
				'stream'		  => $stream,
				'thisPage'		  => site_url($this->groups_model->get_url($group->id)),
				'css_files'       => array(
					'/resources/css/layout.css',
					'/resources/css/group_page.css',
					'/resources/css/dateselector.jf.css'
				),
				'js_files'        => array(
					'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
					'/resources/js/ccc.js',
					'/resources/js/connect.js',
					'/resources/js/group_page.js',
					'/resources/js/dateselector.jf.js'
				)
			));
			
			//$this->load->view('connect/group/head');
			$this->load->view('general/head');
			$this->load->view('connect/group/post');
			$this->load->view('general/foot');
		}
	}
	
	function edit_post($slug, $post_id)
	{
		$group  = $this->groups_model->item(null, $slug);
		$stream = $this->stream_posts_model->item($post_id);
		
		$this->load->helper('date');
		
		if ($group === false)
		{
			show_error('The group "'.$slug.'" does not exist.');
		}
		elseif ($stream === false || $stream->group_id !== $group->id)
		{
			redirect($this->groups_model->get_url($group->id));
			//show_error('Could not find stream post.');
		}
		else
		{
			if (($group->id === $stream->group_id && ($stream->user_id === $this->account->id || $this->users->is_role($this->account->id, $group->id, 'facilitator'))) === false)
			{
				redirect($this->groups_model->get_url($group->id));
			}
			if ($_POST)
			{
				$this->db->where('id', $stream->id)->update('stream_posts', array(
					'subject' => $_POST['subject'],
					'content' => $_POST['content']
				));
				$this->session->set_flashdata('message', 'Post has been saved.');
				redirect($this->groups_model->get_url($group->id).'/p/'.($stream->slug ? $stream->slug : $stream->id));
			}
			
			// get campuses associated with a master group
			$campuses = null;
			if ($group->type === 'master')
			{
				$campuses = $this->db->where('type', 'campus')->where('campus_id', $group->id)->get('groups');
				if ($campuses->num_rows() === 0)
				{
					$campuses = null;
				}
			}
			
			// get church and small groups associated with a campus
			$church       = null;
			$small_groups = null;
			if ($group->type === 'campus')
			{
				$church       = $this->db->where('type', 'master')->where('id', $group->campus_id)->get('groups');
				if ($church->num_rows() === 1)
				{
					$church   = $church->row(0);
				}
				else
				{
					$church   = null;
				}
				if ($church !== null)
				{
					$campuses = $this->db->where('type', 'campus')->where('campus_id', $church->id)->where('id', '!='.$group->id)->get('groups');
					if ($campuses->num_rows() === 0)
					{
						$campuses = null;
					}
				}
				$small_groups = $this->db->where('type', 'small group')->where('campus_id', $group->id)->limit(15)->get('groups');
				if ($small_groups->num_rows() === 0)
				{
					$small_groups = null;
				}
			}
			
			// get campus associated with a small group
			$campus = null;
			if ($group->type === 'small group')
			{
				$campus       = $this->db->where('id', $group->campus_id)->get('groups');
				if ($campus->num_rows() === 1)
				{
					$campus   = $campus->row(0);
				}
				else
				{
					$campus   = null;
				}
				if ($campus !== null)
				{
					$church       = $this->db->where('type', 'master')->where('id', $campus->id)->get('groups');
					if ($church->num_rows() === 1)
					{
						$church   = $church->row(0);
					}
					else
					{
						$church   = null;
					}
				}
			}
			
			$groups_for_user = null;
			if ($this->account !== null)
			{
				$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
			}
			
			$this->load->vars(array(
				'title'			  => $group->name . ' &raquo; ' . $stream->subject,
				'message'         => $this->session->flashdata('message'),
				'error'           => $this->session->flashdata('error'),
				'account'         => $this->account,
				'groups_for_user' => $groups_for_user,
				'group'           => $group,
				'campuses'        => $campuses,
				'church'          => $church,
				'campus'          => $campus,
				'small_groups'    => $small_groups,
				'stream'		  => $stream,
				'thisPage'		  => site_url($this->groups_model->get_url($group->id)),
				'css_files'       => array(
					'/resources/css/layout.css',
					'/resources/css/group_page.css',
					'/resources/css/dateselector.jf.css'
				),
				'js_files'        => array(
					'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
					'/resources/js/ccc.js',
					'/resources/js/connect.js',
					'/resources/js/group_page.js',
					'/resources/js/dateselector.jf.js'
				)
			));
			
			//$this->load->view('connect/group/head');
			$this->load->view('general/head');
			$this->load->view('connect/group/post_edit');
			$this->load->view('general/foot');
		}
	}
	
	function show($slug)
	{
		
		$this->load->helper('date');
		
		$group = $this->groups_model->item(null, $slug);
		if ($group !== false)
		{
		
			if ($_POST)
			{
				if (isset($_POST['stream_post_id']))
				{
					$post_id = $_POST['stream_post_id'];
					$content = $_POST['content'];
					
					$this->stream_replies_model->reply($post_id, $this->account->id, $content);
					$this->session->set_flashdata('message', 'Your reply has been posted.');
					
					//send notifications to posted
					$post = $this->stream_posts_model->item($post_id);
					if ($post->user_id !== $this->account->id)
					{
						$this->notifications_model->create($post->user_id, $group->id, $post->type, $this->account->first_name.' '.$this->account->last_name.' Replied to Your '.ucfirst($post->type).' in '.$group->name, serialize(array(
							'special' => 'reply',
							'posted_by' => $this->account->first_name.' '.$this->account->last_name,
							'subject' => $this->account->first_name.' '.$this->account->last_name.' Replied to Your '.ucfirst($post->type),
							'content' => $content,
							'type' => $post->type,
							'created_at' => time(),
							'link' => site_url($this->groups_model->get_url($post->group_id).'/p/'.$post->id)
						)), $this->account->first_name.' '.$this->account->last_name.' replied to your <a href="'.site_url($this->groups_model->get_url($post->group_id).'/p/'.$post->id).'">'.$post->type.'</a> in <a href="'.site_url($this->groups_model->get_url($post->group_id)).'">'.$group->name.'</a>');
					}
					/*$members = $this->db->where('group_id', $group->id)->get('groups_users');
					$message = serialize(array(
						'posted_by' => $this->account->first_name.' '.$this->account->last_name,
						'subject' => $type === 'prayer' ? 'Prayer Posted by '.$this->account->first_name.' '.$this->account->last_name : $this->stream_posts_model->item($post)->subject,
						'created_at' => $this->stream_posts_model->item($post)->created_at,
						'link' => site_url($this->groups_model->get_url($group->id).'/p/'.$post)
					));
					$short_message = $this->account->first_name.' '.$this->account->last_name.' replied to a <a href="'.site_url($this->groups_model->get_url($group->id).'/p/'.$post).'">'.$type.'</a> to <a href="'.site_url($this->groups_model->get_url($group->id)).'">'.$group->name.'</a>.';
					foreach ($members->result() as $member)
					{
						if ($member->user_id !== $this->account->id)
						{
							$this->notifications_model->create($member->user_id, $group->id, $type, ($type === 'prayer' ? 'Prayer Posted by '.$this->account->first_name.' '.$this->account->last_name : $this->stream_posts_model->item($post)->subject), $message, $short_message);
						}
					}*/
					
					redirect($this->groups_model->get_url($group->id).'#post-'.$post_id);
				}
				else
				{
					if ($this->account !== null)
					{
						$content = $_POST['content'];
						$type    = $_POST['type'];
						if ($type !== 'prayer')
						{
							$subject = $_POST['subject'];
						}
						if ($type === 'event')
						{
							$event_date = strtotime($_POST['event_date']);
						}
						
						if ($content && $type)
						{
							switch ($type)
							{
								case 'news':
									$post = $this->stream_posts_model->post_news($this->account->id, $group->id, $subject, $content);
									$this->files_model->upload('file', $post, 'post_image', 'user_images/post_images', $error, 'jpg|png|jpeg');
									break;
								case 'event':
									$post = $this->stream_posts_model->post_event($this->account->id, $group->id, $subject, $content, $event_date);
									$this->files_model->upload('file', $post, 'post_image', 'user_images/post_images', $error, 'jpg|png|jpeg');
									break;
								case 'discussion':
									$post = $this->stream_posts_model->post_discussion($this->account->id, $group->id, $subject, $content);
									break;
								case 'contribution':
									$post = $this->stream_posts_model->post_contribution($this->account->id, $group->id, $subject, $content);
									break;
								case 'prayer':
									$post = $this->stream_posts_model->post_prayer($this->account->id, $group->id, $content);
									break;
								case 'qna':
									$post = $this->stream_posts_model->post_qna($this->account->id, $group->id, $subject, $content);
									$type = 'question';
									break;
							}
							
							if ($post !== false) {
								$this->session->set_flashdata('message', 'Your '.$type.' has been posted.');
								
								//send notifications to all members
								$members = $this->db->where('group_id', $group->id)->get('groups_users');
								$message = serialize(array(
									'posted_by' => $this->account->first_name.' '.$this->account->last_name,
									'subject' => $type === 'prayer' ? 'Prayer Posted by '.$this->account->first_name.' '.$this->account->last_name : $this->stream_posts_model->item($post)->subject,
									'content' => $this->stream_posts_model->item($post)->content,
									'created_at' => $this->stream_posts_model->item($post)->created_at,
									'link' => site_url($this->groups_model->get_url($group->id).'/p/'.$post)
								));
								$short_message = $this->account->first_name.' '.$this->account->last_name.' posted a <a href="'.site_url($this->groups_model->get_url($group->id).'/p/'.$post).'">'.$type.'</a> to <a href="'.site_url($this->groups_model->get_url($group->id)).'">'.$group->name.'</a>.';
								foreach ($members->result() as $member)
								{
									if ($member->user_id !== $this->account->id)
									{
										$this->notifications_model->create($member->user_id, $group->id, $type, ($type === 'prayer' ? 'Prayer Posted by '.$this->account->first_name.' '.$this->account->last_name : $this->stream_posts_model->item($post)->subject), $message, $short_message);
									}
								}
								
								redirect($this->groups_model->get_url($group->id).'#post-'.$post);
							}
							else {
								
							}
						}
					}
				}
			}
			
			// get campuses associated with a master group
			$campuses = null;
			if ($group->type === 'master')
			{
				$campuses = $this->db->where('type', 'campus')->where('campus_id', $group->id)->get('groups');
				if ($campuses->num_rows() === 0)
				{
					$campuses = null;
				}
			}
			
			// get church and small groups associated with a campus
			$church       = null;
			$small_groups = null;
			if ($group->type === 'campus')
			{
				$church       = $this->groups_model->item($group->campus_id);//$this->db->where('type', 'master')->where('id', $group->campus_id)->get('groups');
				if ($church !== false && $church->type == 'master')
				{
					//$church   = $church->row(0);
				}
				else
				{
					$church   = null;
				}
				if ($church !== null)
				{
					$campuses = $this->db->where('type', 'campus')->where('campus_id', $church->id)->where('id', '!='.$group->id)->get('groups');
					if ($campuses->num_rows() === 0)
					{
						$campuses = null;
					}
				}
				$small_groups = $this->db->where('type', 'small group')->where('campus_id', $group->id)->limit(15)->get('groups');
				if ($small_groups->num_rows() === 0)
				{
					$small_groups = null;
				}
			}
			
			// get campus associated with a small group
			$campus = null;
			if ($group->type === 'small group')
			{
				$campus       = $this->groups_model->item($group->campus_id);
				if ($campus === false) $campus = null;
				if ($campus !== null)
				{
					$church       = $this->groups_model->item($campus->id);
					if ($church !== false && $church->type == 'master')
					{
						$church   = $church->row(0);
					}
					else
					{
						$church   = null;
					}
				}
			}
			
			$groups_for_user = null;
			if ($this->account !== null)
			{
				$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
			}
			
			$types = array('none');
			
			if ($group->hide_news == 0) $types[] = 'news';
			if ($group->hide_events == 0) $types[] = 'event';
			if ($group->hide_contributions == 0) $types[] = 'contribution';
			if ($group->hide_discussion == 0) $types[] = 'discussion';
			if ($group->hide_prayers == 0) $types[] = 'prayer';
			if ($group->hide_qna == 0) $types[] = 'qna';
			
			$pages = $this->pages_model->items_in_sidebar($group->type === 'master' ? 0 : $group->id);
			
			$count = $this->stream_posts_model->count_items($group->id, $types);
			$pages_count = ceil($count / 25);
			if (isset($_GET['page']) && $_GET['page'] && $_GET['page'] > $pages_count)
			{
				redirect($this->groups_model->get_url($group->id).'?page='.($_GET['page'] - 1));
			}
			
			$this->load->vars(array(
				'title'			  => $group->name,
				'message'         => $this->session->flashdata('message'),
				'error'           => $this->session->flashdata('error'),
				'account'         => $this->account,
				'groups_for_user' => $groups_for_user,
				'group'           => $group,
				'campuses'        => $campuses,
				'church'          => $church,
				'campus'          => $campus,
				'sticky'          => $this->stream_posts_model->sticky_item_in_group($group->id),
				'streams'	      => $this->account !== null && $this->groups_model->belongs_to_group($group->id, $this->account->id) ? ($this->stream_posts_model->items($group->id, $types, isset($_GET['page']) && $_GET['page'] ? $_GET['page'] : 1)->result()) : ($this->stream_posts_model->items($group->id, array('news', 'event', 'contribution'), isset($_GET['page']) && $_GET['page'] ? $_GET['page'] : 1)->result()),
				'types'			  => $types,
				'count_streams'   => $this->stream_posts_model->count_items($group->id, $types),
				'pages_count'	  => $pages_count,
				'small_groups'    => $small_groups,
				'pages'			  => $pages,
				'thisPage'		  => site_url($this->groups_model->get_url($group->id)),
				'css_files'       => array(
					'/resources/css/layout.css',
					'/resources/css/group_page.css',
					'/resources/css/dateselector.jf.css'
				),
				'js_files'        => array(
					'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
					'/resources/js/ccc.js',
					'/resources/js/connect.js',
					'/resources/js/group_page.js',
					'/resources/js/dateselector.jf.js'
				)
			));
			if ($group->type === 'campus' || $group->type === 'ministry')
			{
				$this->load->vars(array(
					'rss_files' => array(site_url($this->groups_model->get_url($group->id).'/rss'))
				));
			}
			
			//$this->load->view('connect/group/head');
			$this->load->view('general/head');
			$this->load->view('connect/group/stream_navigation');
			$this->load->view('general/foot');
			//$this->load->view('connect/group/foot');
		}
		else
		{
			show_error('The group "'.$slug.'" does not exist.');
		}
	}
	
	function join($slug)
	{
		$group = $this->groups_model->item(null, $slug);
		if ($group !== false)
		{
			if ($this->account === null)
			{
				redirect($this->groups_model->get_url($group->id));
			}
			$check = ($group->requires_member_approval == 1 && $this->db->where('user_id', $this->account->id)->where('group_id', $group->id)->where('approved', 0)->get('groups_users')->num_rows()) || $group->requires_member_approval == 0;
			//if ($check)
			//{
				$try = $this->groups_model->assign_user($this->account->id, $group->id);
				if ($try)
				{
					$this->session->set_flashdata('message', 'You have now joined '.$group->name.'. You will receive an email each time new content is posted. To change your email settings, <a href="'.($this->groups_model->get_url($group->id).'/group_settings').'">click here</a>.');
				}
				else
				{
					if ($group->requires_member_approval == 1)
					{
						$this->session->set_flashdata('message', '<strong>'.$group->name.' requires all members to be approved. You will be contacted shortly.</strong>');
					}
					elseif ($this->groups_model->belongs_to_group($group->id, $this->account->id))
					{
						$this->session->set_flashdata('error', '<strong>Unable to join '.$group->name.'</strong>'.('<br /> You are already a member of this '.($group->type !== 'master' ? $group->type : 'church').'!'));
					}
					else
					{
						$this->session->set_flashdata('error', '<strong>Unable to join '.$group->name.'</strong>'.($group->type === 'campus' ? '<br /> This could be because you belong to another campus.' : ''));
					}
				}
				redirect($this->groups_model->get_url($group->id));
			/*}
			else
			{
				$this->session->set_flashdata('message', '<strong>'.$group->name.' requires all members to be approved. You will be contacted shortly.</strong>');
				redirect($this->groups_model->get_url($group->id));
			}*/
		}
	}
	
	function show_page($group_slug, $page_slug)
	{
		$group = $this->groups_model->item(null, $group_slug);
		if (!$group->is_public && !$this->groups_model->belongs_to_group($group->id, $this->account->id)) redirect($this->groups_model->get_url($group->id));
		if ($group !== false)
		{
			$page = $this->pages_model->item_by_slug($page_slug, $group->type === 'master' ? 0 : $group->id);
			if ($page === false) redirect($this->groups_model->get_url($group->id));
			
			// get campuses associated with a master group
			$campuses = null;
			if ($group->type === 'master')
			{
				$campuses = $this->db->where('type', 'campus')->where('campus_id', $group->id)->get('groups');
				if ($campuses->num_rows() === 0)
				{
					$campuses = null;
				}
			}
			
			// get church and small groups associated with a campus
			$church       = null;
			$small_groups = null;
			if ($group->type === 'campus')
			{
				$church       = $this->db->where('type', 'master')->where('id', $group->campus_id)->get('groups');
				if ($church->num_rows() === 1)
				{
					$church   = $church->row(0);
				}
				else
				{
					$church   = null;
				}
				if ($church !== null)
				{
					$campuses = $this->db->where('type', 'campus')->where('campus_id', $church->id)->where('id', '!='.$group->id)->get('groups');
					if ($campuses->num_rows() === 0)
					{
						$campuses = null;
					}
				}
				$small_groups = $this->db->where('type', 'small group')->where('campus_id', $group->id)->limit(15)->get('groups');
				if ($small_groups->num_rows() === 0)
				{
					$small_groups = null;
				}
			}
			
			// get campus associated with a small group
			$campus = null;
			if ($group->type === 'small group')
			{
				$campus       = $this->db->where('id', $group->campus_id)->get('groups');
				if ($campus->num_rows() === 1)
				{
					$campus   = $campus->row(0);
				}
				else
				{
					$campus   = null;
				}
				if ($campus !== null)
				{
					$church       = $this->db->where('type', 'master')->where('id', $campus->id)->get('groups');
					if ($church->num_rows() === 1)
					{
						$church   = $church->row(0);
					}
					else
					{
						$church   = null;
					}
				}
			}
			
			$groups_for_user = null;
			if ($this->account !== null)
			{
				$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
			}
			
			$pages = $this->pages_model->items_in_sidebar($group->id);
			
			$this->load->vars(array(
				'title'			  => $group->name . ' &raquo; '.$page->title,
				'message'         => $this->session->flashdata('message'),
				'error'           => $this->session->flashdata('error'),
				'account'         => $this->account,
				'groups_for_user' => $groups_for_user,
				'group'           => $group,
				'campuses'        => $campuses,
				'church'          => $church,
				'campus'          => $campus,
				'small_groups'    => $small_groups,
				'page'			  => $page,
				'pages'			  => $pages,
				'thisPage'		  => site_url($this->groups_model->get_url($group->id)),
				'css_files'       => array(
					'/resources/css/layout.css',
					'/resources/css/group_page.css',
					'/resources/css/dateselector.jf.css'
				),
				'js_files'        => array(
					'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
					'/resources/js/ccc.js',
					'/resources/js/connect.js',
					'/resources/js/group_page.js',
					'/resources/js/dateselector.jf.js'
				)
			));
			
			$this->load->view('general/head');
			if ($page->group_id == 0)
			{
				$this->load->view('connect/group/standalone_page');
			}
			elseif ($page->type == 'giving')
			{
				$this->load->view('connect/group/giving');
			}
			else
			{
				$this->load->view('connect/group/page');
			}
			$this->load->view('general/foot');
		}
		else
		{
			show_error('The group "'.$group_slug.'" does not exist.');
		}
	}
	
	function rss($slug)
	{
		$group = $this->groups_model->item(null, $slug);
		if ($group)
		{
			$items = $this->stream_posts_model->items($group->id);
			$this->load->vars(array(
				'group'   => $group,
				'streams' => $items
			));
			header('Content-type: application/xml');
			$this->load->view('connect/group/rss');
		}
		else
		{
			
		}
	}
	
	function finder()
	{
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		$this->load->vars(array(
			'title'			  => 'Small Group Finder',
			'message'         => $this->session->flashdata('message'),
			'error'           => $this->session->flashdata('error'),
			'account'         => $this->account,
			'groups_for_user' => $groups_for_user,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css',
				'/resources/css/finder.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/connect.js',
				'/resources/js/group_page.js',
				'/resources/js/dateselector.jf.js',
				'/resources/js/finder.js'
			)
		));
		$this->load->view('general/head');
		$this->load->view('connect/group/finder');
		$this->load->view('general/foot');
	}
	function finder_api()
	{
		$campus_id = isset($_GET['small_group_finder_campus']) ? $_GET['small_group_finder_campus'] : null;
		$category  = isset($_GET['small_group_finder_category']) ? $_GET['small_group_finder_category'] : null;
		$childcare = isset($_GET['small_group_finder_childcare']) ? $_GET['small_group_finder_childcare'] : null;
		$city      = isset($_GET['small_group_finder_city']) ? $_GET['small_group_finder_city'] : null;
		$day       = isset($_GET['small_group_finder_day']) ? $_GET['small_group_finder_day'] : null;
		
		$data = array();
		if ($campus_id) $data['campus_id']     = $campus_id;
		if ($category)  $data['category']      = $category;
		if ($childcare) $data['has_childcare'] = $childcare;
		if ($city)      $data['city']          = trim($city);
		if ($day)       $data['day_of_week']   = $day;
		
		if (count($data)) $this->db->where($data);
		$groups = $this->db->where('type', 'small group')->get('groups');
		header('Content-type: application/json');
		echo json_encode($groups->result_array());
	}
	
}