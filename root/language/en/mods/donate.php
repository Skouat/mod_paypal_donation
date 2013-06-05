<?php
/**
*
* donate.php [English]
*
* @package Paypal Donation MOD
* @copyright (c) 2012 Skouat
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
// Notice
	'DONATION_DISABLED'				=> 'Sorry, the Donation page is currently unavailable.',
	'DONATION_NOT_INSTALLED'		=> 'Paypal Donation MOD database entries are missing.<br />Please run the %sinstaller%s to make the database changes for the MOD.',
	'DONATION_NOT_INSTALLED_USER'	=> 'The Donation page is not installed. Please notify the board founder.',
	'SANDBOX_ADDRESS_MISSING'		=> 'Sorry, Paypal Sandbox is enabled but some settings are missing. Please notify the board founder.',

// Image alternative text
	'IMG_DONATE'					=> 'donate',
	'IMG_LOADER'					=> 'loading',

// Default Currency
	'CURRENCY_DEFAULT'		=> 'USD', // Note : If you remove from ACP ALL currencies, this value will be defined as the default currency.

// Stats
	'DONATE_RECEIVED'			=> 'We received',
	'DONATE_RECEIVED_IN'		=> 'in donations.',
	'DONATE_NOT_RECEIVED'		=> 'We haven’t received any donations.',
	'DONATE_USED'				=> 'We used',
	'DONATE_ALREADY_RECEIVE'	=> 'already received.',
	'DONATE_NOT_USED'			=> 'We haven’t used any donations.',
	'DONATE_GOAL_RAISE'			=> 'Our goal is to raise',
	'DONATE_GOAL_REACHED'		=> 'Our goal donation was reached.',
	'DONATE_NO_GOAL'			=> 'We haven’t defined a donation goal.',
	'DONATE_USED_EXCEEDED'		=> 'All your donations have been used.',
	'DONATE_USED_OF'			=> 'of your donations on',

// Pages
	'DONATION_TITLE'			=> 'Make a Donation',
	'DONATION_TITLE_HEAD'		=> 'Make a Donation to',
	'DONATION_CANCEL_TITLE'		=> 'Donation Canceled',
	'DONATION_SUCCESS_TITLE'	=> 'Donation Successfull',
	'DONATION_CONTACT_PAYPAL'	=> 'Connecting to Paypal - Please Wait...',
	'SANDBOX_TITLE'				=> 'Test Paypal Donation with Paypal Sandbox',

	'DONATION_INDEX'			=> 'Donations',
));

// UMIL
$lang = array_merge($lang, array(
	'INSTALL_DONATION_MOD'				=> 'Install Donation Mod',
	'INSTALL_DONATION_MOD_CONFIRM'		=> 'Are you ready to install the Donation Mod?',
	'DONATION_MOD'						=> 'Donation Mod',
	'DONATION_MOD_EXPLAIN'				=> 'Install Donation Mod database changes with UMIL auto method.',
	'UNINSTALL_DONATION_MOD'			=> 'Uninstall Donation Mod',
	'UNINSTALL_DONATION_MOD_CONFIRM'	=> 'Are you ready to uninstall the Donation Mod? All settings and data saved by this mod will be removed!',
	'UPDATE_DONATION_MOD'				=> 'Update Donation Mod',
	'UPDATE_DONATION_MOD_CONFIRM'		=> 'Are you ready to update the Donation Mod?',
));

// Custom language key for Pages Donations personnalisation
$lang = array_merge($lang, array(
	'CUSTOM_DONATION_BODY'		=> '<h2>This is an example of text based on the language keys</h2><br /><p> Edit <strong>CUSTOM_DONATION_BODY</strong> Language Key in <strong>/language/en/mods/donate.php</strong>.<br /> Write the text you want displayed in your custom pages.</p>',
	'CUSTOM_DONATION_SUCCESS'	=> '<h2>This is an example of text based on the language keys</h2><br /><p> Edit <strong>CUSTOM_DONATION_SUCCESS</strong> Language Key in <strong>/language/en/mods/donate.php</strong>.<br /> Write the text you want displayed in your custom pages.</p>',
	'CUSTOM_DONATION_CANCEL'	=> '<h2>This is an example of text based on the language keys</h2><br /><p> Edit <strong>CUSTOM_DONATION_CANCEL</strong> Language Key in <strong>/language/en/mods/donate.php</strong>.<br /> Write the text you want displayed in your custom pages.</p>',
));

?>