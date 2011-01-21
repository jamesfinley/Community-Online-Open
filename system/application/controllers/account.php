<?php

class account extends MY_Controller {

	function _remap()
	{
		if ($this->uri->segment(1) !== 'account')
		{
			if ($this->uri->segment(1) == 'login')
			{
				$this->login();
			}
			if ($this->uri->segment(1) == 'register')
			{
				$this->register();
			}
			if ($this->uri->segment(1) == 'reset')
			{
				$this->reset();
			}
		}
		else
		{
			switch($this->uri->segment(2))
			{
				case 'settings':
					$this->settings();
					break;
				case 'logout':
					$this->logout();
					break;
			}
		}
	}

	function settings()
	{
		if ( ! $this->account )
		{
			$this->require_login();	
		}
		
		if ( $_POST )
		{
			if ($_POST['password'] && $_POST['password'] !== $_POST['confirm_password'])
			{
				$_POST['password'] = null;
			}
			$this->users->update($this->account->id, $_POST['email'], $_POST['password'], $_POST['first_name'], $_POST['last_name']);
			
			if (isset($_FILES['avatar']))
			{
				$config['upload_path'] 		= 'user_images/avatars/';
				$config['allowed_types'] 	= 'gif|jpg|png';
				$config['overwrite']		= true;
				
				$ext  = $_FILES['avatar']['name'];
				$ext  = explode('.', $ext);
				$ext  = $ext[count($ext) - 1];
				$name = $this->account->id.'-'.$_POST['first_name'].'-'.$_POST['last_name'];
				$config['file_name'] = $name;
				$this->load->library('upload', $config);
				
				$this->upload->do_upload('avatar');
				
				$this->db->where('id', $this->account->id)->update('users', array(
					'avatar' => $name.'.'.$ext
				));
				$this->db->where('type', 'avatars')->delete('stored_images');
			}

			$this->session->set_flashdata('message', 'Updated user.');

			redirect('account/settings');
			return;		
		}
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}	
				
