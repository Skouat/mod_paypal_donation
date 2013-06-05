<?php
/**
*
* info_acp_donation.php [French]
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
	'ACP_DONATION_MOD'				=> 'Paypal Donation',
	'DONATION_CONFIG'				=> 'Configuration',
	'DONATION_CONFIG_EXPLAIN'		=> '',
	'DONATION_SAVED'				=> 'Les paramètres de Paypal Donation ont été sauvegardés',
	'MODE_CURRENCY'					=> 'devise',

// Global Donation settings
	'DONATION_ENABLE'						=> 'Activer Paypal Donation',
	'DONATION_ENABLE_EXPLAIN'				=> 'Active ou désactive le MOD Paypal Donation.',
	'DONATION_ACCOUNT_ID'					=> 'ID du compte Paypal',
	'DONATION_ACCOUNT_ID_EXPLAIN'			=> 'Saisir l’adresse email ou l’ID de compte marchand.',
	'DONATION_DEFAULT_CURRENCY'				=> 'Devise par défaut',
	'DONATION_DEFAULT_CURRENCY_EXPLAIN'		=> 'Défini quelle devise sera sélectionnée par défaut.',
	'DONATION_DEFAULT_VALUE'				=> 'Valeur de don par défaut',
	'DONATION_DEFAULT_VALUE_EXPLAIN'		=> 'Défini quelle valeur de don sera suggérée par défaut.',
	'DONATION_DROPBOX_ENABLE'				=> 'Activer la liste déroulante',
	'DONATION_DROPBOX_ENABLE_EXPLAIN'		=> 'Si activée, elle remplacera la zonte de texte par un menu déroulant.',
	'DONATION_DROPBOX_VALUE'				=> 'Valeurs de la liste déroulante',
	'DONATION_DROPBOX_VALUE_EXPLAIN'		=> 'Définissez les nombres que vous voulez voir dans la liste déroulante.<br />Séparez chaques valeurs par une virgule (",") et sans espaces.',

// Paypal sandbox settings
	'SANDBOX_SETTINGS'						=> 'Paramètres Paypal Sandbox',
	'SANDBOX_ENABLE'						=> 'Tester avec Paypal',
	'SANDBOX_ENABLE_EXPLAIN'				=> 'Activez cette option si vous voulez utiliser Paypal Sandbox au lieu des Services Paypal.<br />Pratique pour les développeurs/testeurs. Toutes les transactions sont fictives.',
	'SANDBOX_FOUNDER_ENABLE'				=> 'Sandbox pour les fondateurs',
	'SANDBOX_FOUNDER_ENABLE_EXPLAIN'		=> 'Si activé, Paypal Sandbox ne sera visible que par les fondateurs du forum.',
	'SANDBOX_ADDRESS'						=> 'Addresse PayPal Sandbox',
	'SANDBOX_ADDRESS_EXPLAIN'				=> 'Inscrire votre addresse e-mail de vendeur Paypal Sandbox',

// Stats Donation settings
	'DONATION_STATS_SETTINGS'				=> 'Paramètres des statistiques',
	'DONATION_STATS_INDEX_ENABLE'			=> 'Statistiques des dons sur l’index',
	'DONATION_STATS_INDEX_ENABLE_EXPLAIN'	=> 'Activez cette option si vous voulez afficher les statistiques des dons sur l’index du forum',
	'DONATION_RAISED_ENABLE'				=> 'Activer dons recueillis',
	'DONATION_RAISED'						=> 'Dons recueillis',
	'DONATION_RAISED_EXPLAIN'				=> 'Inscrire le montant total des dons actuellement reçus',
	'DONATION_GOAL_ENABLE'					=> 'Activer Objectif des dons',
	'DONATION_GOAL'							=> 'Objectif des dons',
	'DONATION_GOAL_EXPLAIN'					=> 'Inscrire le montant total des dons à atteindre',
	'DONATION_USED_ENABLE'					=> 'Activer dons utilisés',
	'DONATION_USED'							=> 'Dons Utilisés',
	'DONATION_USED_EXPLAIN'					=> 'Inscrire le montant des dons déjà utilisés',

	'DONATION_CURRENCY_ENABLE'				=> 'Activer Devise des dons',
	'DONATION_CURRENCY_ENABLE_EXPLAIN'		=> 'Activez cette option, pour rendre visible le Code ISO 4217 de la devise défini par défaut dans les statistiques des dons',

// Donation Page settings
	'DONATION_DONATION_PAGES_CONFIG'			=> 'Donation pages',
	'DONATION_DONATION_PAGES_CONFIG_EXPLAIN'	=> 'Permet d’améliorer le rendu des pages personalisables du MOD',

// Donation Page Draft settings
	'DONATION_DRAFT_PREVIEW'				=> 'Aperçu de la pages de dons',
	'DONATION_DRAFT_SETTINGS'				=> 'Simulation de page de dons',
	'DONATION_DRAFT_EXPLAIN'				=> 'Rédiger ici votre texte pour la Page des dons',

// Donation Page Body settings
	'DONATION_BODY_SETTINGS'				=> 'Paramètres de la Page des dons',
	'DONATION_BODY'							=> 'Texte de la page des dons',
	'DONATION_BODY_EXPLAIN'					=> 'Saisir le texte que vous souhaitez afficher sur la page des dons.<br /><br />Le <strong>BBcode</strong> et les <strong>Clés de langues</strong> sont autorisés<br />Vous pouvez utiliser les BBcodes ou les Clés de langue, mais pas les deux en même temps.<br />Si besoin, Clé de langues à utiliser : <strong>CUSTOM_DONATION_BODY</strong>',
	'COPY_TO_DONATION_BODY'					=> 'Copier vers Page des dons',

// Donation Success settings
	'DONATION_SUCCESS_SETTINGS'				=> 'Paramètres de la page des dons validés',
	'DONATION_SUCCESS'						=> 'Texte de la page des dons validés',
	'DONATION_SUCCESS_EXPLAIN'				=> 'Saisir le texte que vous souhaitez afficher sur la page des dons validés<br />Après avoir reçus un dons avec succès, les membres seront redirigés sur cette page.<br /><br />Le <strong>BBcode</strong> et les <strong>Clés de langues</strong> sont autorisés<br />Vous pouvez utiliser les BBcodes ou les Clés de langues, mais pas les deux en même temps.<br />Si besoin, Clé de langue à utiliser : <strong>CUSTOM_DONATION_SUCCESS</strong>',
	'COPY_TO_DONATION_SUCCESS'				=> 'Copier vers Dons validés',

// Donation Cancel settings
	'DONATION_CANCEL_SETTINGS'				=> 'Paramètres de la page des dons annulés',
	'DONATION_CANCEL'						=> 'Texte de la page des dons annulés',
	'DONATION_CANCEL_EXPLAIN'				=> 'Saisir le texte que vous souhaitez afficher sur la page des dons annulés<br />les membres seront redirigés sur cette page s’ils abandonnent une donnation depuis le site Paypal.<br /><br />Le <strong>BBcode</strong> et les <strong>Clés de langues</strong> sont autorisés<br />Vous pouvez utiliser les BBcodes ou les Clés de langues, mais pas les deux en même temps.<br />Si besoin, Clé de langue à utiliser : <strong>CUSTOM_DONATION_CANCEL</strong>',
	'COPY_TO_DONATION_CANCEL'				=> 'Copier vers Dons annulés',

// Currency Management
	'DONATION_CURRENCY_CONFIG'				=> 'Gestion des devises',
	'DONATION_CURRENCY_CONFIG_EXPLAIN'		=> 'Permet de gérer les devises pour faire un don',
	'DONATION_CURRENCY_NAME'				=> 'Nom de la devise',
	'DONATION_CURRENCY_NAME_EXPLAIN'		=> 'Exemple : Euro',
	'DONATION_CURRENCY_ISO_CODE'			=> 'Code ISO 4217',
	'DONATION_CURRENCY_ISO_CODE_EXPLAIN'	=> 'Code alpabetique de la devise.<br />Consulter le <a href="http://www.iso.org/iso/support/faqs/faqs_widely_used_standards/widely_used_standards_other/currency_codes/currency_codes_list-1.htm" title="official website">site web officiel</a> pour connaitre le code ISO 4217',
	'DONATION_CURRENCY_SYMBOL'				=> 'Symbole de la devise',
	'DONATION_CURRENCY_SYMBOL_EXPLAIN'		=> 'Inscire le symbole de la devise.<br />Exemple : <strong>€</strong> pour Euro',
	'DONATION_CURRENCY_ENABLED'				=> 'Activer la devise',
	'DONATION_CURRENCY_ENABLED_EXPLAIN'		=> 'Si activée, la devise sera disponible dans les listes de sélection',
	'DONATION_CREATE_CURRENCY'				=> 'Ajouter une nouvelle devise',

//logs
	'LOG_DONATION_UPDATED'			=> '<strong>Paypal Donation: Configuration mise à jour.</strong>',
	'LOG_DONATION_PAGES_UPDATED'	=> '<strong>Paypal Donation: Pages de dons mises à jour.</strong>',
	'LOG_ITEM_ADDED'				=> '<strong>Paypal Donation: %1$s ajouté(e)</strong><br />» %2$s',
	'LOG_ITEM_UPDATED'				=> '<strong>Paypal Donation: %1$s ajouté(e)</strong><br />» %2$s',
	'LOG_ITEM_REMOVED'				=> '<strong>Paypal Donation: %1$s supprimé(e)</strong>',
	'LOG_ITEM_MOVE_DOWN'			=> '<strong>Paypal Donation: Déplacement de la %1$s. </strong> %2$s <strong>après</strong> %3$s',
	'LOG_ITEM_MOVE_UP'				=> '<strong>Paypal Donation: Déplacement de la %1$s. </strong> %2$s <strong>avant</strong> %3$s',
	'LOG_ITEM_ENABLED'				=> '<strong>Paypal Donation: %1$s activé(e)</strong><br />» %2$s',
	'LOG_ITEM_DISABLED'				=> '<strong>Paypal Donation: %1$s désactivé(e)</strong><br />» %2$s',

//Confirm box
	'CURRENCY_ENABLED'		=> 'Une devise a été activée',
	'CURRENCY_DISABLED'		=> 'Une devise a été désactivée.',
	'CURRENCY_ADDED'		=> 'Une nouvelle devise a été ajoutée.',
	'CURRENCY_UPDATED'		=> 'Une devise a été mise à jour.',
	'CURRENCY_REMOVED'		=> 'Une devise a été supprimée.',

// Errors
	'MUST_SELECT_ITEM'		=> 'L’objet sélectionné n’existe pas',
	'ENTER_CURRENCY_NAME'	=> 'Entrez un nom de devise',
));
?>