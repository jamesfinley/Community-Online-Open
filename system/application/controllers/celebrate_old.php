<?php

class Celebrate extends Controller
{
		function __construct()
		{
			parent::Controller();
			
			//check for account info
			$this->account = null;
			if ($this->session->userdata('account_email'))
			{
				if ($this->users->login($this->session->userdata('account_email'), $this->session->userdata('account_password')) === false)
				{
					$this->session->unset_userdata('account_email');
					$this->session->unset_userdata('account_password');
				}
				else
				{
					$this->account = $this->db->where('email', $this->session->userdata('account_email'))->select('id, email, first_name, last_name')->get('users')->row();
				}
			}
		}
		
		function index()
		{
			$groups_for_user = null;
			if ($this->account !== null)
			{
				$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
			}
						
			$this->load->vars(array(
				'message'         => $this->session->flashdata('message'),
				'error'           => $this->session->flashdata('error'),
				'account'         => $this->account,
				'groups_for_user' => $groups_for_user
			));
			
			$this->load->view('celebrate/head');
			$this->load->view('celebrate/foot');
		}
	
}