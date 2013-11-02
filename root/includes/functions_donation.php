<?php
/**
*
* @package PayPal Donation MOD
* @copyright (c) 2013 Skouat
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
* Special Thanks to the following individuals for their inspiration:
* 	David Lewis (Highway of Life) http://startrekguide.com
* 	Micah Carrick (email@micahcarrick.com) http://www.micahcarrick.com
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

define('ASCII_RANGE', '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

class ppdm_main
{
	var $vars = array();

	/**
	* Method get_vars
	* Sets preset dynamic vars
	*
	* @param bool $acp
	*/
	function get_vars($acp = false)
	{
		global $config, $user, $auth;

		$this->vars = array(
			0	=> array(
				'var'	=> '{USER_ID}',
				'value'	=> $user->data['user_id'],
			),
			1	=> array(
				'var'	=> '{USERNAME}',
				'value'	=> $user->data['username'],
			),
			2	=> array(
				'var'	=> '{SITE_NAME}',
				'value'	=> $config['sitename'],
			),
			3	=> array(
				'var'	=> '{SITE_DESC}',
				'value'	=> $config['site_desc'],
			),
			4	=> array(
				'var'	=> '{BOARD_CONTACT}',
				'value'	=> $config['board_contact'],
			),
			5	=> array(
				'var'	=> '{BOARD_EMAIL}',
				'value'	=> $config['board_email'],
			),
			6	=> array(
				'var'	=> '{BOARD_SIG}',
				'value'	=> $config['board_email_sig'],
			),
		);

		if ($acp)
		{
			//Add language entries for displaying the vars
			for ($i = 0, $size = sizeof($this->vars); $i < $size; $i++)
			{
				$this->vars[$i]['name'] = $user->lang['DONATION_DP_' . substr(substr($this->vars[$i]['var'], 0, -1), 1)];
			}
		}
	}
}

class donation_main
{
	// Data from transaction
	private $trans_data = array();

	// Transaction verified (bool)
	public $verified = false;

	// Sender details
	public $business = '';

	/**
	* Define the member ID of the sender.
	* sender_data is set to 1 for anonymous donors. Default 1.
	*
	* @var int
	*/
	public $sender_data = array();

	// PayPal url
	public $u_paypal = '';

	//Board donation page
	public $page;

	// PayPal response (VERIFIED or INVALID)
	private $response = '';

	// PayPal response status (code 200 or other)
	private $response_status = '';

	/**
	*  If true, the recommended cURL PHP library is used to send the post back 
	*  to PayPal. If false then fsockopen() is used. Default true.
	*
	*  @var boolean
	*/
	public $use_curl = true;

	/**
	*  If true, cURL will use the CURLOPT_FOLLOWLOCATION to follow any 
	*  "Location: ..." headers in the response.
	*
	*  @var boolean
	*/
	public $follow_location = false;

	/**
	*  If true, explicitly sets cURL to use SSL version 3. Use this if cURL
	*  is compiled with GnuTLS SSL.
	*
	*  @var boolean
	*/
	public $force_ssl_v3 = true;

	/**
	 *  If true, the PayPal sandbox URI www.sandbox.paypal.com is used for the
	 *  post back. If false, the live URI www.paypal.com is used. Default false.
	 *
	 *  @var boolean
	 */
	public $use_sandbox = false;

	/**
	 *  If true, the error are logged into /store/transaction.log.
	 *  If false, error aren't logged. Default false.
	 *
	 *  @var boolean
	 */
	public $use_log_error = false;

	/**
	* The amount of time, in seconds, to wait for the PayPal server to respond
	* before timing out. Default 30 seconds.
	*
	* @var int
	*/
	public $timeout = 30;

	/**
	* Log error messages
	*
	* @param string $message
	*/
	public function log_error($message, $exit = false, $error_type = E_USER_NOTICE, $args = array())
	{
		global $phpbb_root_path;

		$error_timestamp = date('d-M-Y H:i:s Z');

		$backtrace = '';
		if ($this->use_sandbox)
		{
			$backtrace = get_backtrace();
			$backtrace = html_entity_decode(strip_tags(str_replace(array('<br />', "\n\n"), "\n", $backtrace)));
		}

		$message = str_replace('<br />', ';', $message);

		if (sizeof($args))
		{
			$message .= '[args] ';
			foreach ($args as $key => $value)
			{
				$value = urlencode($value);
				$message .= "{$key} = $value;";
			}
			unset($value);
		}

		if ($this->use_log_error)
		{
			error_log("[$error_timestamp] $message $backtrace", 3, $phpbb_root_path . 'store/transaction.log');
		}

		if ($exit)
		{
			trigger_error($message, $error_type);
		}
	}

