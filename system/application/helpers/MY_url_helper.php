<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function site_url($uri = '', $ssl = false)
{
	$CI =& get_instance();
	$url = $CI->config->site_url($uri);
	if ($ssl)
	{
		$url = str_replace('http://', 'https://', $url);
	}
	return $url;
}