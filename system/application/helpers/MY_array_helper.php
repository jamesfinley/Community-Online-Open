<?php

function is_assoc(array $array)
{
	// Keys of the array
	$keys = array_keys($array);

	// If the array keys of the keys match the keys, then the array must
	// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
	return array_keys($keys) !== $keys;
}

function rotate($source_array, $keep_keys = TRUE)
{
	$new_array = array();
	foreach ($source_array as $key => $value)
	{
		$value = ($keep_keys === TRUE) ? $value : array_values($value);
		foreach ($value as $k => $v)
		{
			$new_array[$k][$key] = $v;
		}
	}

	return $new_array;
}

?>