	/**
	 * Post Back Using cURL
	 *
	 * Sends the post back to PayPal using the cURL library. Called by
	 * the validate_transaction() method if the use_curl property is true. Throws an
	 * exception if the post fails. Populates the response, response_status,
	 * and post_uri properties on success.
	 *
	 * @param  string  The post data as a URL encoded string
	 */
	protected function curl_post($encoded_data)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->u_paypal);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->follow_location);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);

		if ($this->force_ssl_v3)
		{
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		}

		$this->response = curl_exec($ch);
		$this->response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
		
		if ($this->response === false || $this->response_status == '0')
		{
			$errno = curl_errno($ch);
			$errstr = curl_error($ch);
			$this->log_error($user->lang['CURL_ERROR'] . $errno . ' (' . $errstr . ')');
		}
	}

	/**
	* Post Back Using fsockopen()
	*
	* Sends the post back to PayPal using the fsockopen() function. Called by
	* the validate_transaction() method if the use_curl property is false.
	* Throws an exception if the post fails. Populates the response,
	* response_status, properties on success.
	*
	* @param  string  The post data as a URL encoded string
	*/
	protected function fsock_post($encoded_data)
	{
		global $user;

		$errstr = '';
		$errno = 0;

		// post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($encoded_data) . "\r\n";
		$header .= "Connection: Close\r\n\r\n";

		$parse_url = parse_url($this->u_paypal);

		$fp = fsockopen('ssl://' . $parse_url['host'], 443, $errno, $errstr, $this->timeout);

		if (!$fp)
		{
			$this->log_error($user->lang['FSOCK_ERROR'] . $errno . ' (' . $errstr . ')');
		}
		else
		{
			// Send the data to PayPal
			fputs($fp, $header . $encoded_data . "\r\n\r\n");

			// Loop through the response
			while (!feof($fp))
			{
				if (empty($this->response)) {
					// extract HTTP status from first line
					$this->response .= $status = fgets($fp, 1024);
					$this->response_status = trim(substr($status, 9, 4));
				} else {
					$this->response .= fgets($fp, 1024);
				}
			}

			fclose($fp);
		}
	}

	/**
	* Post Data back to PayPal to validate the authenticity of the transaction.
	*
	* @param  array	$post_data = null
	* @param  int	$group_id = 0				ID of the donors group
	* @param  bool	$group_as_default = null	Define the value to set as default the donors group
	*
	*/
	public function validate_transaction($post_data = null, $group_id = 0, $group_as_default = false )
	{
		global $user, $db;

		$values = array();

		$this->data_list();

		// we ensure that the txn_id (transaction ID) contains only ASCII chars...
		$pos = strspn($this->trans_data['txn_id'], ASCII_RANGE);
		$len = strlen($this->trans_data['txn_id']);

		if ($pos != $len)
		{
			return;
		}

		if (!$this->trans_data['txn_id'])
		{
			$this->log_error($user->lang['INVALID_TRANSACTION_RECORD'], true, E_USER_NOTICE, $this->trans_data);
		}

		$decode_ary = array('receiver_email', 'payer_email', 'payment_date', 'business');
		foreach ($decode_ary as $key)
		{
			$this->trans_data[$key] = urldecode($this->trans_data[$key]);
		}

		// Add the cmd=_notify-validate for PayPal
		$params = 'cmd=_notify-validate&';

		if (!sizeof($post_data))
		{
			// Grab the post data form and set in an array to be used in the URI to PayPal
			foreach ($_POST as $key => $value)
			{
				$encoded = urlencode(stripslashes($value));
				$values[] = $key . '=' . $encoded;

				$this->trans_data[$key] = $value;
			}
		}
		else
		{
			foreach ($post_data as $key => $value)
			{
				$encoded = urlencode(stripslashes($value));
				$values[] = $key . '=' . $encoded;

				$this->data[$key] = $value;
			}
		}

		// implode the array into a string URI
		$params .= implode('&', $values);

		if ($this->use_curl)
		{
			$this->curl_post($params);
		}
		else
		{
			$this->fsock_post($params);
		}

		if (strpos($this->response_status, '200') === false)
		{
			$this->log_error($user->lang['INVALID_RESPONSE_STATUS'], true, E_USER_NOTICE, $this->response_status);
		}

		// the item number contains the user_id and the payment time in timestamp format
		list($this->trans_data['user_id'], $this->trans_data['payment_time']) = explode('_', substr($this->trans_data['item_number'], 4));

		// set confirmed to true/false depending on if the transaction was verified.
		if (strpos($this->response, 'VERIFIED') !== false)
		{
			$this->log_error('DEBUG VERIFIED:' . $this->get_text_report());
			$this->verified = $this->trans_data['confirmed'] = true;
			$this->sender_data = $this->trans_data['user_id'];
		}
		elseif (strpos($this->response, 'INVALID') !== false)
		{
			$this->trans_data['confirmed'] = false;
			$this->log_error('DEBUG INVALID:' . $this->get_text_report());
		}
		else
		{
			$this->log_error('DEBUG OTHER:' . $this->get_text_report());
			$this->log_error($user->lang['UNEXPECTED_RESPONSE'], true);
		}

		// Retrieve information text of donation customizable pages
		$sql = 'SELECT txn_id FROM ' . DONATION_DATA_TABLE . " WHERE txn_id = '" . $db->sql_escape($this->trans_data['txn_id']) . "'";
		$result = $db->sql_query($sql);
		$transaction_exists = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		// if true log to db with create method, if false update value
		$this->log_to_db($transaction_exists);

		// If the transaction is verified...
		if ($this->verified)
		{
			$anonymous_user = false;

			// if the user_id is not anonymous, get the user information (user id, username)
			if ($this->trans_data['user_id'] != ANONYMOUS)
			{
				$sql = 'SELECT user_id, username
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . (int) $this->trans_data['user_id'];
				$result = $db->sql_query($sql);
				$this->sender_data = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!sizeof($this->sender_data))
				{
					// no results, therefore the user is anonymous...
					$anonymous_user = true;
				}
			}
			else
			{
				// the user is anonymous by default
				$anonymous_user = true;
			}

			if ($anonymous_user)
			{
				// if the user is anonymous, check their PayPal email address with all known email hashes
				// to determine if the user exists in the database with that email
				$sql = 'SELECT user_id, username
						FROM ' . USERS_TABLE . '
						WHERE user_email_hash = ' . crc32(strtolower($this->trans_data['payer_email'])) . strlen($this->trans_data['payer_email']);
				$result = $db->sql_query($sql);
				$this->sender_data = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!sizeof($this->sender_data))
				{
					// no results, therefore the user is really a guest
					$this->sender_data = false;
				}
			}

			// we add the user to the donors group
			if (!empty($group_id) && $this->sender_data !== false && $this->trans_data['payment_status'] === 'Completed')
			{
				if (!function_exists('group_user_add'))
				{
					global $phpbb_root_path, $phpEx;

					include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
				}

				// add the user to the donors group and set as default.
				group_user_add($group_id, array($this->sender_data['user_id']), array($this->sender_data['username']),get_group_name($group_id), $group_as_default);
			}
		}

		// Send email and/or PM to founders
		$this->send_message();

	}

	/**
	 * Send a message to the Founders informing them of the Donation received.
	 * Echo information based on if the donation is verified or unverified.
	 *
	 * @param int $send_type	0 => disabled, 1 => PM, 2 => E-mail, 3 => Both
	 * @param string $message
	 * @param string $subject
	 */
	private function send_message($send_type = '', $message = '', $subject = '')
	{
		global $user, $config, $db;

		$send_type = (isset($config['donation_send_confirmation'])) ? $config['donation_send_confirmation'] : $send_type;

		if (empty($send_type))
		{
			return;
		}

		if (!$subject)
		{
			$l_title = ($this->verified) ? 'DONATION_RECEIVED_VERIFIED' : 'DONATION_RECEIVED_UNVERIFIED';

			$subject = $user->lang($l_title, !$this->sender_data ?  $user->lang['GUEST'] : $this->sender_data['username']);
		}

		// grab user data from all founders.
		$sql = 'SELECT user_id, username, user_email, user_lang, user_notify_type
				FROM ' . USERS_TABLE . '
				WHERE user_type = ' . USER_FOUNDER;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			// alternatively we could use rowset.
			$founder_ary[] = array(
				'user_id'			=> $row['user_id'],
				'username'			=> $row['username'],
				'user_email'		=> $row['user_email'],
				'user_lang'			=> $row['user_lang'],
				'user_notify_type'	=> $row['user_notify_type'],
			);
		}
		$db->sql_freeresult($result);


		// Determine if we are sending a PM or e-mailing the founders instead.
		switch ($send_type)
		{
			case 1:
				$this->send_pm($message, $subject, $founder_ary);
			break;

			case 2:
				$this->send_email($subject, $founder_ary);
			break;

			case 3:
				$this->send_pm($message, $subject, $founder_ary);
				$this->send_email($subject, $founder_ary);
			break;
		}
	}

	/**
	 * Send a PM to the Founders informing them of the Donation received.
	 * Echo information based on if the donation is verified or unverified.
	 *
	 * @param string $message
	 * @param string $subject
	 * @param string $founder_ary
	 */
	private function send_pm($message = '', $subject = '', $founder_ary = NULL)
	{
		global $user, $phpbb_root_path, $phpEx;

		if (!class_exists('parse_message'))
		{
			include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
		}

		if (!function_exists('submit_pm'))
		{
			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		}

		if (!$message)
		{
			if (empty($this->trans_data['settle_amount']))
			{
				$amount = $this->trans_data['mc_gross'] . ' ' . $this->trans_data['mc_currency'];
			}
			else
			{
				$amount = $this->trans_data['settle_amount'] . ' ' . $this->trans_data['settle_currency'];
			}

			$message = ($this->verified) ? 'DONATION_RECEIVED_MSG_VERIFIED' : 'DONATION_RECEIVED_MSG_UNVERIFIED';
			$message = sprintf($user->lang[$message], $this->trans_data['payer_email'], (!$this->sender_data) ?  $user->lang['GUEST'] : $this->sender_data['username'], $amount);

			// if there is a memo, add the memo to the message
//			if (!empty($this->trans_data['memo']))
//			{
//				$message .= "\n\n" . $user->lang['DONATION_MESSAGE'] . ":\n\n" . $db->sql_escape($this->trans_data['memo']);
//			}

			// if the transaction is not verified, all the admin to manually verify the transaction.
			if (!$this->verified)
			{
				$parse_url = parse_url($this->u_paypal);
				$message .= "\n\n" . sprintf($user->lang['TRANSACTION_NOT_VERIFIED'], $parse_url['scheme'] . '://' . $parse_url['host'] ,$this->trans_data['txn_id']);
			}
		}

		// Setup the PM message parser.
		$message_parser = new parse_message();
		$message_parser->message = $message;
		$message_parser->parse(true, true, true, true, true, true, true);

		foreach ($founder_ary as $id)
		{
			$address_list[$id['user_id']] = 'to';
		}

		// Setup the PM data...
		$pm_data = array(
			'from_user_id'		=> ($this->sender_data) ? $this->sender_data['user_id'] : ANONYMOUS,
			'from_username'		=> 'PayPal',
			'address_list'		=> array('u' => $address_list),
			'icon_id'			=> 10,
			'from_user_ip'		=> $user->ip,
			'enable_bbcode'		=> true,
			'enable_smilies'	=> true,
			'enable_urls'		=> true,
			'enable_sig'		=> false,
			'message'			=> $message_parser->message,
			'bbcode_bitfield'	=> $message_parser->bbcode_bitfield,
			'bbcode_uid'		=> $message_parser->bbcode_uid,
		);

		// Send the PM to the founders.
		submit_pm('post', $subject, $pm_data, false);
	}

	/**
	 * Send a E-mail to the Founders informing them of the Donation received.
	 * Echo information based on if the donation is verified or unverified.
	 *
	 * @param string $subject
	 * @param string $founder_ary
	 */
	private function send_email($subject = '', $founder_ary = NULL)
	{
		global $user, $config, $phpbb_root_path, $phpEx;

		// Setup the e-mail for the founders
		if (!class_exists('messenger'))
		{
			include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
		}

		$messenger = new messenger(false);
		// we may be using one e-mail template, not decided yet...
		$email_tpl = ($this->verified) ? 'paypal_donation' : 'paypal_unverified';

		$parse_url = parse_url($this->u_paypal);

		foreach ($founder_ary as $row)
		{
			// use the specified email language template according tho this users' language settings.
			$messenger->template($email_tpl, $row['user_lang']);

			// set the "reply to" header.
			$messenger->replyto($this->trans_data['payer_email']);

			// set the "to" header.
			$messenger->to($row['user_email'], $row['username']);

			// E-mail subject
			$messenger->subject(htmlspecialchars_decode($subject));

			$user_id = ($this->sender_data) ? $this->sender_data['user_id'] : $user->data['user_id'];
			$username = ($this->sender_data) ? $this->sender_data['username'] : $user->data['username'];

			// set some X-AntiAbuse headers, may not be needed but...
			$messenger->headers('X-AntiAbuse: Board servername - ' . $config['server_name']);
			$messenger->headers('X-AntiAbuse: User_id - ' . $user_id);
			$messenger->headers('X-AntiAbuse: Username - ' . $username);
			$messenger->headers('X-AntiAbuse: User IP - ' . $user->ip);

			// Assign variables for the MVC to be used in the e-mail template
			$messenger->assign_vars(array(
				'TO_USERNAME'	=> $row['username'],
				'SUBJECT'		=> $subject,
				'AMOUNT'		=> (empty($this->trans_data['settle_amount'])) ? $this->trans_data['mc_gross'] : $this->trans_data['settle_amount'],
				'CURRENCY'		=> (empty($this->trans_data['settle_currency'])) ? $this->trans_data['mc_currency'] : $this->trans_data['settle_currency'],
				'U_PAYPAL'		=> $parse_url['scheme'] . '://' . $parse_url['host'],
				'TXN_ID'		=> $this->trans_data['txn_id'],
				'PAYER_EMAIL'	=> $this->trans_data['payer_email'],
				'PAYER_USERNAME'=> ($this->sender_data) ? $this->sender_data['username'] : $this->trans_data['first_name'],
			));

			// now send the e-mail message
			$messenger->send($row['user_notify_type']);
		}
	}

	/**
	 * Log the transaction to the database
	 *
	 * @param bool $update -- update an existing transaction or insert a new transaction
	 */
	public function log_to_db($update = false)
	{
		global $db;

		list($this->trans_data['user_id'], $this->trans_data['payment_time']) = explode('_', substr($this->trans_data['item_number'], 4));

		// list the data to be thrown into the database
		$sql_ary = array(

			'txn_id'			=> $this->trans_data['txn_id'],
			'txn_type'			=> $this->trans_data['txn_type'],
			'confirmed'			=> $this->trans_data['confirmed'],
			'user_id'			=> $this->trans_data['user_id'],

			'item_name'			=> $this->trans_data['item_name'],
			'item_number'		=> $this->trans_data['item_number'],
			'payment_time'		=> $this->trans_data['payment_time'],

			'business'			=> $this->trans_data['business'],
			'receiver_id'		=> $this->trans_data['receiver_id'],
			'receiver_email'	=> $this->trans_data['receiver_email'],

			'payment_status'	=> $this->trans_data['payment_status'],
			'mc_gross'			=> floatval($this->trans_data['mc_gross']),
			'mc_fee'			=> floatval($this->trans_data['mc_fee']),
			'mc_currency'		=> $this->trans_data['mc_currency'],
			'settle_amount'		=> floatval($this->trans_data['settle_amount']),
			'net_amount'		=> number_format($this->trans_data['mc_gross'] - $this->trans_data['mc_fee'], 2),
			'exchange_rate'		=> $this->trans_data['exchange_rate'],
			'settle_currency'	=> $this->trans_data['settle_currency'],
			'payment_type'		=> $this->trans_data['payment_type'],
			'payment_date'		=> $this->trans_data['payment_date'],

			'payer_id'			=> $this->trans_data['payer_id'],
			'payer_email'		=> $this->trans_data['payer_email'],
			'payer_status'		=> $this->trans_data['payer_status'],
			'first_name'		=> $this->trans_data['first_name'],
			'last_name'			=> $this->trans_data['last_name'],

//			'memo'				=> $this->trans_data['memo'],
		);

		if ($update)
		{
			$sql = 'UPDATE ' . DONATION_DATA_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE txn_id = '" . $this->trans_data['txn_id'] . "'";
			$db->sql_query($sql);
		}
		else
		{
			// insert the data
			$sql = 'INSERT INTO ' . DONATION_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);
		}
	}

	/**
	 * Setup the data list with default values.
	 */
	private function data_list()
	{
		$data_ary = array(
			'txn_id'			=> '',		// Transaction ID
			'txn_type'			=> '',		// Transaction type - Should be: 'send_money'

			'item_name'			=> '',		// $config['sitename']
			'item_number'		=> '',		// 'uid_' . $user->data['user_id'] . '_' . time()

			'business'			=> '',		// Primary merchant e-mail address
			'receiver_id'		=> '',		// Secure Merchant Account ID
			'receiver_email'	=> '',		// Merchant e-mail address

			'payment_status'	=> '',		// 'Completed'
			'mc_gross'			=> '',		// Amt recieved (before fees)
			'mc_fee'			=> '',		// Amt of fees
			'mc_currency'		=> '',		// Currency
			'settle_amount'		=> '',		// Amt recieved after currency conversion (before fees)
			'settle_currency'	=> '',		// Currency of 'settle_amount'
			'exchange_rate'		=> '',		// Exchange rate used if a currency conversion occured
			'payment_type'		=> '',		// Payment type
			'payment_date'		=> '',		// Payment Date/Time EX: '19:08:04 Oct 03, 2007 PDT'

			'payer_id'			=> '',		// PayPal sender ID
			'payer_email'		=> '',		// PayPal sender email address
			'payer_status'		=> '',		// PayPal sender status (verified, unverified?)
			'first_name'		=> '',		// First name of sender
			'last_name'			=> '',		// Last name of sender

//			'memo'				=> '',		// Memo sent by the donor
		);

		$this->trans_data['confirmed'] = false;	// used to check if the payment is confirmed

		foreach ($data_ary as $key => $default)
		{
//			if ($key === 'memo' || $key === 'item_name' || $key === 'item_name' || $key === 'first_name' || $key === 'last_name')
//			{
//				$this->trans_data[$key] = utf8_normalize_nfc(request_var($key, $default, true));
//			}
//			else
//			{
				$this->trans_data[$key] = request_var($key, $default);
//			}
		}
	}

	/**
	 * Get Response
	 *
	 * Returns the entire response from PayPal as a string including all the
	 * HTTP headers.
	 *
	 * @return string
	 */
	public function get_response()
	{
		return $this->response;
	}

	/**
	* Get Text Report
	*
	* Returns a report of the IPN transaction in plain text format. This is
	* useful in emails to order processors and system administrators. Override
	* this method in your own class to customize the report.
	*
	* @return string
	*/
	public function get_text_report()
	{
		$r = '';

		// Date and POST url
		for ($i = 0;$i < 80;$i++)
		{
			$r .= '-';
		}

		$r .= "\n[" . date('m/d/Y g:i A') . '] - ' . $this->u_paypal;

		if ($this->use_curl)
		{
			$r .= " (curl)\n";
		}
		else
		{
			$r .= " (fsockopen)\n";
		}
		
		// HTTP Response
		for ($i = 0;$i < 80;$i++)
		{
			$r .= '-';
		}

		$r .= "\n" . $this->get_response() . "\n";

		// POST vars
		for ($i = 0;$i < 80;$i++)
		{
			$r .= '-';
		}
		$r .= "\n";

		foreach ($this->trans_data as $key => $value)
		{
			$r .= str_pad($key, 25) . $value . "\n";
		}

		$r .= "\n\n";

		return $r;
	}
}

