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
if ( !defined('IN_PHPBB') )
{
	exit;
}

class paypal_donation_version
{
	function version()
	{
		return array(
			'author' => 'Skouat',
			'title' => 'Paypal Donation MOD',
			'tag' => 'mod_paypal_donation',
			'version' => '1.0.1',
			'file' => array('skouat31.free.fr', 'phpbb', 'mods.xml'),
		);
	}
}
