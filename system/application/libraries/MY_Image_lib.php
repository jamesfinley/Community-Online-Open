<?php

class MY_Image_lib extends CI_Image_lib {
	
	// --------------------------------------------------------------------
	
	/**
	 * Image Resize & Crop
	 *
	 * This is a wrapper function that chooses the proper
	 * resize function based on the protocol specified
	 *
	 * @access	public
	 * @return	bool
	 */
	function resize_n_crop()
	{
		$protocol = 'image_process_'.$this->image_library;
	
		if (preg_match('/gd2$/i', $protocol))
		{
			$protocol = 'image_process_gd';
		}
	
		return $this->$protocol('resize_n_crop');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Image Process Using GD/GD2
	 *
	 * This function will resize or crop
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function image_process_gd($action = 'resize')
	{
		$v2_override = FALSE;

		// If the target width/height match the source, AND if the new file name is not equal to the old file name
		// we'll simply make a copy of the original with the new name... assuming dynamic rendering is off.
		if ($this->dynamic_output === FALSE)
		{
			if ($this->orig_width == $this->width AND $this->orig_height == $this->height)
			{
 				if ($this->source_image != $this->new_image)
 				{
					if (@copy($this->full_src_path, $this->full_dst_path))
					{
						@chmod($this->full_dst_path, DIR_WRITE_MODE);
					}
				}

				return TRUE;
			}
		}

		// Let's set up our values based on the action
		if ($action == 'crop')
		{
			//  Reassign the source width/height if cropping
			$this->orig_width  = $this->width;
			$this->orig_height = $this->height;

			// GD 2.0 has a cropping bug so we'll test for it
			if ($this->gd_version() !== FALSE)
			{
				$gd_version = str_replace('0', '', $this->gd_version());
				$v2_override = ($gd_version == 2) ? TRUE : FALSE;
			}
		}
		elseif ($action == 'resize_n_crop')
		{
			
		}
		else
		{
			// If resizing the x/y axis must be zero
			$this->x_axis = 0;
			$this->y_axis = 0;
		}

		//  Create the image handle
		if ( ! ($src_img = $this->image_create_gd()))
		{
			return FALSE;
		}

 		//  Create The Image
		//
		//  old conditional which users report cause problems with shared GD libs who report themselves as "2.0 or greater"
		//  it appears that this is no longer the issue that it was in 2004, so we've removed it, retaining it in the comment
		//  below should that ever prove inaccurate.
		//
		//  if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor') AND $v2_override == FALSE)
 		if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor'))
		{
			$create	= 'imagecreatetruecolor';
			$copy	= 'imagecopyresampled';
		}
		else
		{
			$create	= 'imagecreate';
			$copy	= 'imagecopyresized';
		}

		$dst_img = $create($this->width, $this->height);
		$copy($dst_img, $src_img, 0, 0, 0, 0, $this->width, $this->height, $this->orig_width, $this->orig_height);
		
		if ($action == 'resize_n_crop')
		{
			$dst_img2 = $create($this->crop_width, $this->crop_height);
			$copy($dst_img2, $dst_img, 0, 0, $this->x_axis, $this->y_axis, $this->width, $this->height, $this->width, $this->height);
			$dst_img = $dst_img2;
		}

		//  Show the image
		if ($this->dynamic_output == TRUE)
		{
			$this->image_display_gd($dst_img);
		}
		else
		{
			// Or save it
			if ( ! $this->image_save_gd($dst_img))
			{
				return FALSE;
			}
		}

		//  Kill the file handles
		imagedestroy($dst_img);
		imagedestroy($src_img);

		// Set the file to 777
		@chmod($this->full_dst_path, DIR_WRITE_MODE);

		return TRUE;
	}
	
}