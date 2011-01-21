<?php

class GivingBack_Model extends Model {

	private function parse_return($content)
	{
		$refId = $this->substring_between($content, '<refId>', '</refId>');
		$resultCode = $this->substring_between($content, '<resultCode>', '</resultCode>');
		$code = $this->substring_between($content, '<code>', '</code>');
		$text = $this->substring_between($content, '<text>', '</text>');
		$subscriptionId = $this->substring_between($content, '<subscriptionId>', '</subscriptionId>');
		
		return array($refId, $resultCode, $code, $text, $subscriptionId);
	}
	
	private function substring_between($haystack, $start, $end) 
	{
	
		if (strpos($haystack, $start) === false || strpos($haystack, $end) === false) 
		{
			return false;
		}
		else
		{
			$start_position = strpos($haystack, $start) + strlen($start);
			$end_position = strpos($haystack, $end);
			return substr($haystack, $start_position, $end_position - $start_position);
		}
	}
	
	function process_payment_once($user_id, $amount, $info)
	{
		$user = $user_id !== null ? $this->users->item($user_id) : null;
		
		if ((isset($info['payment_type']) && $info['payment_type'] == 'credit') || !isset($info['payment_type']))
		{
			$cc					= $info['cc'];
			$card_code			= $info['code'];
			$exp				= $info['exp'];
			$zip       			= $info['zip'];
		}
		else
		{
			$routing			= $info['routing'];
			$account			= $info['account'];
			$bank				= $info['bank'];
			$type				= $info['type'];
			$holder				= $info['holder'];
		}
		
		if ($info['page_id'] === null)
		{
			$desc					= 'Jesus Mission Fund'.($info['campus'] !== null ? ', '.$info['campus']->name : '').($info['comments'] ? ', '.$info['comments'] : '');
			$tags					= 'Jesus Mission Fund'.($info['campus'] !== null ? ', '.$info['campus']->name : '');
		}
		else
		{
			$desc					= ($info['campus'] !== null ? ', '.$info['campus']->name : '').($info['comments'] ? ', '.$info['comments'] : '');
			$tags					= ($info['campus'] !== null ? ', '.$info['campus']->name : '');
			if ($info['custom_fields'] !== null)
			{
				foreach ($info['custom_fields'] as $field)
				{
					$desc .= ', '.$field;
					$tags .= ', '.$field;
				}
			}
		}
		$amount					= $amount;
		$firstName				= $info['firstName'];
		$lastName				= $info['lastName'];
		$email					= $info['email'];
		
		$this->load->library('MY_payment');
		
		if ((isset($info['payment_type']) && $info['payment_type'] == 'credit') || !isset($info['payment_type']))
		{
			$params->method     = 'CC';
			$params->cc			= $cc;
			$params->card_code	= $card_code;
			$params->exp		= $exp;
			$params->zip		= $zip;
		}
		else
		{
			$params->method     = 'ECHECK';
			$params->aba_code	= $routing;
			$params->acct_num	= $account;
			$params->acct_type	= $type;
			$params->bank_name	= $bank;
			$params->acct_name	= $holder;
		}
		$params->desc			= $desc;
		$params->amount			= $amount;
		$params->firstName		= $firstName;
		$params->lastName		= $lastName;
		$params->email			= $email;
		$params->address    	= '';
		$params->city	    	= '';
		$params->state			= '';
		$params->customerMonth	= '';
		$params->customerDay	= '';
		$params->customerYear	= '';
		$params->specialCode    = '';
		$params->invoice		= '1-418-1-'.time();
		
		$result = $this->my_payment->Authorize($params);
		
		return array(
			'result' => $result,
			'tags' => $tags
		);
	}
	
	function process_once($user_id, $amount, $info)
	{
		//get user
		$user = $user_id !== null ? $this->users->item($user_id) : null;
		//if ($user === false) return false;
		
		//process payment
		$result = $this->process_payment_once($user_id, $amount, $info);
		$tags   = $result['tags'];
		$result = $result['result'];
		
		//split
		$result = explode('|', $result);
		
		$message = '';
		$messageType = 'success';
		
		//initial fail
		switch($result[0])
		{
			case 1: //everything was successful
				break;
			case 2: case 3:
				$message = 'Your online gift could not be processed due to the following reason: '.$result[3];
				$messageType = 'error';
				break;
		}
		
		//secondary fail
		switch($result[38])
		{
			case 'N':
				$message = 'Your online gift could not be processed due to the following reason: '.'The credit card security code does not match the credit card. Please try again.';
				$messageType = 'error';
				break;
			case 'P':
				$message = 'Your online gift could not be processed due to the following reason: '.'The credit card security code you entered could not be processed. Please try a different card.';
				$messageType = 'error';
				break;
			case 'S':
				$message = 'Your online gift could not be processed due to the following reason: '.'The credit card security code was not entered. Please enter it below.';
				$messageType = 'error';
				break;
			case 'U':
				$message = 'Your online gift could not be processed due to the following reason: '.'The card issuer is unable to process your request. Please contact <a href="/index.php?option=com_cbcontact&task=view&contact_id=32&Itemid=194">David Girdwood</a> for help.';
				$messageType = 'error';
				break;
		}
		
		//store info and send email
		if ($result[0] == 1)
		{
			//get transaction id
			$transaction_id = $result[6];
			
			$this->db->insert('giving', array(
				'user_id' 			=> $user_id,
				'campus_id'			=> $info['campus'] ? $info['campus']->id : null,
				'page_id'			=> $info['page_id'],
				'transaction_id' 	=> $transaction_id,
				'amount' 			=> $amount,
				'payment_method' 	=> $info['payment_type'],
				'first_name' 		=> $info['firstName'],
				'last_name' 		=> $info['lastName'],
				'email' 			=> $info['email'],
				'zip' 				=> $info['zip'],
				'tags' 				=> $tags,
				'comments' 			=> $info['comments'],
				'payment_at' 		=> time(),
				'created_at' 		=> time()
			));
			
			//send email
			
		}
		
		return array($message, $messageType, $result);
	}
	
