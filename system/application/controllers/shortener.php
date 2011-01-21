<?php

class Shortener extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->require_login();
	}
	
	function shorten()
	{
		$this->load->model('shortener_model');
		if ($_POST['url'])
		{
			$url = $this->shortener_model->shorten_url($_POST['url'], $_POST['tags']);
		}
	}
	
}