<?php

class Home extends MY_Controller 
{	
	function index () {
		if (isset($_GET['import_from_joomla']))
		{
			$DB = $this->load->database('joomla', true);
			$users = $DB->where('block', 0)->where('lastvisitDate !=', '0000-00-00 00:00:00')->select('name, email, password, registerDate, lastvisitDate')->get('jos_users');
			$DB = $this->load->database('default', true);
			$i = 0;
			foreach ($users->result() as $user)
			{
				$name = explode(' ', $user->name);
				$data = array(
					'first_name'  => $name[0],
					'last_name'   => isset($name[1]) ? $name[1] : '',
					'email'       => $user->email,
					'password'    => $user->password,
					'last_on'     => strtotime($user->lastvisitDate),
					'from_joomla' => 1
				);
				if ($this->db->where('email', $user->email)->get('users')->num_rows() == 0)
				{
					$this->db->insert('users', $data);
					$i++;
				}
			}
			
			echo 'Imported '.$i.' users from Joomla.';
			
			return true;
		}
		if (isset($_GET['import_people_from_3cms']))
		{
			if ($_POST)
			{
				$import = $_POST['import'];
				$import = explode("\n", $import);
				$users_added_to_groups = 0;
				$total  = 0;
				for ($i=1; $i<count($import); $i++) 
				{
					$import[$i] = explode(', ', $import[$i]);
					if (isset($import[$i][1]))
					{
						//$users_added_to_groups++;
						$email = $import[$i][1];
						$group = $this->db->where('threecms_id', $import[$i][0])->get('groups');
						if ($group->num_rows())
						{
							$total++;
							$check = $this->db->where('email', $email)->get('users');
							if ($check->num_rows())
							{
								/*$user_id = $check->row()->id;
								$group = $this->db->where('threecms_id', $import[$i][0])->get('groups');
								
								if ($group->num_rows() > 0)
								{
									if ($this->db->where('group_id', $group->row()->id)->where('user_id', $user_id)->get('groups_users')->num_rows() === 0)
									{
										$this->db->insert('groups_users', array(
											'group_id' => $group->row()->id,
											'user_id'  => $user_id
										));
									}
									$this->db->insert('roles', array(
										'user_id' => $user_id,
										'group_id' => $group->row()->id,
										'type' => 'facilitator',
										'created_at' => time(),
										'updated_at' => time()
									));
									$users_added_to_groups++;
								}*/
								
							}
							else
							{
								$this->db->insert('users', array(
									'email' => $email
								));
								$user_id = $this->db->insert_id();
								$this->db->insert('groups_users', array(
									'group_id' => $group->row()->id,
									'user_id'  => $user_id
								));
								$this->db->insert('roles', array(
									'user_id' => $user_id,
									'group_id' => $group->row()->id,
									'type' => 'facilitator',
									'created_at' => time(),
									'updated_at' => time()
								));
								$users_added_to_groups++;
							}
						}
					}
					/*if (isset($import[$i][2]))
					{
						$email = $import[$i][2];
						$check = $this->db->where('email', $email)->get('users');
						if ($check->num_rows())
						{
							$user_id = $check->row()->id;
							if ($check->row()->threecms_id == 0)
							{
								$this->db->where('id', $user_id)->update('users', array(
									'threecms_id' => $import[$i][1]
								));
							}
							$group = $this->db->where('threecms_id', $import[$i][0])->get('groups');
							if ($group->num_rows() > 0)
							{
								if ($this->db->where('group_id', $group->row()->id)->where('user_id', $user_id)->get('groups_users')->num_rows() === 0)
								{
									$this->db->insert('groups_users', array(
										'group_id' => $group->row()->id,
										'user_id'  => $user_id
									));
									$users_added_to_groups++;
								}
							}
							//echo $import[$i][2].'<br />';
						}
					}*/
				}
				echo $users_added_to_groups.' users of '.$total.' added to groups.';
			}
			else
			{
				?>
				<form method="post">
					<textarea name="import"></textarea>
					<input type="submit" />
				</form>
				
				<?php
			}
			/*require_once 'SOAP/Client.php';
			$service = new SOAP_WSDL("https://dev.3cmshost.com/login.aspx?ReturnUrl=WebService/ExecuteReports.asmx&op=RunReport&user=3cms@communititychristian.org&pwd=3cms", array('timeout' => 0));
			$client  = $service->getProxy();
			print_r($client);
			$return  = $client->RunReport('', '', '', '', '', 'People_Export');
			echo $return;
			
			//$xml     = new SimpleXMLElement($return, LIBXML_NOCDATA);
			//print_r($xml);*/
			return true;
		}
		if (isset($_GET['import_groups_from_3cms']))
		{
			require_once 'SOAP/Client.php';
			$service = new SOAP_WSDL("https://cccprod.communitychristian.org/3cms_prod/WebService/ExecuteScript.asmx?wsdl", array('timeout' => 0));
			$client  = $service->getProxy();
			$return  = $client->Get_Groups(array());
			
			$xml     = new SimpleXMLElement($return, LIBXML_NOCDATA);
			$console = array();
			//$this->db->empty_table('jos_sg_group');
			foreach ($xml->group as $group) {
				$array = array();
				$id    = 0;
				foreach ($group->attributes() as $a => $b) {
					if ($a) {
						$id = (string)$b;
					}
				}
				if ($id !== 0) {
					$i = 0;
					if ($this->db->where('threecms_id', $id)->get('groups')->num_rows() === 0)
					{
						$array = array(
							'published'           => 1,
							'campus'              => $group->campus[0] ? (string)$group->campus[0] : '',
							'group_type'          => $group->type[0] ? (string)$group->type[0] : '',
							'category'            => $group->category[0] ? (string)$group->category[0] : '',
							'leader_name'         => $group->leader[0] ? (string)$group->leader[0] : '',
							'leader_email'        => $group->email[0] ? (string)$group->email[0] : '',
							'leader_phone'        => $group->phone[0] ? (string)$group->phone[0] : '',
							'host_street_address' => $group->street[0] ? (string)$group->street[0] : '',
							'host_apartment'      => $group->apt[0] ? (string)$group->apt[0] : '',
							'host_city'           => $group->city[0] ? (string)$group->city[0] : '',
							//'host_state'          => $group->state[0] ? (string)$group->state[0] : '',
							'host_zip_code'       => $group->postalcode[0] ? (string)$group->postalcode[0] : '',
							//'location'   => $group->location[0] ? (string)$group->location[0] : '',
							'latitude'            => $group->latitude[0] ? (string)$group->latitude[0] : '',
							'longitude'           => $group->longitude[0] ? (string)$group->longitude[0] : '',
							'topic'               => $group->topic[0] ? (string)$group->topic[0] : '',
							'day'                 => $group->day[0] ? (string)$group->day[0] : '',
							'meeting_time'        => $group->time[0] ? (string)$group->time[0] : '',
							'childcare'           => $group->childcare[0] ? (string)$group->childcare[0] : ''
						);
						
						$leader = explode(', ', $array['leader_name']);
						$leader = array($leader[1], $leader[0]);
						
						$slug = strtolower(implode('_', $leader).'s_group');
						$slug_check = $this->db->where('slug', $slug)->get('groups');
						if ($slug_check->num_rows() > 0)
						{
							$slug_check = $this->db->where('slug', $slug.'1')->get('groups');
							if ($slug_check->num_rows() > 0)
							{
								$slug_check = $this->db->where('slug', $slug.'2')->get('groups');
								if ($slug_check->num_rows() > 0)
								{
									$slug = $slug.'3';
								}
								else
								{
									$slug = $slug.'2';
								}
							}
							else
							{
								$slug = $slug.'1';
							}
						}
						$data = array(
							'threecms_id'   => $id,
							'type'          => 'small group',
							'name'          => implode(' ', $leader).'\'s Group',
							'slug'          => $slug,
							'address'       => $array['host_street_address'].($array['host_apartment'] ? ' Apt. '.$array['host_street_address'] : ''),
							'city'          => $array['host_city'],
							'zip_code'      => $array['host_zip_code'],
							'state'         => (string)$group->state[0],
							'country'       => 'United States of America',
							'latitude'      => $array['latitude'],
							'longitude'     => $array['longitude'],
							'category'      => $array['category'],
							'topic'         => $array['topic'],
							'day_of_week'   => $array['day'],
							'meeting_time'  => $array['meeting_time'],
							'has_childcare' => $array['childcare'],
							'is_public'     => 0,
							'requires_member_approval' => 1,
							'created_at'	=> time(),
							'updated_at'	=> time()
						);
						
						switch ($array['campus'])
						{
							case 'Carillon':
								$data['campus_id'] = 10;
							break;
							case 'East Aurora':
								$data['campus_id'] = 6;
							break;
							case 'Chicago':
								$data['campus_id'] = 21;
							break;
							case 'Montgomery':
								$data['campus_id'] = 9;
							break;
							case 'Nap Downtown':
								$data['campus_id'] = 4;
							break;
							case 'Nap Yellow Box':
								$data['campus_id'] = 2;
							break;
							case 'Plainfield':
								$data['campus_id'] = 13;
							break;
							case 'Romeoville':
								$data['campus_id'] = 7;
							break;
							case 'Shorewood':
								$data['campus_id'] = 8;
							break;
							case 'Yorkville':
								$data['campus_id'] = 11;
							break;
							case 'INACTIVE - PIL':
								$data = null;
							break;
						}
						
						if ($data !== null)
						{
							//$this->db->where('threecms_id', $id)->update('groups', $data);
							$this->db->insert('groups', $data);
							$i++;
						}
					}
					
					/*if ($this->_exists_in_database($id)) {
						//update data
						//$this->db->where('id', $id)->update('jos_sg_group', $array);
						array_push($console, 'Updated group with ID '.$id);
					}
					else {
						//insert data
						$array['id'] = $id;
						//$this->db->insert('jos_sg_group', $array);
						array_push($console, 'Inserted group with ID '.$id);
					}*/
				}
			}
			echo 'Imported '.$i.' groups from Joomla.';
			echo implode('<br />', $console);
			
			return true;
		}
		if (isset($_GET['send_all_user_email']))
		{
			$total_users = $this->db->query('SELECT id FROM users')->num_rows();
			
			$this->load->library('email');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			
			$rows = 0;
			$loops = 0;
			for ($i=0; $i<$total_users; $i += 15)
			{
				$users = $this->db->query("SELECT email FROM users WHERE email != '' LIMIT {$i}, 15");
				$rows += $users->num_rows();
				$emails = array();
				foreach ($users->result() as $user) {
					array_push($emails, $user->email);
				}
				array_push($emails, 'jamesfinley@gmail.com');
				
				$this->email->from('no-reply@communitychristian.org', 'Community Online');
				$this->email->to('no-reply@communitychristian.org');
				//$this->email->to('jamesfinley@gmail.com');
				$this->email->bcc($emails);
				
				$this->email->subject('New communitychristian.org');
				$this->email->message('You are receiving this email because you created an account on Community Christian Church\'s website at communitychristian.org — And we wanted you to know we have a brand new website experience that you won\'t want to miss. <br /><br />Introducing Community Online<br />We\'d like to invite you to login to our new website and experience church in a whole new way. <br />Looking for something? Subscribe to feeds from the campus or ministries you\'re interested in: the new homepage can be customized just for you. <br />In a small group already? Use our new group pages to facilitate discussion, post prayer requests, and share information with the other people in your group. <br />Out of town? Login on the weekends and experience a service online together with other people. <br /><br />We\'ve built the new site from the ground up to create a place where you can not only find information, but a real community of people who are finding their way back to God online. <br /><br />Forget your password? You can login or change your password here:<br />http://communitychristian.org/login<br /><br />Don\'t want these emails? Login to change your preferences:<br />http://communitychristian.org/account/settings');
				//$this->email->message($emails);
				if ($this->email->send()) {
					$loops++;
				}
			}
			echo $loops.' emails sent.';
			
			/*$this->load->library('email');
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			
			$this->email->from('no-reply@communitychristian.org', 'Community Online');
			$this->email->to('jamesfinley@gmail.com'); 
			
			$this->email->subject('New communitychristian.org');
			$this->email->message('You are receiving this email because you created an account on Community Christian Church’s website at communitychristian.org — And we wanted you to know we have a brand new website experience that you won’t want to miss. <br /><br />Introducing Community Online<br />We’d like to invite you to login to our new website and experience church in a whole new way. <br />Looking for something? Subscribe to feeds from the campus or ministries you’re interested in: the new homepage can be customized just for you. <br />In a small group already? Use our new group pages to facilitate discussion, post prayer requests, and share information with the other people in your group. <br />Out of town? Login on the weekends and experience a service online together with other people. <br /><br />We’ve built the new site from the ground up to create a place where you can not only find information, but a real community of people who are finding their way back to God online. <br /><br />Forget your password? You can login or change your password here:<br />http://communitychristian.org/login<br /><br />Don’t want these emails? Login to change your preferences:<br />http://communitychristian.org/account/settings');
			$this->email->send();*/
			
			//$results = $this->db->query("SELECT GROUP_CONCAT(DISTINCT email SEPARATOR '<br /> ') AS emails FROM users WHERE email != ''");
			//echo $results->row()->emails;
			
			return true;
		}
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
					
		$data = array(
			// Account
			'account'         => $this->account,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/celebrate.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css',
				'/resources/css/home.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/jquery.swfobject.min.js',
				'/resources/js/jquery.easing.js',
				'/resources/js/dateselector.jf.js',
				'/resources/js/ccc.js',
				'/resources/js/celebrate.js',
				'/resources/js/home.js',
				'/resources/js/jquery.swfobject.min.js',
				'/resources/js/flowplayer-3.2.4.min.js'
			),
						
			// Big Idea
			'adult_idea'	  => $this->big_idea_model->adult_idea(),
			'adult_videos'    => $this->big_idea_model->videos_for_tab(1),
			'student_idea'	  => $this->big_idea_model->student_idea(),
			'student_videos'  => $this->big_idea_model->videos_for_tab(2),
			'kid_idea'		  => $this->big_idea_model->kid_idea(),
			'kid_videos'      => $this->big_idea_model->videos_for_tab(3),
			
			'groups_for_user' => $groups_for_user,
			//Stream
			'streams'		  => $this->account !== null ? $this->stream_posts_model->personal_stream($this->account->id, array('news', 'events'))->result() : $this->stream_posts_model->items(array(1), array('news', 'event', 'contribution'), 1, 25)->result(),
			'sidebar_streams' => $this->account !== null ? $this->stream_posts_model->personal_stream($this->account->id, array('discussion', 'prayer'), 1, 10) : '',
			'show_replies'    => true
		);
		
		$this->load->vars($data);
		$this->load->view('general/head');
		$this->load->view('home/index');
		$this->load->view('general/foot');
	}
	
	function email()
	{
		$to       = $_POST['to'];
		$from     = $_POST['from'];
		$message  = $_POST['message'];
		$big_idea = $_POST['big_idea'];
		
		mail($to, 'You\'re Invited to Community Christian Church!', $message."\n\n".$big_idea."\n\nhttp://communitychristian.org", 'From: '.$from);
		
		echo 'Your email has been sent to '.$from.'.';
	}
	
	function timestampForCurrentWeek()
	{		
		if ( date('N') == 7 )
		{
			return strtotime('12:00am');
		}
		
		return strtotime('-1 Sunday');
		
	}
}