<?php
/**
*
* @package Paypal Donation MOD
* @copyright (c) 2012 Skouat
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_donation.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();
$user->add_lang('mods/donate');

// Check for mod installed
if (!isset($config['donation_enable']) || !isset($config['donation_mod_version']) || !isset($config['donation_default_currency']))
{
	if($user->data['user_type'] == USER_FOUNDER)
	{
		$installer =  append_sid("{$phpbb_root_path}install_donation_mod.$phpEx");
		$message = sprintf($user->lang['DONATION_NOT_INSTALLED'], '<a href="' . $installer . '">', '</a>');
	}
	else
	{
		$message = $user->lang['DONATION_NOT_INSTALLED_USER'];
	}
	trigger_error ($message);
}

// Do we have the donation mod enabled and paypal account set ?
if ((empty($config['donation_enable']) && empty($config['paypal_sandbox_enable'])) || (!empty($config['donation_enable']) && empty($config['donation_account_id']) && (empty($config['paypal_sandbox_enable']) || (!empty($config['paypal_sandbox_enable']) && empty($config['paypal_sandbox_founder_enable'])))))
{
	trigger_error($user->lang['DONATION_DISABLED'], E_USER_NOTICE);
}
elseif (!empty($config['paypal_sandbox_enable']) && empty($config['paypal_sandbox_address']))
{
	trigger_error($user->lang['SANDBOX_ADDRESS_MISSING'], E_USER_NOTICE);
}

// Assign $mode to template
$mode = request_var('mode', '');
$template->assign_var('MODE', $mode);

switch ($mode)
{
	case 'success':
	case 'cancel':
		// Retrieve information text of donation customizable pages
		$sql = 'SELECT item_text, item_text_bbcode_uid, item_text_bbcode_bitfield, item_text_bbcode_options
			FROM ' . DONATION_ITEM_TABLE . "
			WHERE item_name = 'donation_" . $db->sql_escape($mode) . "'
				AND item_type = 'donation_pages'";
		$result = $db->sql_query($sql);
		$donation_pages = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$donation_body = '';
		$donation_body = isset($user->lang[strtoupper($donation_pages['item_text'])]) ? $user->lang[strtoupper($donation_pages['item_text'])] : $donation_pages['item_text'];
		$donation_body = generate_text_for_display($donation_body, $donation_pages['item_text_bbcode_uid'], $donation_pages['item_text_bbcode_bitfield'], $donation_pages['item_text_bbcode_options']);

		$template->assign_vars(array(
			'DONATION_BODY'		=> $donation_body,
			'L_DONATION_TITLE'	=> $user->lang['DONATION_' . strtoupper($mode) . '_TITLE'],
		));

		page_header($user->lang['DONATION_' . strtoupper($mode) . '_TITLE']);
		$template->set_filenames(array(
			'body' => 'donate/donate_body.html')
		);
	break;

	default:
		$s_hidden_fields = '';

		// Build Paypal return URL
		$success_url = append_sid(generate_board_url(true) . $user->page['script_path'] . $user->page['page_name'], 'mode=success');
		$cancel_url = append_sid(generate_board_url(true) . $user->page['script_path'] . $user->page['page_name'], 'mode=cancel');

		// Retrieve Paypal Sandbox value
		$business = (!empty($config['paypal_sandbox_enable']) && !empty($config['paypal_sandbox_founder_enable'])) ? $config['paypal_sandbox_address'] : $config['donation_account_id'];
		$forms_url = (!empty($config['paypal_sandbox_enable']) && !empty($config['paypal_sandbox_founder_enable'])) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

		// Retrieve currency value
		$list_currency = donation_item_list((int) $config['donation_default_currency'], 'currency', '', $user->lang['CURRENCY_DEFAULT']);
		$donation_currency = donation_item_list((int) $config['donation_default_currency'], 'currency', 'default_currency', $user->lang['CURRENCY_DEFAULT']);

		// Retrieve donation value for drop-down list
		$list_donation_value = '';
		$s_dropbox = false;

		if (!empty($config['donation_dropbox_enable']) && !empty($config['donation_dropbox_value']))
		{
			$donation_arr_value = explode(',', $config['donation_dropbox_value']);

			foreach ($donation_arr_value as $value)
			{
				$int_value = (int) $value;
				if (!empty($int_value) && is_numeric($value) && ($int_value == $value))
				{
					$list_donation_value .= '<option value="' . $int_value . '">' . $int_value . '</option>';
					$s_dropbox = true;
				}
			}
		}

		// Build hidden fields
		
		$s_hidden_fields = build_hidden_fields(array(
			'cmd'			=> '_xclick',
			'business'		=> $business,
			'item_name'		=> $user->lang['DONATION_TITLE_HEAD'] . ' ' . $config['sitename'],
			'no_shipping'	=> 1,
			'return'		=> $success_url,
			'cancel_return'	=> $cancel_url,
			'item_number'	=> 'uid_' . $user->data['user_id'] . '_' . time(),
			'tax'			=> 0,
			'bn'			=> 'Board_Donate_WPS',
			));

		// Retrieve text content for DONATION_BODY 
		$sql = 'SELECT item_text, item_text_bbcode_uid, item_text_bbcode_bitfield, item_text_bbcode_options
			FROM ' . DONATION_ITEM_TABLE . "
			WHERE item_name = 'donation_body'
				AND item_type = 'donation_pages'";
		$result = $db->sql_query($sql);
		$donation_pages = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		// Check if language key page donation body exist
		$donation_body = '';
		$donation_body = isset($user->lang[strtoupper($donation_pages['item_text'])]) ? $user->lang[strtoupper($donation_pages['item_text'])] : $donation_pages['item_text'];
		$donation_body = generate_text_for_display($donation_body, $donation_pages['item_text_bbcode_uid'], $donation_pages['item_text_bbcode_bitfield'], $donation_pages['item_text_bbcode_options']);

		$template->assign_vars(array(
			'DONATION_RAISED_ENABLE'		=> (!empty($config['donation_raised_enable'])) ? true : false,
			'DONATION_RAISED'				=> (!empty($config['donation_raised'])) ? $config['donation_raised'] : 0,
			'DONATION_GOAL_ENABLE'			=> (!empty($config['donation_goal_enable'])) ? true : false,
			'DONATION_GOAL'					=> (!empty($config['donation_goal'])) ? $config['donation_goal'] : 0,
			'DONATION_USED_ENABLE'			=> (!empty($config['donation_used_enable'])) ? true : false,
			'DONATION_USED'					=> (!empty($config['donation_used'])) ? $config['donation_used'] : 0,
			'DONATION_CURRENCY_ENABLE'		=> (!empty($config['donation_currency_enable'])) ? true : false,
			'DONATION_CURRENCY'				=> $donation_currency,

			'DONATION_BODY'					=> $donation_body,
			'LIST_DONATION_CURRENCY'		=> $list_currency,
			'DONATION_DEFAULT_VALUE'		=> (!empty($config['donation_default_value'])) ? $config['donation_default_value'] : 0,
			'LIST_DONATION_VALUE'			=> $list_donation_value,

			'S_HIDDEN_FIELDS'				=> $s_hidden_fields,
			'S_DONATE_FORMS'				=> $forms_url,
			'S_DONATION_DROPBOX'			=> $s_dropbox,
			'S_SANDBOX'						=> (!empty($config['paypal_sandbox_enable']) && !empty($config['paypal_sandbox_founder_enable'])) ? true : false,
			));

		if (!empty($config['donation_goal_enable']) && (int) $config['donation_goal'] > 0)
		{
			donation_percent_stats('GOAL_NUMBER', (int) $config['donation_raised'], (int) $config['donation_goal']);
		}

		if (!empty($config['donation_used_enable']) && (int) $config['donation_raised'] > 0 && (int) $config['donation_used'] > 0)
		{
			donation_percent_stats('USED_NUMBER', (int) $config['donation_used'], (int) $config['donation_raised']);
		}

		page_header($user->lang['DONATION_TITLE']);
		$template->set_filenames(array(
			'body' => 'donate/donate_body.html')
		);
	break;
}

// Set up Navlinks
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' => $user->lang['DONATION_TITLE'],
	'U_VIEW_FORUM' => append_sid("{$phpbb_root_path}donate.$phpEx"),
));

page_footer();

?>