		$this->load->vars(array(
			'message'         => $this->session->flashdata('message'),
			'error'           => $this->session->flashdata('error'),
			'account'         => $this->account,
			'groups_for_user' => $groups_for_user,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/settings.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/dateselector.jf.js'
			)
		));	
	
		//$this->load->view('account/settings/head');
		$this->load->view('general/head');
		$this->load->view('account/settings/settings');
		$this->load->view('general/foot');
		//$this->load->view('account/settings/foot');	
	}

	function login()
	{
		if ($_GET && isset($_GET['redirect']))
		{
			$this->session->set_userdata('return_url', $_GET['redirect']);
		}

		//check for account
		if ($this->facebook_connect->has_account())
		{
			redirect('');
		}
		elseif ($this->facebook_connect->is_authorized())
		{
			redirect('register_with_facebook');
		}

		//check for login vars
		$error = '';
		if (isset($_POST['email']) && isset($_POST['password']))
		{
			if ($user_id = $this->users->login($_POST['email'], md5($_POST['password'])))
			{
				/*$this->session->set_userdata(array(
					'account_email'    => $_POST['email'],
					'account_password' => md5($_POST['password']),
					'user_id'		   => $user_id
				));*/
				$this->do_login($user_id, $_POST['email'], md5($_POST['password']));
								
				$this->session->set_flashdata('message', 'You are now logged in.');
				
				$url = $this->session->userdata('return_url');
				
				if ( $url )
				{
					$this->session->unset_userdata('return_url');
				
					redirect($url);
					return;
				}
				
				redirect('');
			}
			else {
				$error = 'Username/password incorrect.';
			}
		}
		
		$this->load->view('login/head');
		$this->load->view('login/login', array(
			'error' => $error
		));
		$this->load->view('login/foot');
	}
	
	function register()
	{
		if ( $_POST )
		{
			if ((isset($_GET['redirect']) && $_GET['redirect'] == 'watch' && $_POST['password']) || $_POST['password'] === $_POST['confirm_password'])
			{
				if ($_POST['email'] && $_POST['first_name'] && $_POST['last_name'])
				{
					$this->db->where('email', $_POST['email']);
					
					if ( $this->db->count_all_results('users') )
					{
						$this->session->set_flashdata('error', 'This email address has already been registered. Please use a different email or <a href="'.site_url('login').'">login</a>');
						redirect('register');
					}
						
					$id = $this->users->insert($_POST['email'], $_POST['password'], $_POST['first_name'], $_POST['last_name']);
					
					$this->do_login($id, $_POST['email'], md5($_POST['password']));
					
					if (isset($_GET['redirect']) && $_GET['redirect'] == 'watch')
					{
					}
					else
					{
						$this->session->set_flashdata('message', 'Thanks for creating an account on Community Online! The next step is to join the campus that you attend so you can begin participating in the conversation with other people. Go to your campus page below and then click "Join" -- you can also join Ministries and Small Groups to keep up with everything that\'s happening here at Community. Welcome!');
					}
					
					$message = serialize(array(
						'posted_by' => $_POST['first_name'].' '.$_POST['last_name'],
						'subject' => 'Welcome to Community Online',
						'created_at' => time(),
						'content' => 'Thanks for creating an account on Community Online where you can keep in touch with your small groups, experience live services online, and stay up-to-date with the things that interest you at Community Christian Church. It\'s the best way to experience church online.<br /><br />If you haven\'t done so already, visit your campus page and click "Join" to begin receiving notifications of new content and start discussions on your own. You can also join small groups and ministries to post your own prayer requests or discussion questions.  Be part of the conversation!<br /><br />At any time, you can adjust your email settings for each group by going to the group\'s page. And you can update your own account information here: https://communitychristian.org/account/settings<br /><br />Welcome!',
						'link' => site_url('')
					));
					$this->notifications_model->create($id, 0, 'register', 'Welcome to Community Online', $message, 'Welcome to Community Online');
					
					redirect((isset($_GET['redirect']) && $_GET['redirect'] == 'watch') ? 'watch' : 'locations');
				}
			}
		}

		$this->load->vars(array(
			'title'			  => 'Register',
			'message'         => $this->session->flashdata('message'),
			'error'           => $this->session->flashdata('error'),
			'account'         => $this->account,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/settings.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/dateselector.jf.js'
			)		
		));
	
		//$this->load->view('head');
		$this->load->view('general/head');
		$this->load->view('register');
		$this->load->view('general/foot');
		//$this->load->view('foot');
	}
	
	function register_with_facebook()
	{
		//check for account
		if ($this->facebook_connect->has_account())
		{
			redirect('');
		}
		elseif ($this->facebook_connect->is_authorized() === false)
		{
			redirect('login');
		}
		
		if ($_POST)
		{
			$this->users->create_with_facebook($this->facebook_connect->fb_user);
			redirect('');
		}
		
		$data = array(
			'fb_info' => $this->facebook_connect->user_info()
		);
		$this->load->vars($data);
		$this->load->view('head');
		$this->load->view('register_with_facebook');
		$this->load->view('foot');
	}
	
	function reset() 
	{
		$error = NULL;
		
		if ( $this->is_logged_in() )
		{
			redirect('');
		}
		
		$view = '';

		if ( isset($_POST['password'], $_POST['confirm_password'], $_GET['hash']) )
		{
			$password = $_POST['password'];
			$password_confirmation = $_POST['confirm_password'];
			
			if ( strlen($password) > 5 && $password == $password_confirmation )
			{
				// Save new password
				$hash = $_GET['hash'];
				$user = $this->users->item(NULL, $hash);				

				$this->do_login($user->id, $user->email, md5($password));
			
				$this->db->update('users', array('password'=>md5($password), 'reset_hash' => NULL), array('id'=>$user->id));
			
				redirect('');
			}
			else
			{
				// Display view stating that passwords are not equal
				$view = 'create_password';
				$error = 'Passwords must be equal.';
			}
		}
		else if ( isset($_GET['hash']) )
		{
			// Display View for confirming new passwords
			
			$hash = $_GET['hash'];
			
			$user = $this->users->item(NULL, $hash);

			$view = 'create_password';
		}
		else if ( isset($_POST['email']) )
		{
			$email = $_POST['email'];

			$hash_url = $this->users->generateResetHash($email);
			
			if ( $hash_url )
			{
				// Email $hash_url to $email		

				$view = $this->load->view('account/reset_password/email', array('hash_url'=>$hash_url), TRUE);
				
				$this->load->library('email');
			
				$config['mailtype'] = 'html';
				
				$this->email->initialize($config);
	
				$this->email->from('no-reply@communitychristian.org', 'Community Online');
				$this->email->to($email); 
				
				$this->email->subject('Community Online: Password Reset');
				$this->email->message($view);	
				
				$this->email->send();
				
				redirect('');
			}
			else
			{
				// Email address does not exist
				$error = "Email address does not exist";

				$view = 'request_email';
			}
		}
		else {
			// Display view that will request email address from person
			$view = 'request_email';
		}

		if ( $view )
		{
			$this->load->vars(array(
				'title'			  => 'Reset Password',
				'message'         => $this->session->flashdata('message'),
				'error'           => $error,
				'account'         => $this->account,
				'css_files'       => array(
					'/resources/css/layout.css',
					'/resources/css/settings.css',
					'/resources/css/dateselector.jf.css'
				),
				'js_files'        => array(
					'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
					'/resources/js/ccc.js',
					'/resources/js/dateselector.jf.js'
				)		
			));
		
			$this->load->view('general/head');
			$this->load->view('account/reset_password/'.$view);
			$this->load->view('general/foot');
		}
		else
		{
			show_404('page');
		}
	}
	
	function logout() {
		$this->do_logout();
		redirect('');
	}
	
}