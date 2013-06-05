<?php
/**
*
* donate_custom.php [English]
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

/*
* Custom language key for Pages Donations personnalisation
*/
$lang = array_merge($lang, array(
	'CUSTOM_DONATION_BODY'		=> '<h2>Ceci est un exemple de texte basé sur la clé de langue <strong>CUSTOM_DONATION_BODY</strong></h2><br /><p>En savoir plus sur “<a href="http://www.phpbb.com/customise/db/mod/paypal_donation_mod/faq/f_749" title="Comment utiliser les clés de langues?">Comment utiliser les clés de langues ?</a>” (lien externe en anglais).</p>',
	'CUSTOM_DONATION_SUCCESS'	=> '<h2>Ceci est un exemple de texte basé sur la clé de langue <strong>CUSTOM_DONATION_SUCCESS</strong></h2><br /><p>En savoir plus sur “<a href="http://www.phpbb.com/customise/db/mod/paypal_donation_mod/faq/f_749" title="Comment utiliser les clés de langues?">Comment utiliser les clés de langues ?</a>” (lien externe en anglais).</p>',
	'CUSTOM_DONATION_CANCEL'	=> '<h2>Ceci est un exemple de texte basé sur la clé de langue <strong>CUSTOM_DONATION_CANCEL</strong></h2><br /><p>En savoir plus sur “<a href="http://www.phpbb.com/customise/db/mod/paypal_donation_mod/faq/f_749" title="Comment utiliser les clés de langues?">Comment utiliser les clés de langues ?</a>” (lien externe en anglais).</p>',
));

?>