	function process_payment_recurring($user_id, $amount, $info)
	{
		if ((isset($info['payment_type']) && $info['payment_type'] == 'credit') || !isset($info['payment_type']))
		{
			$cc					= $info['cc'];
			$card_code			= $info['code'];
			$exp				= $info['exp'];
			$zip       			= $info['zip'];
		}
		else
		{
			$routing			= $info['routing'];
			$account			= $info['account'];
			$bank				= $info['bank'];
			$type				= $info['type'];
			$holder				= $info['holder'];
		}
		
		$desc					= 'Community Christian Church - Online Giving';
		$amount					= $amount;
		$firstName				= $info['firstName'];
		$lastName				= $info['lastName'];
		
		$frequency				= $info['frequency'];
		switch ($frequency)
		{
			case 'weekly':
				$length			= 7;
				$unit			= 'days';
				break;
			case 'monthly':
				$length			= 1;
				$unit			= 'months';
				break;
			case 'quarterly':
				$length			= 3;
				$unit			= 'months';
				break;
			case 'semi-annually':
				$length			= 6;
				$unit			= 'months';
				break;
			case 'annually':
				$length			= 12;
				$unit			= 'months';
				break;
		}
		$startdate				= $info['start_date'];
		$totalOccurrences		= $info['num_gifts'];
		$subscription_name		= 'Jesus Mission Fund';
		$transkey				= '';
		
		$xml = 	"<?xml version=\"1.0\" encoding=\"utf-8\"?>".
				"<ARBCreateSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
					"<merchantAuthentication>".
						"<name></name>".
						"<transactionKey></transactionKey>".
					"</merchantAuthentication>".
					"<refId>Sampledaryl</refId>".
					"<subscription>".
						"<name>".$subscription_name."</name>".
						"<paymentSchedule>".
							"<interval>".
								"<length>". $length ."</length>".
								"<unit>". $unit ."</unit>".
							"</interval>".
							"<startDate>" . date('Y-m-d', strtotime($startdate)) . "</startDate>".
							"<totalOccurrences>". $totalOccurrences . "</totalOccurrences>".
							"<trialOccurrences>0</trialOccurrences>".
						"</paymentSchedule>".
						"<amount>". $amount ."</amount>".
						"<trialAmount>0</trialAmount>".
						"<payment>".
							"<creditCard>".
								"<cardNumber>" . $cc . "</cardNumber>".
								"<expirationDate>" . $exp . "</expirationDate>".
							"</creditCard>".
						"</payment>".
						"<customer>".
							"<email>". $info['email'] ."</email>".
						"</customer>".
						"<billTo>".
							"<firstName>". $firstName . "</firstName>".
							"<lastName>" . $lastName . "</lastName>".
							"<zip>". $zip . "</zip>".
						"</billTo>".
					"</subscription>".
				"</ARBCreateSubscriptionRequest>";
			
		$posturl = "https://api.authorize.net/xml/v1/request.api";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		
		list($refId, $resultCode, $code, $text, $subscriptionId) = $this->parse_return($response);
		if ($resultCode != 'Ok')
		{
			$result = array('Your online gift could not be processed due to the following reason: '.$text, 'error');
		}
		if($resultCode=='Ok')
		{
			$result = array('', 'success');
		}
		
		$this->db->insert('giving', array(
			'user_id' 			=> $user_id,
			'campus_id'			=> $info['campus'] ? $info['campus']->id : null,
			'page_id'			=> $info['page_id'],
			'transaction_id' 	=> $subscriptionId,
			'amount' 			=> $amount,
			'frequency'			=> $frequency,
			'payment_method' 	=> $info['payment_type'],
			'first_name' 		=> $info['firstName'],
			'last_name' 		=> $info['lastName'],
			'email' 			=> $info['email'],
			'zip' 				=> $info['zip'],
			'tags' 				=> '',
			'comments' 			=> $info['comments'],
			'payment_at' 		=> strtotime($startdate),
			'created_at'		=> time()
		));
		
		return array(
			'refId' => $refId,
			'resultCode' => $resultCode,
			'code' => $code,
			'text' => $text,
			'subscriptionID' => $subscriptionId,
			'result' => $result
		);
	}
	
	function process_recurring($user_id, $amount, $info)
	{
		//get user
		$user = $user_id !== null ? $this->users->item($user_id) : null;
		//if ($user === false) return false;
		
		//process payment
		$result = $this->process_payment_recurring($user_id, $amount, $info);
		
		return $result;
	}
	
}