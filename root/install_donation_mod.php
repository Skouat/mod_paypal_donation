<?php
/**
*
* @package PayPal Donation MOD
* @copyright (c) 2013 Skouat
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// name of the mod
$mod_name = 'DONATION_MOD';

// name of the config variable
$version_config_name = 'donation_mod_version';

// language file which will be included when installing
$language_file = 'mods/donate';

// logo image
$logo_img = 'images/donation/ppdm_logo_small.png';

// current time needed for 'donation_install_date'
$current_time = time();

// Options to display to the user
$options = array(
	'legend2'	=> 'WARNING',
	'welcome'	=> array('lang' => 'INSTALL_DONATION_MOD_WELCOME', 'type' => 'custom', 'function' => 'display_message', 'params' => array('INSTALL_DONATION_MOD_WELCOME_NOTE', 'error'), 'explain' => false),
	'legend3'	=> 'ACP_SUBMIT_CHANGES',
);

// array of versions and actions within each
$versions = array(
	'1.1.0' => array(
		// Add new enable/disable config entry
		'config_add' => array(
			array('donation_donors_group_id', ''),
			array('donation_group_as_default', false),
			array('donation_send_confirmation', 0),
			array('donation_num_known_donors', 0, true),
			array('donation_num_anonymous_donors', 0, true),
			array('donation_num_transactions', 0, true),
		),

		'custom'	=> 'ppdm_check_config',

		// Add the module in ACP under the mods tab
		'module_add' => array(
			array('acp', 'ACP_DONATION_MOD', array(
				'module_basename'	=> 'donation',
				'module_langname'	=> 'DONATION_DT_LOG',
				'module_mode'		=> 'transactions',
				'module_auth'		=> 'acl_a_pdm_manage',
				'after'				=> 'DONATION_DC_CONFIG',
				),
			),
		),

		'table_add' => array(
			array('phpbb_donation_data', array(
				'COLUMNS' => array(
					'transaction_id'	=> array('UINT', NULL, 'auto_increment'),
					'txn_id'			=> array('VCHAR:18', ''),
					'txn_type'			=> array('VCHAR:32', ''),
					'confirmed'			=> array('BOOL', 0),
					'user_id'			=> array('UINT', 0),
					'item_name'			=> array('VCHAR:128', ''),
					'item_number'		=> array('VCHAR:128', ''),
					'payment_time'		=> array('TIMESTAMP', 0),
					'business'			=> array('VCHAR:128', ''),
					'receiver_id'		=> array('VCHAR:16', ''),
					'receiver_email'	=> array('VCHAR:128', ''),
					'payment_status'	=> array('VCHAR:32', ''),
					'mc_gross'			=> array('DECIMAL:8', 0),
					'mc_fee'			=> array('DECIMAL:8', 0),
					'mc_currency'		=> array('VCHAR:16', ''),
					'settle_amount'		=> array('DECIMAL:8', 0),
					'settle_currency'	=> array('VCHAR:16', ''),
					'net_amount'		=> array('DECIMAL:8', 0),
					'exchange_rate'		=> array('VCHAR:16', ''),
					'payment_type'		=> array('VCHAR:16', ''),
					'payment_date'		=> array('VCHAR:32', ''),
					'payer_id'			=> array('VCHAR:16', ''),
					'payer_email'		=> array('VCHAR:128', ''),
					'payer_status'		=> array('VCHAR:16', ''),
					'first_name'		=> array('VCHAR:10', ''),
					'last_name'			=> array('VCHAR:10', ''),
//					'memo'				=> array('VCHAR', ''),
					),

				'PRIMARY_KEY'	=> 'transaction_id',
				'KEYS' => array(
					'user_id'			=> array('INDEX', 'user_id'),
					'txn_id'			=> array('INDEX', 'txn_id'),
					),
			)),
		),
	),

	'1.0.4' => array(
		// Nothing changed in this version
	),

	'1.0.3' => array(
		// Add new config entry
		'config_add' => array(
			array('donation_install_date', $current_time),
		),

		// Now to add some permission settings
		'permission_add' => array(
			array('u_pdm_use', true),
		),
		// How about we give some default permissions then as well?
		'permission_set' => array(
			// Global Role permissions
			array('ROLE_USER_FULL', 'u_pdm_use'),
		),

		// Add the module in ACP under the .MODS tab
		'module_add' => array(
			array('acp', 'ACP_DONATION_MOD', array(
				'module_basename'	=> 'donation',
				'module_langname'	=> 'DONATION_OVERVIEW',
				'module_mode'		=> 'overview',
				'module_auth'		=> 'acl_a_pdm_manage',
				'before'			=> 'DONATION_CONFIG',
				),
			),
		),

		'table_row_remove' => array(
			array('phpbb_donation_item',
				array(
					'item_type'		=> 'donation_pages',
					'item_name'		=> 'donation_draft',
				),
			),
		),

		'table_row_update' => array(
			array('phpbb_donation_item',
				array(
					'item_type'		=> 'donation_pages',
				),
				array(
					'item_iso_code'	=> $user->lang_name,
				),
			),
		),

		// remove unused language files
		'custom'	=> 'unused_language_files',

		'cache_purge' => '',
	),

	'1.0.2' => array(
		// Remove unnecessary config
		'config_remove'	=> array(
			array('donation_currency_enable', false),
		),

		// Now to add some permission settings
		'permission_add' => array(
			array('a_pdm_manage', true),
		),

		// Rename module and add new permission
		// 1st remove module
		'module_remove' => array(
			array('acp', 'ACP_DONATION_MOD', 'DONATION_CONFIG'),
			array('acp', 'ACP_DONATION_MOD', 'DONATION_DONATION_PAGES_CONFIG'),
			array('acp', 'ACP_DONATION_MOD', 'DONATION_CURRENCY_CONFIG'),
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_DONATION_MOD'),
		),
		// 2nd add them
		'module_add' => array(
			array('acp', 'ACP_CAT_DOT_MODS', array(
				'module_enabled'	=> 1,
				'module_display'	=> 1,
				'module_langname'	=> 'ACP_DONATION_MOD',
				'module_auth'		=> 'acl_a_pdm_manage',
				),
			),

			array('acp', 'ACP_DONATION_MOD', array(
				'module_basename'	=> 'donation',
				'module_langname'	=> 'DONATION_CONFIG',
				'module_mode'		=> 'configuration',
				'module_auth'		=> 'acl_a_pdm_manage',
				),
			),

			array('acp', 'ACP_DONATION_MOD', array(
				'module_basename'	=> 'donation',
				'module_langname'	=> 'DONATION_DP_CONFIG',
				'module_mode'		=> 'donation_pages',
				'module_auth'		=> 'acl_a_pdm_manage',
				'after'				=> 'DONATION_CONFIG',
				),
			),

			array('acp', 'ACP_DONATION_MOD', array(
				'module_basename'	=> 'donation',
				'module_langname'	=> 'DONATION_DC_CONFIG',
				'module_mode'		=> 'currency',
				'module_auth'		=> 'acl_a_pdm_manage',
				'after'				=> 'DONATION_DP_CONFIG',
				),
			),
		),

		// Purge cache
		'cache_purge' => '',
	),

	'1.0.1' => array(
		// Purge cache
		'cache_purge' => '',
	),

	// Version 1.0.0-RC2
	'1.0.0-RC2' => array(
		// Add new enable/disable config entry
		'config_add' => array(
			array('donation_default_value', 0),
			array('donation_dropbox_enable', false),
			array('donation_dropbox_value', '1,2,3,4,5,10,20,25,50,100'),
			array('paypal_sandbox_founder_enable', true),
		),

		'table_column_add' => array(
			array('phpbb_donation_item', 'item_text_bbcode_bitfield', array('VCHAR:255', '')),
			array('phpbb_donation_item', 'item_text_bbcode_uid', array('VCHAR:8', '')),
			array('phpbb_donation_item', 'item_text_bbcode_options', array('UINT:4', 7)),
		),

		'table_insert' => array(
			array('phpbb_donation_item',
				array(
					'item_type'					=> 'donation_pages',
					'item_name'					=> 'donation_draft',
					'item_iso_code'				=> '',
					'item_symbol'				=> '',
					'item_text'					=> '',
					'item_enable'				=> true,
					'left_id'					=> 0,
					'right_id'					=> 0,
					'item_text_bbcode_uid'		=> '',
					'item_text_bbcode_bitfield'	=> '',
					'item_text_bbcode_options'	=> 7,
				),
			),
		),

		'cache_purge' => '',
	),

	// Version 1.0.0-RC1
	'1.0.0-RC1'	=> array(
		// Add new enable/disable config entry
		'config_add' => array(
			array('donation_account_id', ''),
			array('donation_currency_enable', true),
			array('donation_default_currency', 1),
			array('donation_enable', false),
			array('donation_goal', 0),
			array('donation_goal_enable', false),
			array('donation_raised', 0),
			array('donation_raised_enable', false),
			array('donation_stats_index_enable', false),
			array('donation_used', 0),
			array('donation_used_enable', false),
			array('paypal_sandbox_enable', false),
			array('paypal_sandbox_address', ''),
		),

		// Add the module in ACP under the mods tab
		'module_add' => array(
			array('acp', 'ACP_CAT_DOT_MODS', array(
				'module_enabled'	=> 1,
				'module_display'	=> 1,
				'module_langname'	=> 'ACP_DONATION_MOD',
				'module_auth'		=> 'acl_a_board',
				),
			),

			array('acp', 'ACP_DONATION_MOD', array(
				'module_basename'	=> 'donation',
				'module_langname'	=> 'DONATION_CONFIG',
				'module_mode'		=> 'configuration',
				'module_auth'		=> 'acl_a_board',
				),
			),

			array('acp', 'ACP_DONATION_MOD', array(
				'module_basename'	=> 'donation',
				'module_langname'	=> 'DONATION_DONATION_PAGES_CONFIG',
				'module_mode'		=> 'donation_pages',
				'module_auth'		=> 'acl_a_board',
				'after'				=> 'DONATION_CONFIG',
				),
			),

			array('acp', 'ACP_DONATION_MOD', array(
				'module_basename'	=> 'donation',
				'module_langname'	=> 'DONATION_CURRENCY_CONFIG',
				'module_mode'		=> 'currency',
				'module_auth'		=> 'acl_a_board',
				'after'				=> 'DONATION_DONATION_PAGES_CONFIG',
				),
			),
		),

		'table_add' => array(
			array('phpbb_donation_item', array(
				'COLUMNS' => array(
					'item_id'			=> array('UINT', NULL, 'auto_increment'),
					'item_type'			=> array('VCHAR:16', ''),
					'item_name'			=> array('VCHAR:50', ''),
					'item_iso_code'		=> array('VCHAR:10', ''),
					'item_symbol'		=> array('VCHAR:10', ''),
					'item_text'			=> array('TEXT', ''),
					'item_enable'		=> array('BOOL', 1),
					'left_id'			=> array('UINT', 0),
					'right_id'			=> array('UINT', 0),
				),

				'PRIMARY_KEY'	=> 'item_id',
			)),
		),

		// Creating the entries
		'table_insert' => array(
			array('phpbb_donation_item', array(
				array(
					'item_type'			=> 'donation_pages',
					'item_name'			=> 'donation_body',
					'item_iso_code'		=> '',
					'item_symbol'		=> '',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 0,
					'right_id'			=> 0,
				),
				array(
					'item_type'			=> 'donation_pages',
					'item_name'			=> 'donation_success',
					'item_iso_code'		=> '',
					'item_symbol'		=> '',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 0,
					'right_id'			=> 0,
				),
				array(
					'item_type'			=> 'donation_pages',
					'item_name'			=> 'donation_cancel',
					'item_iso_code'		=> '',
					'item_symbol'		=> '',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 0,
					'right_id'			=> 0,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'U.S. Dollar',
					'item_iso_code'		=> 'USD',
					'item_symbol'		=> '&#36;', // symbol dollar
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 1,
					'right_id'			=> 2,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Euro',
					'item_iso_code'		=> 'EUR',
					'item_symbol'		=> '&#8364;', // symbol euro
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 3,
					'right_id'			=> 4,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Pound Sterling',
					'item_iso_code'		=> 'GBP',
					'item_symbol'		=> '&#163;', // symbol livre sterling
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 5,
					'right_id'			=> 6,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Yen',
					'item_iso_code'		=> 'JPY',
					'item_symbol'		=> '&#165;', // symbol yen
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 7,
					'right_id'			=> 8,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Australian Dollar',
					'item_iso_code'		=> 'AUD',
					'item_symbol'		=> '&#36;', // symbol $
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 9,
					'right_id'			=> 10,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Canadian Dollar',
					'item_iso_code'		=> 'CAD',
					'item_symbol'		=> '&#36;', // symbol $
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 11,
					'right_id'			=> 12,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Hong Kong Dollar',
					'item_iso_code'		=> 'HKD',
					'item_symbol'		=> '&#36;', // symbol $
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 13,
					'right_id'			=> 14,
				),
			)),
		),

		'cache_purge' => '',
	),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

/*
* Function called for version 1.0.3.
* 
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function unused_language_files($action, $version)
{
	global $db, $phpbb_root_path, $phpEx;

	$check_file_exists = false;

	if ($action == 'update')
	{

	$sql = 'SELECT * FROM ' . LANG_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$file_path = $phpbb_root_path . 'language/' . $row['lang_dir'] . '/mods/donate_custom.' . $phpEx;

			if (file_exists($file_path))
			{
				@unlink($file_path);
				$check_file_exists = true;
			}
		}
	}
	if ($check_file_exists)
	{
		return array('command' => 'UNUSED_LANG_FILES_TRUE', 'result' => 'SUCCESS');
	}
	else
	{
		return array('command' => 'UNUSED_LANG_FILES_FALSE', 'result' => 'SUCCESS');
	}
}

/**
* Display a message with a specified css class
*
* @param string		$lang_string	The language string to display
* @param string		$class			The css class to apply
* @return string					Formated html code
*/
function display_message($lang_string, $class)
{
	global $user;

	return '<span class="' . $class . '">' . $user->lang[$lang_string] . '</span>';
}

