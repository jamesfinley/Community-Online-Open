<?php

class Twitter_Model extends Model {
	
	private $consumer_key = '6I8CdekJoQT8eHJGIHUKYA';
	private $consumer_secret = 'WC4TbwNJJ3Nqw8SWkI1zZPqKQwvrY3u8dZxZgTVw';
	
	function __construct()
	{
		parent::Model();
	}
	
	private function get_token($user_id)
	{
		$tokens = $this->db->where('user_id', $user_id)->get('twitter');
		if ($tokens->num_rows() === 1)
		{
			return $tokens->row();
		}
		return false;
	}
	
	private function save_token($user_id, $access_token, $access_token_secret)
	{
		//delete prior if it exists
		$this->db->where('user_id', $user_id)->delete('twitter');
		
		$this->db->insert('twitter', array(
			'user_id' => $user_id,
			'access_token' => $access_token,
			'access_token_secret' => $access_token_secret
		));
	}
	
	function has_access($user_id)
	{
		if ($this->get_token($user_id) !== false) return true;
		return false;
	}
	
	function auth($user_id, $redirect = null)
	{
		$tokens = $this->get_token($user_id);
		$access_token = null;
		$access_token_secret = null;
		if ($tokens !== false)
		{
			$access_token 			= $tokens->access_token;
			$access_token_secret 	= $tokens->access_token_secret;
		}
		$auth = $this->my_twitter->oauth($this->consumer_key, $this->consumer_secret, $access_token, $access_token_secret);
		if ( isset($auth['access_token']) && isset($auth['access_token_secret']) )
		{
			$this->save_token($user_id, $auth['access_token'], $auth['access_token_secret']);
			if ( isset($_GET['oauth_token']) )
			{
				redirect($redirect ? $redirect : $this->session->userdata('return_url'));
				return;
			}
		}
	}
	
	function tweet($user_id, $text)
	{
		$this->session->userdata('return_url', current_url());
		$this->auth($user_id);
		
		$this->my_twitter->call('statuses/update', array('status' => $text));
	}
	
}