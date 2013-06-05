<?php
/**
*
* donate.php [French]
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
	'DONATION_DISABLED'				=> 'Désolé, la page des Dons n’est pas disponible',
	'DONATION_NOT_INSTALLED'		=> 'Des entrées dans la base de données pour le MOD <strong>Paypal Donation</strong> sont manquantes.<br />Merci de lancer à nouveau le %sfichier d’installation%s pour corriger le problème.',
	'DONATION_NOT_INSTALLED_USER'	=> 'La page des Dons n’est pas installée. Merci de contacter l’administrateur du forum.',
	'SANDBOX_ADDRESS_MISSING'		=> 'Désolé, Paypal Sandbox est activé mais certains paramètres sont manquants. Merci de contacter l’administrateur du forum.',

// Default Currency
	'CURRENCY_DEFAULT'		=> 'EUR', // Note : Si depuis l’ACP vous supprimez toutes les devises, cette valeur sera définie comme valeur par défaut.

// Stats
	'DONATION_RECEIVED'			=> 'Nous avons reçu',
	'DONATION_RECEIVED_IN'		=> 'de dons.',
	'DONATION_NOT_RECEIVED'		=> 'Nous n’avons pas encore reçu de dons.',
	'DONATION_USED'				=> 'Les dons ont été utilisé à hautre de ',
	'DONATION_USED_FROM'		=> 'sur les',
	'DONATION_ALREADY_RECEIVE'	=> 'déjà reçus.',
	'DONATION_NOT_USED'			=> 'Les dons n’ont pas été utilisés.',
	'DONATION_GOAL_RAISE'		=> 'Notre objectif est d’obtenir',
	'DONATION_GOAL_REACHED'		=> 'L’objectif de don a été atteint.',
	'DONATION_NO_GOAL'			=> 'Nous n’avons pas défini d’objectif de dons à atteindre.',
	'DONATION_USED_EXCEEDED'	=> 'Tous les dons ont été utilisés.',
	'DONATION_USED_OF'			=> 'de vos dons',

// Pages
	'DONATION_TITLE'			=> 'Faire un don',
	'DONATION_TITLE_HEAD'		=> 'Faire un don à',
	'DONATION_CANCEL_TITLE'		=> 'Dons Annulé',
	'DONATION_SUCCESS_TITLE'	=> 'Dons Validé',
	'DONATION_CONTACT_PAYPAL'	=> 'Connexion à Paypal - Merci de patienter...',
	'SANDBOX_TITLE'				=> 'Tester Paypal Donation avec Paypal Sandbox',

	'DONATION_INDEX'			=> 'Faire un don',

	'DONATION_SAMPLE_LANG_KEY'	=> '<h2>Ceci est un example de texte basé sur les clés de langues</h2><br /><p> Editez la clé de langue <strong>DONATION_SAMPLE_LANG_KEY</strong> ou créez vos propres clés de langues depuis le fichier <strong>/language/fr/mods/donate.php</strong>.<br /> Saisir le texte que vous souhaitez voir apparaitre sur vos pages personnalisées<br />Vous pouvez utiliser du code HTML</p>',
));

$lang = array_merge($lang, array(
	'INSTALL_DONATION_MOD'				=> 'Installer Donation Mod',
	'INSTALL_DONATION_MOD_CONFIRM'		=> 'Êtes-vous prêt à installer Paypal Donation Mod?',
	'DONATION_MOD'						=> 'Paypal Donation Mod',
	'DONATION_MOD_EXPLAIN'				=> 'UMIL effectuera automatiquement, dans la base de données, tous les changements nécessaires pour le MOD Paypal Donation.',
	'UNINSTALL_DONATION_MOD'			=> 'Désinstaller Paypal Donation Mod',
	'UNINSTALL_DONATION_MOD_CONFIRM'	=> 'Êtes-vous prêt à désinstaller Paypal Donation Mod? Tous les réglages et données sauvegardées par ce MOD seront supprimés !',
	'UPDATE_DONATION_MOD'				=> 'Mettre à jour Paypal Donation Mod',
	'UPDATE_DONATION_MOD_CONFIRM'		=> 'Êtes-vous prêt à mettre à jour Paypal Donation Mod?',
));

?>