/*
* Function called for version 1.1.0.
* 
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function ppdm_check_config($action, $version)
{
	global $umil, $db, $config;

	// current time needed for 'prune_log_next_time'
	$donation_goal = isset($config['donation_goal']) ? $config['donation_goal'] : 0;
	$donation_raised = isset($config['donation_raised']) ? $config['donation_raised'] : 0;
	$donation_used = isset($config['donation_used']) ? $config['donation_used'] : 0;

	$config_ary = array(
		array('donation_goal', $donation_goal, true),
		array('donation_raised', $donation_raised, true),
		array('donation_used', $donation_used, true),
	);

	// Run this when uninstalling
	if ($action != 'uninstall')
	{
		foreach ($config_ary as $config_vars)
		{
			if ($umil->config_exists($config_vars[0]))
			{
				$umil->config_update(array($config_vars));

				// config_update doesn't allow to update is_dynamic. So, we update it manually.
				if (!empty($config_vars[2]))
				{
					$sql = 'UPDATE ' . CONFIG_TABLE . "
						SET is_dynamic = '" . $db->sql_escape($config_vars[2]) . "'
						WHERE config_name = '" . $db->sql_escape($config_vars[0]) . "'";
					$db->sql_query($sql);
				}
			}
			else
			{
				$umil->config_add(array($config_vars));
			}
		}
		unset($config_vars);

		return array('command' => 'PPDM_CHECK_CONFIG_INSTALL', 'result' => 'SUCCESS');
	}
}
?>