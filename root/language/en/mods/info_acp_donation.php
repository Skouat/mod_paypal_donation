<?php
/**
*
* info_acp_donation.php [English]
*
* @package language
* @version $Id: $
* @copyright (c) 2007 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_DONATION_MOD'				=> 'Paypal Donation',
	'DONATION_CONFIG'				=> 'Configuration',
	'DONATION_CONFIG_EXPLAIN'		=> '',
	'DONATION_SAVED'				=> 'Donation settings saved',
	'MODE_CURRENCY'					=> 'currency',

// Global Donation settings
	'DONATION_ENABLE'						=> 'Enable Paypal Donation',
	'DONATION_ENABLE_EXPLAIN'				=> 'Enable or disable the Paypal Donation MOD',
	'DONATION_ACCOUNT_ID'					=> 'Paypal account ID',
	'DONATION_ACCOUNT_ID_EXPLAIN'			=> 'Enter your Paypal email address or Marchant account ID',
	'DONATION_DEFAULT_CURRENCY'				=> 'Default currency',
	'DONATION_DEFAULT_CURRENCY_EXPLAIN'		=> 'Define which currency will be selected by default',

// Paypal sandbox settings
	'SANDBOX_SETTINGS'						=> 'Paypal Sandbox Settings',
	'SANDBOX_ENABLE'						=> 'Sandbox Testing',
	'SANDBOX_ADDRESS'						=> 'PayPal Sandbox Address',
	'SANDBOX_ADDRESS_EXPLAIN'				=> 'Define here your Paypal Sandbox Sellers e-mail address',

// Stats Donation settings
	'DONATION_STATS_SETTINGS'				=> 'Stats Donation Config',
	'DONATION_STATS_INDEX_ENABLE'			=> 'Display Donation Stats on index',
	'DONATION_STATS_INDEX_ENABLE_EXPLAIN'	=> 'Enable this if you want to display the donation stats on index',
	'DONATION_RAISED_ENABLE'				=> 'Enable Donation Raised',
	'DONATION_RAISED'						=> 'Donation Raised',
	'DONATION_RAISED_EXPLAIN'				=> 'The current amount raised through donations',
	'DONATION_GOAL_ENABLE'					=> 'Enable Donation Goal',
	'DONATION_GOAL'							=> 'Donation Goal',
	'DONATION_GOAL_EXPLAIN'					=> 'The amount you want to raise in total',
	'DONATION_USED_ENABLE'					=> 'Enable Donation Used',
	'DONATION_USED'							=> 'Donation Used',
	'DONATION_USED_EXPLAIN'					=> 'The amount of donation that you have already used',

	'DONATION_CURRENCY_ENABLE'				=> 'Enable Donation Currency',
	'DONATION_CURRENCY_ENABLE_EXPLAIN'		=> 'Enable Donation Currency must be enabled if you want to display it',

// Donation Page settings
	'DONATION_DONATION_PAGES_CONFIG'			=> 'Donation Pages',
	'DONATION_DONATION_PAGES_CONFIG_EXPLAIN'	=> 'Permit to improve the rendering of customizable pages of the MOD',

// Donation Page Body settings
	'DONATION_BODY_SETTINGS'				=> 'Donation Page Config',
	'DONATION_BODY'							=> 'Donation page text',
	'DONATION_BODY_EXPLAIN'					=> 'Enter the text you want displayed on the main Donation page.<br /><br />HTML or Language Keys is allowed.<br />You can use either HTML code or Language Keys, but not both in the same time',

// Donation Success settings
	'DONATION_SUCCESS_SETTINGS'				=> 'Donation Success Config',
	'DONATION_SUCCESS'						=> 'Donation success text',
	'DONATION_SUCCESS_EXPLAIN'				=> 'Enter the text you want displayed on the success page<br />This is the page users are redirected to after a successfull donation.<br /><br />HTML or Language Keys is allowed.<br />You can use either HTML code or Language Keys, but not both in the same time',

// Donation Cancel settings
	'DONATION_CANCEL_SETTINGS'				=> 'Donation Cancel Config',
	'DONATION_CANCEL'						=> 'Donation cancel text',
	'DONATION_CANCEL_EXPLAIN'				=> 'Enter the text you want displayed on the cancel page<br />This is the page users are redirected to after they cancel a donation.<br /><br />HTML or Language Keys is allowed.<br />You can use either HTML code or Language Keys, but not both in the same time',

// Currency Management
	'DONATION_CURRENCY_CONFIG'				=> 'Currency Management',
	'DONATION_CURRENCY_CONFIG_EXPLAIN'		=> 'Here you can manage currency',
	'DONATION_CURRENCY_NAME'				=> 'Currency name',
	'DONATION_CURRENCY_NAME_EXPLAIN'		=> 'Name of the currency.<br />(i.e. Euro)',
	'DONATION_CURRENCY_ISO_CODE'			=> 'ISO 4217 code',
	'DONATION_CURRENCY_ISO_CODE_EXPLAIN'	=> 'Alpabetic code of the currency.<br />refer to the <a href="http://www.iso.org/iso/support/faqs/faqs_widely_used_standards/widely_used_standards_other/currency_codes/currency_codes_list-1.htm" title="official website">official website</a>',
	'DONATION_CURRENCY_SYMBOL'				=> 'Currency symbol',
	'DONATION_CURRENCY_SYMBOL_EXPLAIN'		=> 'Define the currency symbol.<br />(i.e. € for Euro, $ for U.S. Dollar)',
	'DONATION_CURRENCY_ENABLED'				=> 'Enable currency',
	'DONATION_CURRENCY_ENABLED_EXPLAIN'		=> 'If enabled, currency will be displayed in the dropbox',
	'DONATION_CREATE_CURRENCY'				=> 'Add new currency',

//logs
	'LOG_DONATION_UPDATED'	=> '<strong>Paypal Donation settings updated.</strong>',
	'LOG_ITEM_ADDED'		=> '<strong>Paypal Donation: %1$s added</strong><br />» %2$s',
	'LOG_ITEM_UPDATED'		=> '<strong>Paypal Donation: %1$s updated</strong><br />» %2$s',
	'LOG_ITEM_REMOVED'		=> '<strong>Paypal Donation: %1$s deleted</strong>',
	'LOG_ITEM_MOVE_DOWN'	=> '<strong>Paypal Donation: Moved a %1$s. </strong> %2$s <strong>below</strong> %3$s',
	'LOG_ITEM_MOVE_UP'		=> '<strong>Paypal Donation: Moved a %1$s. </strong> %2$s <strong>above</strong> %3$s',
	'LOG_ITEM_ENABLED'		=> '<strong>Paypal Donation: %1$s enabled</strong><br />» %2$s',
	'LOG_ITEM_DISABLED'		=> '<strong>Paypal Donation: %1$s disabled</strong><br />» %2$s',

//Confirm box
	'CURRENCY_ENABLED'		=> 'A currency has been enabled',
	'CURRENCY_DISABLED'		=> 'A currency has been disabled.',
	'CURRENCY_ADDED'		=> 'A new currency has been added.',
	'CURRENCY_UPDATED'		=> 'A currency has been updated.',
	'CURRENCY_REMOVED'		=> 'A currency has been removed.',

// Errors
	'MUST_SELECT_ITEM'		=> 'The selected item does not exist',
	'ENTER_CURRENCY_NAME'	=> 'Enter a currency name',
));
?>