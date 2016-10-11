<?php
/**
*
* info_acp_donation.php [English]
*
* @package PayPal Donation MOD
* @copyright (c) 2013 Skouat
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
// ’ « » “ ” …
//


/**
* mode: main
*/
$lang = array_merge($lang, array(
	'ACP_DONATION_MOD' => 'PayPal Donation',
));

/**
* mode: overview
*/
$lang = array_merge($lang, array(
	'DONATION_OVERVIEW'			=> 'Overview',
	'DONATION_WELCOME'			=> 'Welcome on PayPal Donation MOD',
	'DONATION_WELCOME_EXPLAIN'	=> '',

	'DONATION_STATS'			=> 'Donation statistics',
	'DONATION_INSTALL_DATE'		=> 'Install date of <strong>PayPal Donation MOD</strong>',
	'DONATION_VERSION'			=> '<strong>PayPal Donation</strong> version',

	'INFO_FSOCKOPEN'			=> 'Fsockopen',
	'INFO_CURL'					=> 'cURL',
	'INFO_DETECTED'				=> 'Detected',
	'INFO_NOT_DETECTED'			=> 'Not detected',

	'STATS_NUMBER_TRANSACTIONS'				=> 'Number of transactions',
	'STATS_TRANSACTIONS_PER_DAY'			=> 'Transactions per day',
	'STATS_NUMBER_KNOWN_DONORS'				=> 'Number of known donors',
	'STATS_KNOWN_DONORS_PER_DAY'			=> 'Known donors per day',
	'STATS_NUMBER_ANONYMOUS_DONORS'			=> 'Number of anonymous donors',
	'STATS_ANONYMOUS_DONORS_PER_DAY'		=> 'Anonymous donors per day',

	'DONATION_VERSION_NOT_UP_TO_DATE_TITLE'	=> 'Your PayPal Donation installation is not up to date.',

	'STAT_RESET_DATE'							=> 'Reset MOD Installation date',
	'STAT_RESET_DATE_EXPLAIN'					=> 'Reset installation affect statistic about the total amount calculation',
	'STAT_RESET_DATE_CONFIRM'					=> 'Are you sure you wish to reset the MOD’s installation date?',
	'STAT_RESYNC_DONORSCOUNTS'					=> 'Resynchronise donors counts',
	'STAT_RESYNC_DONORSCOUNTS_EXPLAIN'			=> 'Resynchronise all donors counts. Only anonymous donors and active members will be taken in consideration.',
	'STAT_RESYNC_DONORSCOUNTS_CONFIRM'			=> 'Are you sure you wish to resynchronise donors counts?',
	'STAT_RESYNC_TRANSACTIONSCOUNTS'			=> 'Resynchronise transactions counts',
	'STAT_RESYNC_TRANSACTIONSCOUNTS_EXPLAIN'	=> 'Only logged transactions will be taken into consideration.',
	'STAT_RESYNC_TRANSACTIONSCOUNTS_CONFIRM'	=> 'Are you sure you wish to resynchronise transactions counts?',
));

