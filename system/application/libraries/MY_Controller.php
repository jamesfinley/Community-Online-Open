<?php

class MY_Controller extends Controller
{
	function __construct()
	{
		parent::Controller();
		
		//date_default_timezone_set('America/Chicago');
		
		//check for account info
		$this->account = null;
		if (get_cookie('co_email') && get_cookie('co_password'))
		{
			if ($this->users->login(get_cookie('co_email'), get_cookie('co_password')) === false)
			{
				$this->do_logout();
			}
			else
			{
				$this->account = $this->db->where('email', get_cookie('co_email'))->select('id, email, first_name, last_name, avatar')->get('users')->row();
				$this->users->mark_as_online($this->account->id);
			}
		}
		
		//$this->output->enable_profiler(TRUE);
		
		$this->cache_roles();
	}
	
	function require_ssl()
	{
		//check if URL is http not https
		if ($_SERVER["HTTPS"] != "on")
		{
			//if not redirect
			redirect(str_replace('http://', 'https://', current_url()));
		}
		return true;
	}
	
	function do_login($user_id, $email, $password)
	{
		set_cookie(array(
			'name' => 'co_user_id',
			'value' => $user_id,
			'expire' => 60*60*24*30 //one month
		));
		set_cookie(array(
			'name' => 'co_email',
			'value' => $email,
			'expire' => 60*60*24*30 //one month
		));
		set_cookie(array(
			'name' => 'co_password',
			'value' => $password,
			'expire' => 60*60*24*30 //one month
		));
	}
	
	function do_logout()
	{
		delete_cookie('co_password');
	}
	
	private function cache_roles()
	{
		$this->roles = $this->db->get('roles')->result();
	}
	
	function require_login()
	{
		if ($this->is_logged_in() === false)
		{
			$this->session->set_userdata('return_url', current_url());
			redirect('login');
		}
	}
	
	function is_logged_in()
	{
		if ($this->account !== null)
		{
			return true;
		}
		return false;
	}
	
	function require_role($role, $returnURL, $group_id = null)
	{
		//require login
		$this->require_login();
		
		if ($group_id !== null)
		{
			$this->db->where('group_id', $group_id);
		}
		
		$roles = $this->db->where('user_id', $this->account->id)->where('type', $role)->limit(1)->get('roles');
		if ($roles->num_rows() === 0)
		{
			redirect($returnURL);
		}
	}
	
	function has_role($role_type, $group_id = null)
	{
		/*if ($group_id !== null)
		{
			$this->db->where('group_id', $group_id);
		}
		
		$roles = $this->db->where('user_id', $this->account->id)->where('type', $role)->limit(1)->get('roles');
		if ($roles->num_rows() === 1)
		{
			return true;
		}
		return false;*/
		foreach($this->roles as $role)
		{
			if ($role->user_id = $this->account->id && (($group_id !== null && $role->group_id == $group_id) || $group_id === null) && $role->type == $role_type)
			{
				return true;
			}
		}
		return false;
		
	}
	
}

?>