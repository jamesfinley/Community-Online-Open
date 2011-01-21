<?php

function ago($time)
{
	if ($time == 0)
	{
		return 'never';
	}
	
	$diff = time() - $time;
	if ($diff < 60) 
	{
		return 'just now';
	}
	
	$diff = round($diff / 60);
	if ($diff < 60) 
	{ 
		$min = 'minute'.(($diff > 1) ? 's' : '');
		return "{$diff} {$min} ago";
	}
	
	$diff = round($diff / 60);
	if ($diff < 24) 
	{
		$hr = 'hour'.(($diff > 1) ? 's' : '');
		return "{$diff} {$hr} ago";
	}
	
	$diff = round($diff / 24);
	if ($diff < 7) 
	{
		return ($diff > 1) ? "{$diff} days ago" : 'yesterday';
	}
	
	$diff = round($diff / 7);
	if ($diff < 12) 
	{
		return ($diff > 1) ? "{$diff} weeks ago" : 'last week';
	}
	else if ($diff < 52) 
	{
		$mo = floor($diff / (30 / 7));
		return "around {$mo} months ago";
	}
	
	$diff = round($diff / 52);
	return ($diff > 1) ? "Around {$diff} years ago" : 'last year';
}

?>