/**
* Calculate donations stats percent number
*
* @param str $type = ''
* @param int $multiplicand
* @param int $dividend
*/
function donation_stats_percent($type = '', $multiplicand, $dividend)
{
	global $template;

	$donation_stats_percent = ($multiplicand * 100) / $dividend;

	$template->assign_vars(array(
		'DONATION_' . $type	=> round($donation_stats_percent, 2),
		'S_' . $type 		=> !empty($type) ? true : false,
	));
}

/**
* PayPal donation configuration check.
*
* @param bool $is_founder = false
*/

function donation_check_configuration($is_founder = false, $is_authorised = false)
{
	global $config, $user;
	// Do we have the donation mod enabled and PayPal account set ?

	// PayPal Donation and PayPal Sandbox is disabled
	if (empty($config['donation_enable']) && empty($config['paypal_sandbox_enable']))
	{
		trigger_error($user->lang['DONATION_DISABLED'], E_USER_NOTICE);
	}

	// PayPal Donation enabled and Account ID missing
	if (!empty($config['donation_enable']) && empty($config['paypal_sandbox_enable']) && empty($config['donation_account_id']))
	{
			trigger_error($user->lang['DONATION_ADDRESS_MISSING'], E_USER_NOTICE);
	}

	// Sandbox is enabled only for founder and $is_founder is false. Or Sandbox is visible for all autorised members
	if (!empty($config['paypal_sandbox_enable']) && ((!empty($config['paypal_sandbox_founder_enable']) && !$is_founder) || (empty($config['paypal_sandbox_founder_enable']) && $is_authorised)))
	{
		// PayPal Donation disabled
		if (empty($config['donation_enable']) && !empty($config['paypal_sandbox_founder_enable']))
		{
			trigger_error($user->lang['DONATION_DISABLED'], E_USER_NOTICE);
		}

		// PayPal Donation enabled and Account ID missing
		if (!empty($config['donation_enable']) && empty($config['donation_account_id']))
		{
			trigger_error($user->lang['DONATION_ADDRESS_MISSING'], E_USER_NOTICE);
		}
	}

	// PayPal Sandbox address missing
	if (empty($config['paypal_sandbox_address']))
	{
		if (!empty($config['paypal_sandbox_enable']) && ((!empty($config['paypal_sandbox_founder_enable']) && $is_founder) || (empty($config['paypal_sandbox_founder_enable']) && $is_authorised)))
		{
			trigger_error($user->lang['SANDBOX_ADDRESS_MISSING'], E_USER_NOTICE);
		}
	}
}


