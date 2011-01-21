<?php

function has_role($role, $account_id, $group_id = null)
{
	$CI =& get_instance();
	
	if ($group_id !== null)
	{
		$CI->db->where('group_id', $group_id);
	}
	
	$roles = $CI->db->where('user_id', $account_id)->where('type', $role)->limit(1)->get('roles');
	if ($roles->num_rows() === 1)
	{
		return true;
	}
	return false;
}