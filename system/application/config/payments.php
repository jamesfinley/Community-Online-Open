<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['at_login']		= ""; //your login
$config['at_password']	= ""; //your transaction key
$config['at_test']		= 0; //Set to 0 for live transactions
$config['at_debug']		= 0; //Set to 0 for live transactions
//$config['at_site']		= 'https://test.authorize.net/gateway/transact.dll'; //comment for live trans
$config['at_site']		= 'https://secure.authorize.net/gateway/transact.dll'; //uncomment for live trans 