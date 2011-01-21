<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Authorize.net Payment Module
|--------------------------------------------------------------------------
|
| Just add the following config to your application/config/config.php file
|
| $config['at_login']	= "xxxxxxxxxx"; //your login
| $config['at_password']	= "xxxxxxxxxxxx"; //your transaction key
| $config['at_test']	= 1; //Set to 0 for live transactions
| $config['at_debug']	= 1; //Set to 0 for live transactions
| $config['at_site'] = 'https://test.authorize.net/gateway/transact.dll'; //comment for live trans
| //$config['at_site'] = 'https://secure.authorize.net/gateway/transact.dll'; //uncomment for live trans
|
|	Call it by doing this:
|
|		$this->load->library('my_payment');
|		$params->cc = '1293081309812039812039' ;//etc... you get the idea
|		
|		$result = $this->my_payment->authorize($params);
|		print_r($result); //response codes from authorize.net
|
|
|
*/

class MY_Payment {

	public function Authorize($params)
	{
		$CI =& get_instance();

		$x_Login = $CI->config->item('at_login');     
		$x_Password = $CI->config->item('at_password');

		$DEBUGGING					= $CI->config->item('at_debug');
		$TESTING					= $CI->config->item('at_test');	
		$ERROR_RETRIES				= 2;

		$auth_net_url				= $CI->config->item('at_site');
		
		if ((isset($params->method) ? $params->method : 'CC') == 'CC')
		{
			$array = array(
				"x_card_num"			=> $params->cc,
				"x_card_code"			=> $params->card_code,
				"x_exp_date"			=> $params->exp,
				"x_zip"					=> $params->zip,
				'x_method'				=> 'CC'
			);
		}
		else
		{
			$array = array(
				"x_bank_aba_code"		=> $params->aba_code,
				"x_bank_acct_num"		=> $params->acct_num,
				"x_bank_acct_type"		=> $params->acct_type,
				"x_bank_name"			=> $params->bank_name,
				"x_bank_acct_name"		=> $params->acct_name,
				"x_echeck_type"			=> 'WEB',
				'x_method'				=> 'ECHECK'
			);
		}
		$authnet_values				= array
		(
			"x_invoice_num"			=> isset($params->invoice) ? $params->invoice : '',
			"x_login"				=> $x_Login,
		 	"x_tran_key"			=> $x_Password,
			"x_version"				=> "3.1",
			"x_delim_char"			=> "|",
			"x_delim_data"			=> "TRUE",
			"x_type"				=> "AUTH_CAPTURE",
		 	"x_relay_response"		=> "FALSE",
			"x_description"			=> $params->desc,
			"x_amount"				=> $params->amount,
			"x_email"				=> $params->email,
			"x_first_name"			=> $params->firstName,
			"x_last_name"			=> $params->lastName,
			"x_address"				=> $params->address,
			"x_city"				=> $params->city,
			"x_state"				=> $params->state,
			"x_recurring_billing"	=> 'FALSE'/*,
			"CustomerBirthMonth"	=> $params->customerMonth,
			"CustomerBirthDay"		=> $params->customerDay,
			"CustomerBirthYear"		=> $params->customerYear,
			"SpecialCode"			=> $params->specialCode*/
		);
		$authnet_values = array_merge($authnet_values, $array);

		$fields = "";
		foreach( $authnet_values as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";

		$ch = curl_init($auth_net_url);
		
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " ));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		
		$result = curl_exec($ch);
		
		if (curl_errno($ch)) {
			echo curl_error($ch);
		}
		
		curl_close ($ch);

		return $result;

	}
}
/* End of file My_payment.php */
/* Location: ./system/application/libraries/My_payment.php */