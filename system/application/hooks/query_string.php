<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Allow Query Strings in CI while still using AUTO uri_protocol
 *
 * @author Dan Horrigan
 */
class Query_String {

	private static $_qs;

	public function __construct() { }

	public static function clean_uri()
	{
		$_GET = NULL;
		$orig_query_string = $_SERVER['REQUEST_URI'];
		
		if (strpos($_SERVER['REQUEST_URI'], '?') !== FALSE)
		{
			$orig_query_string = explode('?', $orig_query_string);
			$orig_query_string = $orig_query_string[1];
		}
		
		self::$_qs = $orig_query_string;

		foreach (array('REQUEST_URI', 'PATH_INFO', 'ORIG_PATH_INFO') as $uri_protocol)
		{
			if ( ! isset($_SERVER[$uri_protocol]))
			{
				continue;
			}

			if (strpos($_SERVER[$uri_protocol], '?') !== FALSE)
			{
				$_SERVER[$uri_protocol] = str_replace('?'.$orig_query_string, '', $_SERVER[$uri_protocol]);
			}
		}
	}

	public static function recreate_get()
	{
		parse_str(self::$_qs, $_GET);
	}
}