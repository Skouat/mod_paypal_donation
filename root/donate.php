<?php
/**
*
* @package phpBB3
* @version $Id: $
* @copyright (c) 2007 phpBB Group
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
$user->setup('mods/donate');

// Check for mod installed
if (!isset($config['donation_enable']) || !isset($config['donation_mod_version']))
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
if (empty($config['donation_enable']) || empty($config['donation_account_id']))
{
	trigger_error($user->lang['DONATION_DISABLED'], E_USER_NOTICE);
}
elseif (!empty($config['paypal_sandbox_enable']) && empty($config['paypal_sandbox_address']))
{
	trigger_error($user->lang['SANDBOX_ADDRESS_MISSING'], E_USER_NOTICE);
}

// Assign $mode to template
$mode = request_var('mode', '');
$template->assign_vars(array(
	'MODE'		=> $mode,
));

switch ($mode)
{
	case 'success':
	case 'cancel':
	// Retrieve information text of donation customizable pages
		$sql = 'SELECT item_text
			FROM ' . DONATION_ITEM_TABLE . '
			WHERE item_name = "donation_' . $mode . '"
				AND item_type = "donation_pages"';
		$donation_pages = get_info($sql);

		$template->assign_vars(array(
			'DONATION_BODY'		=> html_entity_decode(isset($user->lang[$donation_pages['item_text']]) ? $user->lang[$donation_pages['item_text']] : $donation_pages['item_text']),
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
		$success_url =  append_sid(generate_board_url(true) . $user->page['script_path'] . $user->page['page_name'], 'mode=success');
		$cancel_url = append_sid(generate_board_url(true) . $user->page['script_path'] . $user->page['page_name'], 'mode=cancel');

		// Retrieve Paypal Sandbox value
		$business = (!empty($config['paypal_sandbox_enable']) && !empty($config['paypal_sandbox_address'])) ? $config['paypal_sandbox_address'] : $config['donation_account_id'];
		$forms_address = (!empty($config['paypal_sandbox_enable'])) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

		// Retrieve currency value

		$list_currency = donation_item_list($config['donation_default_currency'], 'currency', '', $user->lang['CURRENCY_DEFAULT']);
		$donation_currency = donation_item_list($config['donation_default_currency'], 'currency', 'default_currency', $user->lang['CURRENCY_DEFAULT']);

		// Build hidden fields
		$s_hidden_fields .= '<input type="hidden" name="cmd" value="_xclick" />';
		$s_hidden_fields .= '<input type="hidden" name="business" value="' . $business . '" />';
		$s_hidden_fields .= '<input type="hidden" name="item_name" value="' .  $user->lang['DONATION_TITLE_HEAD'] . ' ' . $config['sitename'] . '" />';
		$s_hidden_fields .= '<input type="hidden" name="no_shipping" value="1" />';
		$s_hidden_fields .= '<input type="hidden" name="return" value="' . $success_url . '" />';
		$s_hidden_fields .= '<input type="hidden" name="cancel_return" value="' . $cancel_url . '" />';
		$s_hidden_fields .= '<input type="hidden" name="item_number" value="uid_' . $user->data['user_id'] . '_' . time() . '" />';
		$s_hidden_fields .= '<input type="hidden" name="tax" value="0" />';
		$s_hidden_fields .= '<input type="hidden" name="bn" value="Board_Donate_WPS_FR" />';

		// Retrieve text content for DONATION_BODY 
		$sql = 'SELECT item_text
			FROM ' . DONATION_ITEM_TABLE . '
			WHERE item_name = "donation_body"
				AND item_type = "donation_pages"';
		$donation_pages = get_info($sql);

		$template->assign_vars(array(
			'DONATION_RAISED_ENABLE'		=> $config['donation_raised_enable'],
			'DONATION_RAISED'				=> $config['donation_raised'],
			'DONATION_GOAL_ENABLE'			=> $config['donation_goal_enable'],
			'DONATION_GOAL'					=> $config['donation_goal'],
			'DONATION_USED_ENABLE'			=> $config['donation_used_enable'],
			'DONATION_USED'					=> $config['donation_used'],
			'DONATION_CURRENCY_ENABLE'		=> $config['donation_currency_enable'],
			'DONATION_CURRENCY'				=> $donation_currency,
			'DONATION_BODY'					=> html_entity_decode(isset($user->lang[$donation_pages['item_text']]) ? $user->lang[$donation_pages['item_text']] : $donation_pages['item_text']),
			'LIST_DONATION_CURRENCY'		=> $list_currency,

			'S_HIDDEN_FIELDS'				=> $s_hidden_fields,
			'S_DONATE_FORMS'				=> $forms_address,
			'S_SANDBOX'						=> (!empty($config['paypal_sandbox_enable'])) ? true : false,
			));

		if ($config['donation_goal_enable'])
		{
			donation_goal_number();
		}

		if ($config['donation_used_enable'])
		{
			donation_used_number();
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
'U_VIEW_FORUM' => append_sid("{$phpbb_root_path}donate.$phpEx"))
);

page_footer();

?>