/**
* mode: configuration
*/
$lang = array_merge($lang, array(
	'DONATION_CONFIG'				=> 'Configuration',
	'DONATION_CONFIG_EXPLAIN'		=> '',
	'DONATION_SAVED'				=> 'Donation settings saved',
	'MODE_CURRENCY'					=> 'currency',
	'MODE_DONATION_PAGES'			=> 'donation pages',

	// Global Donation settings
	'DONATION_ENABLE'						=> 'Enable PayPal Donation',
	'DONATION_ENABLE_EXPLAIN'				=> 'Enable or disable the PayPal Donation MOD',
	'DONATION_ACCOUNT_ID'					=> 'PayPal account ID',
	'DONATION_ACCOUNT_ID_EXPLAIN'			=> 'Enter your PayPal email address or Marchant account ID',
	'DONATION_DEFAULT_CURRENCY'				=> 'Default currency',
	'DONATION_DEFAULT_CURRENCY_EXPLAIN'		=> 'Define which currency will be selected by default',
	'DONATION_DEFAULT_VALUE'				=> 'Default donation value',
	'DONATION_DEFAULT_VALUE_EXPLAIN'		=> 'Define which donation value will be suggested by default',
	'DONATION_DROPBOX_ENABLE'				=> 'Enable drop-down list',
	'DONATION_DROPBOX_ENABLE_EXPLAIN'		=> 'If enabled, it will replace the Textbox by a drop-down list.',
	'DONATION_DROPBOX_VALUE'				=> 'Drop-down value',
	'DONATION_DROPBOX_VALUE_EXPLAIN'		=> 'Define the numbers you want to see in the drop-down list.<br />Use <strong>comma</strong> (",") <strong>with no space</strong> to separate each values.',
	'DONATION_SEND_CONFIRMATION'			=> 'Send transaction status method',
	'DONATION_SEND_CONFIRMATION_EXPLAIN'	=> 'Transaction status can be sended by PM, e-mail or both.<br />By default this setting is disabled.',
	'NONE'									=> 'Disabled',
	'BOTH'									=> 'Both',
	'DONATION_DONORS_GROUP'					=> 'Donors group',
	'DONATION_DONORS_GROUP_EXPLAIN'			=> 'Select the group, the user will automatically be added to.<br />To disable Donors groups, hold <samp>CTRL</samp> or <samp>COMMAND</samp> and clicking on the selected choice.',
	'DONATION_GROUP_AS_DEFAULT'				=> 'Donors group as default',
	'DONATION_GROUP_AS_DEFAULT_EXPLAIN'		=> 'Enable to set the donors group as the default group for users having make a donation',

	// PayPal sandbox settings
	'SANDBOX_SETTINGS'						=> 'PayPal sandbox settings',
	'SANDBOX_ENABLE'						=> 'Sandbox testing',
	'SANDBOX_ENABLE_EXPLAIN'				=> 'Enable this option if you want use PayPal Sandbox instead of PayPal services.<br />Useful for developers/testers. All the transactions are fictitious.',
	'SANDBOX_FOUNDER_ENABLE'				=> 'Sandbox only for founder',
	'SANDBOX_FOUNDER_ENABLE_EXPLAIN'		=> 'If enabled, PayPal Sandbox will be displayed only by the board founders.',
	'SANDBOX_ADDRESS'						=> 'PayPal sandbox address',
	'SANDBOX_ADDRESS_EXPLAIN'				=> 'Define here your PayPal Sandbox Sellers e-mail address',

	// IPN settings
	'IPN_SETTINGS'							=> 'IPN Settings',
	'PAYPAL_IPN_ENABLE'						=> 'Enable IPN',
	'PAYPAL_IPN_ENABLE_EXPLAIN'				=> 'Enable this option if you want use Instant Payment Notification of PayPal services.',
	'PAYPAL_IPN_LOGGING'					=> 'Log Errors',
	'PAYPAL_IPN_LOGGING_EXPLAIN'			=> 'Log Errors and Data into /store/transaction.log',

	// Stats Donation settings
	'DONATION_STATS_SETTINGS'				=> 'Stats donation config',
	'DONATION_STATS_INDEX_ENABLE'			=> 'Display donation stats on index',
	'DONATION_STATS_INDEX_ENABLE_EXPLAIN'	=> 'Enable this if you want to display the donation stats on index',
	'DONATION_RAISED_ENABLE'				=> 'Enable donation raised',
	'DONATION_RAISED'						=> 'Donation raised',
	'DONATION_RAISED_EXPLAIN'				=> 'The current amount raised through donations',
	'DONATION_GOAL_ENABLE'					=> 'Enable donation goal',
	'DONATION_GOAL'							=> 'Donation goal',
	'DONATION_GOAL_EXPLAIN'					=> 'The total amount that you want to raise',
	'DONATION_USED_ENABLE'					=> 'Enable donation used',
	'DONATION_USED'							=> 'Donation used',
	'DONATION_USED_EXPLAIN'					=> 'The amount of donation that you have already used',

	'DONATION_CURRENCY_ENABLE'				=> 'Enable donation currency',
	'DONATION_CURRENCY_ENABLE_EXPLAIN'		=> 'Enable this option if you want to display the ISO 4217 code of default currency in Stats.',
));

/**
* mode: donation pages
* Info: language keys are prefixed with 'DONATION_DP_' for 'DONATION_DONATION_PAGES_'
*/
$lang = array_merge($lang, array(
	// Donation Page settings
	'DONATION_DP_CONFIG'			=> 'Donation pages',
	'DONATION_DP_CONFIG_EXPLAIN'	=> 'Permit to improve the rendering of customizable pages of the MOD.',

	'DONATION_DP_PAGE'				=> 'Page type',
	'DONATION_DP_LANG'				=> 'Language',

	// Donation Page Body settings
	'DONATION_BODY_SETTINGS'	=> 'Donation main page config',
	'DONATION_BODY'				=> 'Donation main page',
	'DONATION_BODY_EXPLAIN'		=> 'Enter the text you want displayed on the main donation page.',

	// Donation Success settings
	'DONATION_SUCCESS_SETTINGS'	=> 'Donation success config',
	'DONATION_SUCCESS'			=> 'Donation success',
	'DONATION_SUCCESS_EXPLAIN'	=> 'Enter the text you want displayed on the success page.',

	// Donation Cancel settings
	'DONATION_CANCEL_SETTINGS'	=> 'Donation cancel config',
	'DONATION_CANCEL'			=> 'Donation cancel',
	'DONATION_CANCEL_EXPLAIN'	=> 'Enter the text you want displayed on the cancel page.',

	// Donation Page Template vars
	'DONATION_DP_PREDEFINED_VARS'	=> 'Predefined Variables',
	'DONATION_DP_VAR_EXAMPLE'		=> 'Example',
	'DONATION_DP_VAR_NAME'			=> 'Name',
	'DONATION_DP_VAR_VAR'			=> 'Variable',

	'DONATION_DP_BOARD_CONTACT'		=> 'Board contact',
	'DONATION_DP_BOARD_EMAIL'		=> 'Board e-mail',
	'DONATION_DP_BOARD_SIG'			=> 'Board’s Signature',
	'DONATION_DP_SITE_DESC'			=> 'Site description',
	'DONATION_DP_SITE_NAME'			=> 'Sitename',
	'DONATION_DP_USER_ID'			=> 'User ID',
	'DONATION_DP_USERNAME'			=> 'Username',
));

