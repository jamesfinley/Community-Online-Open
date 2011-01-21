<?php

class Shortener extends Model {

	private $shortener = 'http://3c.gs/';
	
	function shorten_url($url, $tag, $code = null)
	{
		//if code is given, check if code exists
		if ($code !== null)
		{
			$check = $this->db->where('code', $code)->get('short_urls');
			if ($check->num_rows() !== 0)
			{
				return false;
			}
		}
		
		//check to see if url is already in database
		$check = $this->db->where('url', $url)->get('short_urls');
		if ($check === 1)
		{
			return $this->shortener . $check->row()->code;
		}
		
		//insert url
		$this->db->insert('short_urls', array(
			'code'			=> $code ? $code : '',
			'url' 			=> $url,
			'tag' 			=> $tag,
			'created_at' 	=> time()
		));
		
		//if no code is given, create code
		if ($code === null)
		{
			$id = $this->db->insert_id();
			$code = dechex($id);
			$this->db->where('id', $id)->update('short_urls', array(
				'code' 		=> $code
			));
		}
		
		return $this->shortener . $code;
	}
	
}