/**
* PayPal donation installation check.
*
* @param bool $is_founder = false
*/

function donation_check_install($is_founder = false)
{
	global $user;

	if ($is_founder)
	{
		global $config;

		// init var
		$error = false;

		// let's check if the install is good !
		$check_vars = array(
			'donation_account_id',
			'donation_default_currency',
			'donation_default_value',
			'donation_donors_group_id',
			'donation_dropbox_enable',
			'donation_dropbox_value',
			'donation_enable',
			'donation_goal',
			'donation_goal_enable',
			'donation_group_as_default',
			'donation_install_date',
			'donation_mod_version',
			'donation_num_anonymous_donors',
			'donation_num_known_donors',
			'donation_num_transactions',
			'donation_raised',
			'donation_raised_enable',
			'donation_send_confirmation',
			'donation_stats_index_enable',
			'donation_used',
			'donation_used_enable',
			'paypal_sandbox_address',
			'paypal_sandbox_enable',
			'paypal_sandbox_founder_enable',
			);

		foreach ($check_vars as $check_var)
		{
			if (!isset($config[$check_var]))
			{
				$error = true;
			}
		}
		unset($check_var);

		if ($error)
		{
			global $phpbb_root_path, $phpEx;

			// load language file
			$user->add_lang('mods/donate');

			$installer = "{$phpbb_root_path}install_donation_mod.$phpEx";
			if (!file_exists($installer))
			{
				trigger_error($user->lang['DONATION_INSTALL_MISSING'], E_USER_ERROR);
			}

			trigger_error($user->lang('DONATION_NOT_INSTALLED', '<a href="' . append_sid($installer) . '">', '</a>'), E_USER_ERROR);
		}
	}
}

