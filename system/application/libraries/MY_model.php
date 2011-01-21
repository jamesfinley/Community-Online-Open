<?php

class MY_Model extends Model
{
	private $select = NULL;
	private $escape = NULL;

	function __construct()
	{
		parent::Model();
	}
	
	function begin()
	{
		$this->select();
	}
	
	function select($select = '*', $escape = NULL)
	{
		$this->select = $select;
		$this->escape = $escape;
	}
		
	function do_select($select = '*', $escape = NULL)
	{
		// Use Defaults
		if ( $this->select == NULL && $this->escape == NULL)
		{
			$this->db->select($select, $escape);
		}

		else
		{
			$this->db->select($this->select, $this->escape);		
		}
	}
	
	function end()
	{
		$this->select();
	}
}