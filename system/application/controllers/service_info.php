<?php

class service_info extends Controller {
	
	function __construct()
	{
		parent::Controller();
		
		$this->load->model('services_model');
		$this->load->model('videos_model');
	}
	
	function _array2xml($data, $recursive = false)
	{
		$xml = '';
		if (!$recursive) $xml = '<?xml version="1.0" encoding="UTF-8"?><data>';
		foreach ($data as $key=>$obj)
		{
			if (is_array($obj))
			{
				$xml .= '<'.$key.'>';
				foreach ($obj as $obj2) {
					if (is_array($obj2))
					{
						$xml .= '<item>'.$this->_array2xml($obj2, true).'</item>';
					}
					else
					{
						$xml .= '<item>'.$obj2.'</item>';
					}
				}
				$xml .= '</'.$key.'>';
			}
			else
			{
				$xml .= '<'.$key.'>'.$obj.'</'.$key.'>';
			}
		}
		if (!$recursive) $xml .= '</data>';
		
		return $xml;
	}
	
	function index($id)
	{
		$service = $this->services_model->item_array($id);
		
		$data    = array('error' => 404);
		if ($service !== false)
		{
			$videos  = $this->services_model->videos_for_service($id);
			$service['videos'] = $videos;
			
			$content = unserialize($service['content']);
			$service['content'] = '';
			$service['dynamic_contents'] = $content;
			
			$data = $service;
			$data['error'] = 200;
		}
		
		$xml = $this->_array2xml($data);
		
		header('Content-type: text/xml');
		echo $xml;
	}
	
}