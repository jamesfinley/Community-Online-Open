<?php

function remove_control_characters($content)
{
	return preg_replace('/[^(\x20-\x7F)]*/','', $content);
}

/*
	html_truncate is a function from http://snippets.dzone.com/posts/show/7125
	
	The function truncates a string by character length. 
*/
function html_truncate($text, $length, $suffix = '&hellip;', $isHTML = true) {
	$i = 0;
	$tags = array();
	if($isHTML){
		preg_match_all('/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
		foreach($m as $o){
			if($o[0][1] - $i >= $length)
				break;
			$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
			if($t[0] != '/')
				$tags[] = $t;
			elseif(end($tags) == substr($t, 1))
				array_pop($tags);
			$i += $o[1][1] - $o[0][1];
		}
	}
	
	$output = substr($text, 0, $length = min(strlen($text),  $length + $i)) . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');
	if (strlen($text) > $length)
	{
		$output = substr($output,-4,4)=='</p>' ? $output=substr($output,0,(strlen($output)-4)).$suffix.'</p>' : $output.=$suffix;
	}
	return $output;
}

function find_url_and_return_data ($content)
{
	//find first url
	$pattern = '/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/';
	
	preg_match_all($pattern, $content, $matches);
	
	if (!$matches) return false;
	
	$urls = $matches[1];
	
	$data = array();
	foreach ($urls as $url)
	{
		$type  = 'flickr';
		$datum = get_data_from_flickr_url($url);
		if ($datum === false)
		{
			$type  = 'vimeo';
			$datum = get_data_from_vimeo_url($url);
		}
		if ($datum === false)
		{
			$type  = 'youtube';
			$datum = get_data_from_youtube_url($url);
		}
		if ($datum)
		{
			return array('type' => $type, 'data' => $datum);
		}
	}
	
	return null;
}

function find_urls_and_return_data ($content)
{
	//find first url
	$pattern = '/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/';
	
	preg_match_all($pattern, $content, $matches);
	
	if (!$matches) return false;
	
	$urls = $matches[1];
	
	$data = array();
	foreach ($urls as $url)
	{
		$datum = get_data_from_flickr_url($url);
		if ($datum === false)
		{
			$datum = get_data_from_vimeo_url($url);
		}
		if ($datum === false)
		{
			$datum = get_data_from_youtube_url($url);
		}
		if ($datum)
		{
			array_push($data, $datum);
		}
	}
	return $data;
}

function get_data_from_flickr_url ($url)
{
	preg_match("/flickr.com/", $url, $matches);
	
	if (!$matches) return false;
	
	if ($matches)
	{
		$matches = null;
		
		//find set
		preg_match('/sets\/([0-9]*)/', $url, $matches);
		if ($matches)
		{
			$id = $matches[1];
			
			$params = array(
				'api_key'     => '146e92436cd84ffe70cd0b9349e0fd10',
				'method'      => 'flickr.photosets.getPhotos',
				'photoset_id' => $id,
				'per_page'    => 5,
				'extras'      => 'url_sq, url_o, path_alias',
				'format'      => 'php_serial'
			);
			
			$encoded_params = array();
			
			foreach ($params as $k => $v)
			{
				$encoded_params[] = urlencode($k).'='.urlencode($v);
			}
			
			$url2 = "http://api.flickr.com/services/rest/?".implode('&', $encoded_params);
			$rsp = file_get_contents($url2);
			$rsp_obj = unserialize($rsp);
			
			if ($rsp_obj['stat'] == 'ok')
			{
				//extract photos
				$photoset  = $rsp_obj['photoset'];
				$photolist = $photoset['photo'];
				$photos    = array();
				foreach ($photolist as $photo)
				{
					array_push($photos, array(
						'title'    => $photo['title'],
						'square'   => isset($photo['url_sq']) ? $photo['url_sq'] : null,
						'original' => isset($photo['url_o']) ? $photo['url_o'] : null,
						'width'    => isset($photo['width_o']) ? $photo['width_o'] : null,
						'height'   => isset($photo['height_o']) ? $photo['height_o'] : null,
						'link'     => 'http://flickr.com/photos/'.$photo['pathalias'].'/'.$photo['id'].'/in/set-'.$id
					));
				}
				return array(
					'photos' => $photos
				);
			}
			else
			{
				return false;
			}
		}
		
		$matches = null;
		
		//find photo
		preg_match('/photos\/[^\/]*\/([0-9]*)/', $url, $matches);
		if ($matches)
		{
			$id = $matches[1];
			
			$params = array(
				'api_key'     => '146e92436cd84ffe70cd0b9349e0fd10',
				'method'      => 'flickr.photos.getSizes',
				'photo_id'    => $id,
				'format'      => 'php_serial'
			);
			
			$encoded_params = array();
			
			foreach ($params as $k => $v)
			{
				$encoded_params[] = urlencode($k).'='.urlencode($v);
			}
			
			$url2 = "http://api.flickr.com/services/rest/?".implode('&', $encoded_params);
			$rsp = file_get_contents($url2);
			$rsp_obj = unserialize($rsp);
			
			if ($rsp_obj['stat'] == 'ok')
			{
				$sizes = $rsp_obj['sizes']['size'];
				foreach ($sizes as $size)
				{
					if ($size['label'] == "Large")
					{
						return array(
							'photo' => array(
								'url'    => $size['source'],
								'width'  => $size['width'],
								'height' => $size['height']
							)
						);
					}
				}
				return false;
			}
			else
			{
				return false;
			}
		}
	}
}

function get_data_from_vimeo_url ($url)
{
	preg_match("/vimeo.com/", $url, $matches);
	
	if ($matches)
	{
		$url2 = 'http://vimeo.com/api/oembed.json?url='.$url;
		$rsp = file_get_contents($url2);
		$rsp_obj = json_decode($rsp);
		
		return array(
			'video' => array(
				'title' => $rsp_obj->title,
				'embed' => $rsp_obj->html
			)
		);
	}
	return false;
}

function get_data_from_youtube_url ($url)
{
	preg_match("/youtube.com/", $url, $matches);
	
	if ($matches)
	{
		$url2 = 'http://www.youtube.com/oembed?format=json&url='.$url;
		$rsp = file_get_contents($url2);
		$rsp_obj = json_decode($rsp);
		
		return array(
			'video' => array(
				'title' => $rsp_obj->title,
				'embed' => $rsp_obj->html
			)
		);
	}
	return false;
}

