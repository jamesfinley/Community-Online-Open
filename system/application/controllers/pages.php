<?php

class Pages extends MY_Controller {
	
	function locations()
	{
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		$this->load->vars(array(
			'title'			  => 'Locations',
			'message'         => $this->session->flashdata('message'),
			'error'           => $this->session->flashdata('error'),
			'account'         => $this->account,
			'locations'		  => $this->groups_model->items(0, 'campus'),
			'groups_for_user' => $groups_for_user,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/dateselector.jf.js'
			),
			'rss_files'		  => array(
				site_url('locations/rss')
			)
		));
		
		$this->load->view('general/head');
		$this->load->view('general/locations');
		$this->load->view('general/foot');
	}
	
	function locations_rss()
	{
		$groups = $this->groups_model->items(0, 'campus');
		$ids = array();
		foreach ($groups->result() as $group)
		{
			array_push($ids, $group->id);
		}
		
		$items = $this->stream_posts_model->items($ids);
		$this->load->vars(array(
			'title'       => 'Community Locations',
			'description' => '',
			'link'        => site_url('locations'),
			'streams'     => $items
		));
		header('Content-type: application/xml');
		$this->load->view('connect/group/rss');
	}
		
	function ministries()
	{
		$this->load->helper('date');
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		$this->db->where('campus_id', 0);
		$this->db->where('display_in_sidebar', 1);
		$groups = $this->groups_model->items(0, 'ministry');
	
		$this->load->vars(array(
			'title'			  => 'Ministries',
			'message'         => $this->session->flashdata('message'),
			'error'           => $this->session->flashdata('error'),
			'account'         => $this->account,
			'locations'		  => $this->groups_model->items(0, 'campus'),
			'streams'	      => $this->stream_posts_model->items(array(14, 1))->result(),
			'groups_for_user' => $groups_for_user,
			'ministries'	  => $groups,
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
			),
			'rss_files'		  => array(
				site_url('ministries/rss')
			)
		));
		
		$this->load->view('general/head');
		$this->load->view('general/ministries');
		$this->load->view('general/foot');
	}
	
	function ministries_rss()
	{
		$items = $this->stream_posts_model->items(array(14, 1));
		$this->load->vars(array(
			'title'       => 'Community Ministries',
			'description' => '',
			'link'        => site_url('ministries'),
			'streams'     => $items
		));
		header('Content-type: application/xml');
		$this->load->view('connect/group/rss');
	}
	
}