<?php

class Image_Manip extends MY_Controller {

	private function create_private_chunk()
	{
		//create random a-z0-9 private chunk
		$chunk = $this->random_character().$this->random_character().$this->random_character().$this->random_character().$this->random_character().$this->random_character().$this->random_character();
		
		if ($this->db->where('chunk', $chunk)->get('stored_images')->num_rows() !== 0)
		{
			return $this->create_private_chunk();
		}
		
		return $chunk;
	}
	private function random_character()
	{
		$num = rand(1, 36);
		
		if ($num > 26)
		{
			return $num - 27;
		}
		
		switch ($num)
		{
			case 1:
				return 'a';
				break;
			case 2:
				return 'b';
				break;
			case 3:
				return 'c';
				break;
			case 4:
				return 'd';
				break;
			case 5:
				return 'e';
				break;
			case 6:
				return 'f';
				break;
			case 7:
				return 'g';
				break;
			case 8:
				return 'h';
				break;
			case 9:
				return 'i';
				break;
			case 10:
				return 'j';
				break;
			case 11:
				return 'k';
				break;
			case 12:
				return 'l';
				break;
			case 13:
				return 'm';
				break;
			case 14:
				return 'n';
				break;
			case 15:
				return 'o';
				break;
			case 16:
				return 'p';
				break;
			case 17:
				return 'q';
				break;
			case 18:
				return 'r';
				break;
			case 19:
				return 's';
				break;
			case 20:
				return 't';
				break;
			case 21:
				return 'u';
				break;
			case 22:
				return 'v';
				break;
			case 23:
				return 'w';
				break;
			case 24:
				return 'x';
				break;
			case 25:
				return 'y';
				break;
			case 26:
				return 'z';
				break;
		}
		return 0;
	}

	function golden($w, $type, $file)
	{
		$h = round($w / 1.61803399);
		
		$this->rect($w, $h, $type, $file);
	}
	
	function golden_height($h, $type, $file)
	{
		$w = round($h * 1.61803399);
		
		$this->rect($w, $h, $type, $file);
	}
	
	function golden_tall($h, $type, $file)
	{
		$w = round($h / 1.61803399);
		
		$this->rect($w, $h, $type, $file);
	}
	
	function square($size, $type, $file)
	{
		$this->rect($size, $size, $type, $file);
	}
	
	function rect($w, $h, $type, $file)
	{
		$check = $this->db->where(array(
			'type'   => $type,
			'file'   => $file,
			'width'  => $w,
			'height' => $h
		))->get('stored_images');
		if ($check->num_rows() === 1)
		{
			$this->load_cache($check->row()->cache);
			return true;
		}
		
		$this->load->library('image_lib');
		$path = '';
		switch ($type)
		{
			case 'post_image':
				$path = 'user_images/post_images/'.$file;
				break;
			case 'accessories':
				$path = 'accessory_photos/'.$file;
				break;
			case 'avatars':
				$path = 'user_images/avatars/'.$file;
				break;
			case 'page_images':
				$path = 'user_images/page_images/'.$file;
				break;
		}
		if ($path)
		{
			//get image info
			if (file_exists($path))
			{
				$chunk = $this->create_private_chunk();
				$ext   = explode('.', $path);
				$ext   = $ext[count($ext) - 1];
				$cached = $chunk.'.'.$ext;
				list($width, $height, $t, $attr) = getimagesize($path);
			}
			else
			{
				show_404();
			}
			
			if ($width > $height)
			{
				$config['width']		= $width * $h / $height;
				$config['height']		= $h;
				
				if ($config['width'] < $w)
				{
					$config['width']    = $w;
					$config['height']   = $height * $w / $width;
				}
			}
			else
			{
				$config['width']		= $w;
				$config['height']		= $height * $w / $width;
				
				if ($config['height'] < $h)
				{
					$config['width']    = $width * $h / $height;
					$config['height']   = $h;
				}
			}
			if ($config['width'] > $w)
			{
				$config['x_axis'] = ($config['width'] - $w) / 2;
			}
			if ($config['height'] > $h)
			{
				$config['y_axis'] = ($config['height'] - $h) / 2;
			}
			$config['crop_width'] = $w;
			$config['crop_height'] = $h;
			
			$config['image_library'] 	= 'gd2';
			$config['source_image']		= $path;
			$config['new_image']        = 'cached_images/'.$cached;
			$config['maintain_ratio']	= false;
			$this->image_lib->initialize($config);
			
			$resized = $this->image_lib->resize_n_crop();
			
			$this->db->insert('stored_images', array(
				'type' => $type,
				'file' => $file,
				'width' => $w,
				'height' => $h,
				'chunk' => $chunk,
				'cache' => $cached,
				'created_at' => time()
			));
			
			$this->load_cache($cached);
		}
	}
	
	private function load_cache($file)
	{
		redirect('cached_images/'.$file);
	}
	
}