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
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

$mod_name = 'DONATION_MOD';

$version_config_name = 'donation_mod_version';
$language_file = 'mods/donate';

$versions = array(
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
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_DONATION_MOD'),

			array('acp', 'ACP_DONATION_MOD', array(
					'module_basename'	=> 'donation',
					'modes'				=> array('configuration', 'donation_pages', 'currency'),
				),
			),
		),

		'table_add' => array(
			array($table_prefix . 'donation_item', array(
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
			array($table_prefix . 'donation_item', array(
				array(
					'item_type'			=> 'donation_pages',
					'item_name'			=> 'donation_body',
					'item_iso_code'		=> '',
					'item_symbol'		=> '',
					'item_text'			=> 'DONATION_SAMPLE_LANG_KEY',
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
					'item_symbol'		=> '$',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 1,
					'right_id'			=> 2,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Euro',
					'item_iso_code'		=> 'EUR',
					'item_symbol'		=> '€',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 3,
					'right_id'			=> 4,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Pound Sterling',
					'item_iso_code'		=> 'GBP',
					'item_symbol'		=> '£',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 5,
					'right_id'			=> 6,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Yen',
					'item_iso_code'		=> 'JPY',
					'item_symbol'		=> '¥',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 7,
					'right_id'			=> 8,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Australian Dollar',
					'item_iso_code'		=> 'AUD',
					'item_symbol'		=> '$',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 9,
					'right_id'			=> 10,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Canadian Dollar',
					'item_iso_code'		=> 'CAD',
					'item_symbol'		=> '$',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 11,
					'right_id'			=> 12,
				),
				array(
					'item_type'			=> 'currency',
					'item_name'			=> 'Hong Kong Dollar',
					'item_iso_code'		=> 'HKD',
					'item_symbol'		=> '$',
					'item_text'			=> '',
					'item_enable'		=> true,
					'left_id'			=> 13,
					'right_id'			=> 14,
				),
			)),
		),
	),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

?>