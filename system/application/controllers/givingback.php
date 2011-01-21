<?php

class GivingBack extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->require_ssl();
	}
	
	function process()
	{
		if ($_POST)
		{
			if ($this->is_logged_in())
			{
				$email = $this->account->email;
			}
			$email = isset($_POST['email']) ? $_POST['email'] : ($this->is_logged_in() ? $this->account->email : '');
			
			$amount		= $_POST['amount'];
			$firstName	= $_POST['firstName'];
			$lastName	= $_POST['lastName'];
			$comments   = $_POST['comments'];
			
				
			if ($amount < 10000) {
				
				if ($_POST['payment_type'] == 'credit')
				{
					$cc			= $_POST['cc'];
					$code		= $_POST['code'];
					$exp		= $_POST['exp'];
					$zip		= $_POST['zip'];
				}
				else
				{
					$routing	= $_POST['routing'];
					$account	= $_POST['account'];
					$bank		= $_POST['bank'];
					$type		= $_POST['type'];
					$holder		= $_POST['holder'];
				}
				
				if (isset($_POST['frequency']))
				{
					$frequency 	= $_POST['frequency'];
					$start_date	= $_POST['start_date'];
					$num_gifts	= $_POST['num_gifts'];
				}
				else
				{
					$frequency	= null;
					$start_date	= null;
					$num_gifts	= null;
				}
				
				$this->load->model('givingback_model');
				if (isset($_POST['campus']) && $_POST['campus'] != 0)
				{
					$g = $this->groups_model->item($_POST['campus']);
					$campus = $g;
				}
				else
				{
					$campus = $this->groups_model->item(481);
				}
				$data = array(
					'firstName'	=> $firstName,
					'lastName'	=> $lastName,
					'email'		=> $email,
					'campus'    => $campus,
					'comments' => $comments
				);
				
				$custom_fields = null;
				if (isset($_POST['custom_fields']))
				{
					$data['custom_fields'] = $_POST['custom_fields'];
				}
				$page_id = null;
				$gift_word = 'gift';
				if (isset($_POST['page_id']))
				{
					$page_id         = $_POST['page_id'];
					$data['page_id'] = $_POST['page_id'];
					$data['page']    = $this->pages_model->item($_POST['page_id']);
					$data['page']->content = unserialize($data['page']->content);
					$gift_word = $data['page']->content['gift_word'];
				}
				
				if ($_POST['payment_type'] == 'credit')
				{
					$data['cc']		= $cc;
					$data['code']	= $code;
					$data['exp']	= $exp;
					$data['zip']	= $zip;
					$data['payment_type'] = 'credit';
				}
				else
				{
					$data['routing']	= $routing;
					$data['account']	= $account;
					$data['bank']		= $bank;
					$data['type']		= $type;
					$data['holder']		= $holder;
					$data['payment_type'] = 'echeck';
				}
				
				if ($frequency)
				{
					$data['frequency']	= $frequency;
					$data['start_date']	= $start_date;
					$data['num_gifts']	= $num_gifts;
					
					$result = $this->givingback_model->process_recurring($this->account !== null ? $this->account->id : null, $amount, $data);
					echo $result['result'][1] == 'success' ? 'Thank you for your '.$gift_word.'! You will receive an email shortly.' : '<span class="error">'.$result['result'][0].'</span>';
				}
				else {
				
					$result = $this->givingback_model->process_once($this->account !== null ? $this->account->id : null, $amount, $data);
					$campusName = $campus->name;
					if ($result[1] === 'success') {
						$transID = $result[2][6];
						
						$subject        = 'Thank you for your '.$gift_word.'!';
						$email_message  = "Thank you for your online gift of \${$amount} to the Jesus Mission at COMMUNITY. Your generosity allows us to continue the mission of helping people find their way back to God.<br /><br />";
						$email_message .= "Thank you for joining us on this mission!<br /><br />";
						$email_message .= "Blessings,<br />";
						$email_message .= "Community Christian Church<br />";
						$email_message .= "<table><tr><td><strong>Name:</strong></td><td>{$firstName} {$lastName}</td></tr><tr><td><strong>Email:</strong></td><td>{$email}</td></tr><tr><td><strong>Gift Amount:</strong></td><td>\${$amount}</td></tr><tr><td><strong>Campus:</strong></td><td>{$campusName}</td></tr></table><br />";
						
						if ($page_id === null)
						{
							$email_message .= "A PDF is attached as a record of your gift. You can use this stub on the weekend to participate in Giving Back to God at your campus. Enjoy!";
						}
						
						$this->load->library('email');
						$config['mailtype'] = 'html';
						$this->email->initialize($config);
						
						$this->email->from('no-reply@communitychristian.org', 'Community Online');
						$this->email->to($email); 
						
						$this->email->subject($subject);
						$this->email->message($email_message);
						
						if ($page_id === null)
						{
							$pdfPath = 'receipt-'.$transID.'.pdf';
							$this->create_pdf($transID, $pdfPath);
							
							$this->email->attach($pdfPath);
							
							$this->load->helper('file');
							delete_files($pdfPath);
						}
						
						$this->email->send();
						
						$this->email->clear();
						
						//send email to admin
						$admin_email    = (isset($data['page']) && $data['page']->content['email']) ? $data['page']->content['email'] : 'davidgirdwood@communitychristian.org, dougleddon@communitychristian.org, johnciesniewski@communitychristian.org';
						$subject        = "New online gift";
						$email_message  = "The following online ".$gift_word." was just received:";
						$email_message .= "<table><tr><td><strong>Name:</strong></td><td>{$firstName} {$lastName}</td></tr><tr><td><strong>Email:</strong></td><td>{$email}</td></tr><tr><td><strong>Gift Amount:</strong></td><td>\${$amount}</td></tr><tr><td><strong>Campus:</strong></td><td>{$campusName}</td></tr></table><br />";
						
						$this->email->from('no-reply@communitychristian.org', 'Community Online');
						$this->email->to($admin_email);
						
						$this->email->subject($subject);
						$this->email->message($email_message);
						
						$this->email->send();
						
					}
					
					echo $result[1] === 'success' ? 'Thank you for your donation! You will receive an email shortly.' : '<span class="error">'.$result[0].'</span>';
				}
				
			}
			else 
			{
				echo '<span class="error">Thank you for your generosity. This means so much to us and to this mission of helping people find their way back to God. <strong>For gifts of this size, we would prefer you write us a check to avoid high credit card fees on large transactions.</strong> If you are able, please send your check to the church by mail or contact <a href="/index.php?option=com_cbcontact&task=view&contact_id=32&Itemid=194">David Girdwood</a> for alternate electronic gift options. Again, thank you for your generosity to the Jesus Mission at Community!</span>';
			}
		}
	}
	
	function index()
	{
		$groups_for_user = null;
		if ($this->account !== null)
		{
			$groups_for_user = $this->groups_model->groups_with_users(null, $this->account->id);
		}
		$this->load->vars(array(
			'title'			 => 'Giving Back',
			'account' 		  => $this->account,
			//'hide_navigation' => true,
			'groups_for_user' => $groups_for_user,
			'css_files'       => array(
				'/resources/css/layout.css',
				'/resources/css/group_page.css',
				'/resources/css/dateselector.jf.css'
			),
			'js_files'        => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
				'/resources/js/ccc.js',
				'/resources/js/connect.js',
				'/resources/js/group_page.js',
				'/resources/js/dateselector.jf.js'
			)
		));
		$this->load->view('general/head');
		$this->load->view('giveback/give');
		$this->load->view('general/foot');
	}
	
	private function create_pdf($transaction_id, $path)
	{
		require_once('tcpdf/config/lang/eng.php');
		require_once('tcpdf/tcpdf.php');
		
		$transaction = $this->db->where('transaction_id', $transaction_id)->get('giving');
		
		if ($transaction->num_rows() !== 1) return false;
		
		$row = $transaction->row();
		
		$htmltable = '<h1>Giving Receipt</h1>
						<p>Because we value your participation in Giving Back to God during celebration services on the weekend, we\'re providing this stub as a record of your gift. If you like, you can print this file and place it in the offering bag during Giving Back to God. However, your gift will be processed and recorded even if you don\'t. Thank you for using online giving! </p>
		<img src="http://joomla.communitychristian.org/components/com_pdf/scissors.jpg" width="50" height="33" /><br />
		<table width="95%" border="1" cellspacing="0" cellpadding="10">
		  <tr>
		    <td height="100">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td height="80"><img src="http://joomla.communitychristian.org/components/com_pdf/Community2008-Logo200px.jpg" alt="Community Christian Church" name="Logo" width="158" height="80" id="Logo" /></td>
		    <td height="80"><div align="right"><img src="http://joomla.communitychristian.org/components/com_pdf/gavebanner80x80.jpg" width="80" height="80" alt="" /></div></td>
		  </tr>
		  <tr>
		    <td colspan="2"><blockquote>
		        <p style="font-size:25px"><strong>Name</strong>: '.$row->first_name.' '. $row->last_name.'<br /><strong>Date</strong>: '.date("F j, Y").'<br /><strong>Amount</strong>: $'.$row->amount.$row->comments.'</p>
		      </blockquote></td>
		    </tr>
		  <tr>
		    <td colspan="2"><span class="style2"><strong>http://www.communitychristian.org/give</strong></span></td>
		  </tr>
		  <tr>
		    <td colspan="2"><span style="font-size:15px">Counters: This gift has already been processed and counted toward the weekend totals, please do not add this amount to your totals.</span></td>
		  </tr>
		</table>
		</td>
		  </tr>
		</table>
		'; 
		
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
		
		// set document information
		$pdf->SetCreator('http://www.communitychristian.org/give');
		$pdf->SetAuthor('Community Christian Church');
		$pdf->SetTitle('Online Giving Receipt');
		$pdf->SetSubject('Online Giving');
		$pdf->SetKeywords('Community Christian Church, Giving, generosity, receipt');
		
		// set default header data
		$pdf->SetHeaderData('', '0', "Community Christian Church", "http://www.communitychristian.org/give");
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l); 
		
		// ---------------------------------------------------------
		
		// set font
		$pdf->SetFont('dejavusans', '', 10);
		
		// add a page
		$pdf->AddPage();
		
		// output the HTML content
		$pdf->writeHTML($htmltable, true, 0, true, 0);
		
		// reset pointer to the last page
		$pdf->lastPage();
		
		//Change To Avoid the PDF Error
		  ob_end_clean();
		  
		//Close and output PDF document
		$pdf->Output($path, 'F');
	}
	
}