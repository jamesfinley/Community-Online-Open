<?php

class Twitter extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->require_login();
	}

	function index()
	{
		$this->twitter_model->auth($this->account->id);
	}
	
	function authenticate()
	{
		$this->twitter_model->auth($this->account->id, 'twitter/authenticated');
	}
	
	function authenticated()
	{
		redirect('watch');
	}
	
	function tweet()
	{
		$tweet = $_POST['tweet'];
		$this->twitter_model->tweet($this->account->id, $tweet);
	}
	
}