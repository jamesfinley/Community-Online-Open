<?php

class facebook_connect extends Model {

	function __construct()
	{
		parent::Model();
		
		$this->load->config('facebook');
		
		//create Facebook object 
		include_once 'facebook_connect/facebook.php';
		$this->fb = new Facebook($this->config->item('facebook_connect_api_key'), $this->config->item('facebook_connect_secret'));
		
		//get user
		$this->fb_user = $this->fb->get_loggedin_user();
		
		//check db for user
		$check = $this->db->where('uid', $this->fb_user)->limit(1)->get('facebook_connect');
		$this->user_account = false;
		if ($check->num_rows() === 1)
		{
			$this->user_account = $check->row();
		}
	}
	
	function is_authorized()
	{
		if ($this->fb_user)
		{
			return true;
		}
		return false;
	}
	
	function has_account()
	{
		if ($this->is_authorized() === false)
		{
			return false;
		}
		elseif ($this->user_account !== false)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function account()
	{
		if ($this->has_account())
		{
			return $this->user_account;
		}
		return false;
	}
	
	function user_info($uid = null)
	{
		$user_details = $this->fb->api_client->users_getInfo($uid ? $uid : $this->fb_user, 'last_name, first_name, current_location, locale, profile_url, pic_square_with_logo, sex, status, timezone');
		if ($user_details)
		{
			return $user_details[0];
		}
		return false;
	}
	
}