/**
* mode: currency
* Info: language keys are prefixed with 'DONATION_DC_' for 'DONATION_DONATION_CURRENCY_'
*/
$lang = array_merge($lang, array(
	// Currency Management
	'DONATION_DC_CONFIG'			=> 'Currency management',
	'DONATION_DC_CONFIG_EXPLAIN'	=> 'Here you can manage currency',
	'DONATION_DC_NAME'				=> 'Currency name',
	'DONATION_DC_NAME_EXPLAIN'		=> 'Name of the currency.<br />(i.e. Euro)',
	'DONATION_DC_ISO_CODE'			=> 'ISO 4217 code',
	'DONATION_DC_ISO_CODE_EXPLAIN'	=> 'Alpabetic code of the currency.<br />More about ISO 4217… refer to the <a href="http://www.phpbb.com/customise/db/mod/paypal_donation_mod/faq/f_746" title="PayPal Donation MOD FAQ">PayPal Donation MOD FAQ</a> (external link)',
	'DONATION_DC_SYMBOL'			=> 'Currency symbol',
	'DONATION_DC_SYMBOL_EXPLAIN'	=> 'Define the currency symbol.<br />(i.e. € for Euro, $ for U.S. Dollar)',
	'DONATION_DC_ENABLED'			=> 'Enable currency',
	'DONATION_DC_ENABLED_EXPLAIN'	=> 'If enabled, currency will be displayed in the dropbox',
	'DONATION_DC_CREATE_CURRENCY'	=> 'Add new currency',
));

/**
* mode: transactions
* Info: language keys are prefixed with 'DONATION_DT_' for 'DONATION_DONATION_TRANSACTION_'
*/
$lang = array_merge($lang, array(
	// Main
	'DONATION_DT_LOG'					=> 'Transaction log',
	'DONATION_DT_LOG_EXPLAIN'			=> 'Here you can show transactions details',
	'DONATION_DT_TXN_ID'				=> 'Transaction ID',
	'DONATION_DT_TXN_ID_EXPLAIN'		=> '',
	'DONATION_DT_USERNAME'				=> 'Donor',
	'DONATION_DT_IPN_STATUS'			=> 'IPN Status',
	'DONATION_DT_PAYMENT_STATUS'		=> 'Payment Status',
/*
* TRANSLATORS PLEASE NOTE 
* The line below has a special note.
* "## For the translation:" followed by one "Don't" and one "Yes"
* "Don't" means do not change this column, and "Yes" means you can translate this column.
*/

##	For translate :							 Don't					Yes
	'DONATION_DT_PAYMENT_STATUS_VALUES'	=> array(
											'canceled_reversal'	=> 'Canceled_Reversal',
											'completed'			=> 'Completed',
											'created'			=> 'Created',
											'denied'			=> 'Denied',
											'expired'			=> 'Expired',
											'failed'			=> 'Failed',
											'pending'			=> 'Pending',
											'refunded'			=> 'Refunded',
											'reversed'			=> 'Reversed',
											'processed'			=> 'Processed',
											'voided'			=> 'Voided',
											),

	'DONATION_DT_PAYMENT_DATE'			=> 'Payment Date',
	'DONATION_DT_SORT_TXN_ID'			=> 'Transaction ID',
	'DONATION_DT_SORT_DONORS'			=> 'Donors',
	'DONATION_DT_SORT_IPN_STATUS'		=> 'IPN Status',
	'DONATION_DT_SORT_PAYMENT_STATUS'	=> 'Payment Status',
	'DONATION_DT_VERIFIED'				=> 'Verified',
	'DONATION_DT_UNVERIFIED'			=> 'Not verified',

	'DONATION_DT_DETAILS'				=> 'Transaction details',
	'DONATION_DT_BOARD_USERNAME'		=> 'Donors',
	'DONATION_DT_NAME'					=> 'Name',
	'DONATION_DT_PAYER_EMAIL'			=> 'Payer e-mail',
	'DONATION_DT_PAYER_ID'				=> 'Payer ID',
	'DONATION_DT_PAYER_STATUS'			=> 'Payer status',

	'DONATION_DT_RECEIVER_EMAIL'		=> 'Payment sent to',
	'DONATION_DT_RECEIVER_ID'			=> 'Reiceiver ID',

	'DONATION_DT_TOTAL_AMMOUNT'			=> 'Total amount',
	'DONATION_DT_TOTAL_AMMOUNT_EXPLAIN'	=> '',
	'DONATION_DT_FEE_AMMOUNT'			=> 'Fee amount',
	'DONATION_DT_FEE_AMMOUNT_EXPLAIN'	=> '',
	'DONATION_DT_NET_AMMOUNT'			=> 'Net amount',
	'DONATION_DT_NET_AMMOUNT_EXPLAIN'	=> '',

	'DONATION_DT_CONVERT_FROM'			=> 'Conversion from',
	'DONATION_DT_CONVERT_FROM_EXPLAIN'	=> '',
	'DONATION_DT_SETTLE_AMOUNT'			=> 'Conversion to',
	'DONATION_DT_SETTLE_AMOUNT_EXPLAIN'	=> '',
	'DONATION_DT_EXCHANGE_RATE'			=> 'Exchange rate',
	'DONATION_DT_EXCHANGE_RATE_EXPLAIN'	=> '',

	'DONATION_DT_ITEM_NAME'				=> 'Item name',
	'DONATION_DT_ITEM_NUMBER'			=> 'Item number',

	'DONATION_DT_MEMO'					=> 'Note',
	'DONATION_DT_MEMO_EXPLAIN'			=> '',
));