/**
* Build select for messenger send status.
*
* @param int $default = 0		ID of the default type
*/
function build_messenger_select($default = 0)
{
	return build_select(array(0 => 'NONE', 1 => 'PM', 2 => 'EMAIL', 3 => 'BOTH'), $default);
}

/**
* update transactions statistics.
*/
function update_transactions_stats()
{
	global $db;

	$sql = 'SELECT COUNT(transaction_id) AS transactions
		FROM ' . DONATION_DATA_TABLE . "
		WHERE confirmed = 1
			AND payment_status = 'Completed'";
	$result = $db->sql_query($sql);
	set_config('donation_num_transactions', (int) $db->sql_fetchfield('transactions'), true);
	$db->sql_freeresult($result);
}

/**
* update known donors statistics.
*/
function update_known_donors_stats()
{
	global $db;

	$sql = 'SELECT COUNT(DISTINCT dd.user_id) AS known_donors
		FROM ' . DONATION_DATA_TABLE . ' dd
		INNER JOIN ' . USERS_TABLE . ' u
			ON dd.user_id = u.user_id
		WHERE ' . $db->sql_in_set('u.user_type', array(USER_NORMAL, USER_FOUNDER));
	$result = $db->sql_query($sql);
	set_config('donation_num_known_donors', (int) $db->sql_fetchfield('known_donors'), true);
	$db->sql_freeresult($result);
}

