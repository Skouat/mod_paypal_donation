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
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package module_install
*/
class acp_donation_info
{
	function module()
	{
		return array(
			'filename'			=> 'acp_donation',
			'title'				=> 'ACP_DONATION_MOD',
			'version'			=> '1.0.1',
			'modes'		=> array(
				'configuration'		=> array('title' => 'DONATION_CONFIG', 'auth' => 'acl_a_board', 'cat' => array('ACP_DONATION_MOD')),
				'donation_pages'	=> array('title' => 'DONATION_DONATION_PAGES_CONFIG', 'auth' => 'acl_a_board', 'cat' => array('ACP_DONATION_MOD')),
				'currency'			=> array('title' => 'DONATION_CURRENCY_CONFIG', 'auth' => 'acl_a_board', 'cat' => array('ACP_DONATION_MOD')),
			),
		);
	}
	
	function install()
	{
	}

	function uninstall()
	{
	}
}

?>