/**
* logs
*/
$lang = array_merge($lang, array(
	//logs
	'LOG_DONATION_UPDATED'					=> '<strong>PayPal Donation: Settings updated.</strong>',
	'LOG_DONATION_PAGES_UPDATED'			=> '<strong>PayPal Donation: Donation Pages updated.</strong>',
	'LOG_ITEM_ADDED'						=> '<strong>PayPal Donation: %1$s added</strong><br />» %2$s',
	'LOG_ITEM_UPDATED'						=> '<strong>PayPal Donation: %1$s updated</strong><br />» %2$s',
	'LOG_ITEM_REMOVED'						=> '<strong>PayPal Donation: %1$s deleted</strong>',
	'LOG_ITEM_MOVE_DOWN'					=> '<strong>PayPal Donation: Moved a %1$s. </strong> %2$s <strong>below</strong> %3$s',
	'LOG_ITEM_MOVE_UP'						=> '<strong>PayPal Donation: Moved a %1$s. </strong> %2$s <strong>above</strong> %3$s',
	'LOG_ITEM_ENABLED'						=> '<strong>PayPal Donation: %1$s enabled</strong><br />» %2$s',
	'LOG_ITEM_DISABLED'						=> '<strong>PayPal Donation: %1$s disabled</strong><br />» %2$s',
	'LOG_CLEAR_DONATION_TXN'				=> '<strong>PayPal Donation: Cleared donation transaction log</strong>',
	'LOG_STAT_RESET_DATE'					=> '<strong>PayPal Donation: Installation date reset</strong>',
	'LOG_STAT_RESYNC_DONORSCOUNTS'			=> '<strong>PayPal Donation: Donors counts resynchronised</strong>',
	'LOG_STAT_RESYNC_TRANSACTIONSCOUNTS'	=> '<strong>PayPal Donation: Transactions counts resynchronised</strong>',

	// Confirm box
	'DONATION_DC_ENABLED'		=> 'A currency has been enabled',
	'DONATION_DC_DISABLED'		=> 'A currency has been disabled.',
	'DONATION_DC_ADDED'			=> 'A new currency has been added.',
	'DONATION_DC_UPDATED'		=> 'A currency has been updated.',
	'DONATION_DC_REMOVED'		=> 'A currency has been removed.',
	'DONATION_DP_LANG_ADDED'	=> 'A donation page language has been added',
	'DONATION_DP_LANG_UPDATED'	=> 'A donation page language has been updated',
	'DONATION_DP_LANG_REMOVED'	=> 'A donation page language has been removed',

	// Errors
	'MUST_SELECT_ITEM'			=> 'The selected item does not exist',
	'DONATION_DC_ENTER_NAME'	=> 'Enter a currency name',
));
?>