/**
* update anonymous donors statistics.
*/
function update_anonymous_donors_stats()
{
	global $db;

	$sql = 'SELECT COUNT(DISTINCT dd.payer_id) AS anonymous_donors
		FROM ' . DONATION_DATA_TABLE . ' dd
		WHERE dd.user_id = ' . ANONYMOUS;
	$result = $db->sql_query($sql);
	set_config('donation_num_anonymous_donors', (int) $db->sql_fetchfield('anonymous_donors'), true);
	$db->sql_freeresult($result);
}

/**
* Generate currency list.
*
* @param int $default = 4		ID of the default currency
* @param str $type				Corresponds to 'item_type' value. Can be 'device' or 'donation_pages'
* @param str $format = ''		Determine the output format. Can be 'acp', 'default_currency' or empty
* @param str $lang_key = 'USD'	Retreive from language key file the language key nammed 'CURRENCY_DEFAULT'. If it doesn't exist, USD will be the default value.
*/
function donation_item_list($default = 4, $type, $format = '', $lang_key = 'USD')
{
	global $db;

	// Build $default_currency_check to determine the default currency
	$sql = 'SELECT item_id FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id = ' . (int) $default;
	$result = $db->sql_query($sql);
	$default_currency_check = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$item_select = '';
	if ($format == 'default_currency' && $default_currency_check)
	{
		$item_select .= ' AND item_id = ' . (int) $default;
	}

	$sql = 'SELECT item_id, item_name, item_iso_code, item_symbol
		FROM ' . DONATION_ITEM_TABLE . "
		WHERE item_type = '" . $db->sql_escape($type) . "'
			AND item_enable = 1
			$item_select
		ORDER BY left_id";
	$result = $db->sql_query($sql);

	$item_list_options = '';

	// Build output
	while ($row = $db->sql_fetchrow($result))
	{
		$selected = '';

		$row['item_id'] = (int) $row['item_id'];
		if ($row['item_id'] == (int) $default)
		{
			$selected = ' selected="selected"';
		}

		if ($format == 'acp')
		{
			// Build ACP list
			$item_list_options .= '<option value="' . $row['item_id'] . '"' . $selected . '>' . $row['item_name'] . '</option>';
		}
		elseif ($format == 'default_currency')
		{
			// Build stats default currency
			$item_list_options .= $row['item_iso_code'];
		}
		else
		{
			//Build main donation list
			$item_list_options .= '<option value="' . $row['item_iso_code'] . '"' . $selected . '>' . $row['item_symbol'] . ' ' . $row['item_iso_code'] . '</option>';
		}
	};

	$db->sql_freeresult($result);

	// Assign default value if SQL result is empty and if is a currency type
	if (empty($item_list_options) && $format == 'default_currency' && $type =='currency')
	{
		$item_list_options = $lang_key;
	}
	elseif (empty($item_list_options) && $type =='currency')
	{
		$item_list_options = '<option value="' . $lang_key . '">' . $lang_key . '</option>';
	}
	return $item_list_options;
}
?>