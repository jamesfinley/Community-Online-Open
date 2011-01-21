<?php

class Notes extends MY_Controller 
{	
	function index($note_id = null)
	{
		$this->require_login();
		
		$items = $this->notes_model->items(null, $this->account->id);
		
		$note = false;
		if ($note_id)
		{
			$note = $this->notes_model->item($note_id);
		}
		if ($note_id === null || $note === false)
		{
			$note = $items->row(0);
		}
		
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		$data = array(
			'notes'	 => 'Notes',
			'account' => $this->account,
			'groups_for_user' => $groups_for_user,
			'notes'   => $items,
			'note'    => $note,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/notes.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js'
			)
		);
		$this->load->vars($data);
		$this->load->view('general/head');
		$this->load->view('notes/list');
		$this->load->view('general/foot');
	}
	
	function delete($note_id)
	{
		$this->require_login();
		
		$note = $this->notes_model->item($note_id);
		if ($note === false) redirect('notes');
		if ($note->user_id !== $this->account->id) redirect('notes');
		
		$this->notes_model->delete($note_id);
		redirect('notes');
	}
	
	function api()
	{
		$data = array('error' => 'not logged in');
		if ($this->is_logged_in())
		{
			$data = array();
			$data['results'] = array();
			
			$items = $this->notes_model->items(null, $this->account->id, 5);
			$items = $items->result_array();
			
			for ($i=0, $len=count($items); $i<$len; $i++)
			{
				array_push($data['results'], array(
					'title' => $items[$i]['big_idea'].' ('.$items[$i]['series_title'].')',
					'link'  => site_url('notes/'.$items[$i]['id'])
				));
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($data);
	}
}