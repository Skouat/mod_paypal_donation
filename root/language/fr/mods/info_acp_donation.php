<?php
/**
*
* info_acp_donation.php [French]
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
	'DONATION_OVERVIEW'			=> 'Général',
	'DONATION_WELCOME'			=> 'Bienvenue sur PayPal Donation MOD',
	'DONATION_WELCOME_EXPLAIN'	=> '',

	'DONATION_STATS'			=> 'Statistics des dons',
	'DONATION_INSTALL_DATE'		=> 'Date d’installation de <strong>PayPal Donation</strong>',
	'DONATION_VERSION'			=> 'Version de <strong>PayPal Donation</strong>',

	'INFO_FSOCKOPEN'			=> 'Fsockopen',
	'INFO_CURL'					=> 'cURL',
	'INFO_DETECTED'				=> 'Détecté',
	'INFO_NOT_DETECTED'			=> 'Non détecté',

	'STATS_NUMBER_TRANSACTIONS'				=> 'Nombre de transactions',
	'STATS_TRANSACTIONS_PER_DAY'			=> 'Moyenne journalière des transactions',
	'STATS_NUMBER_KNOWN_DONORS'				=> 'Nombre de donateurs connus',
	'STATS_KNOWN_DONORS_PER_DAY'			=> 'Moyenne journalière des donateurs connus',
	'STATS_NUMBER_ANONYMOUS_DONORS'			=> 'Nombre de donateurs anonymes',
	'STATS_ANONYMOUS_DONORS_PER_DAY'		=> 'Moyenne journalière des donateurs anonymes',

	'DONATION_VERSION_NOT_UP_TO_DATE_TITLE'	=> 'Votre installation de PayPal Donation n’est pas à jour.',

	'STAT_RESET_DATE'							=> 'Réinitialiser la date d’installation du MOD',
	'STAT_RESET_DATE_EXPLAIN'					=> 'La réinitialisation de la date d’installation affecte les statistiques du MOD',
	'STAT_RESET_DATE_CONFIRM'					=> 'Etes-vous sûr de vouloir réinitialiser la data d’installation du MOD ?',
	'STAT_RESYNC_DONORSCOUNTS'					=> 'Resynchroniser les compteurs de donateurs',
	'STAT_RESYNC_DONORSCOUNTS_EXPLAIN'			=> 'Resynchronise tous les compteurs de donnateurs. Seulement les donateurs actif et anonymes seront prises en considération.',
	'STAT_RESYNC_DONORSCOUNTS_CONFIRM'			=> 'Etes-vous sûr de vouloir resynchroniser le compteurs de donateurs ?',
	'STAT_RESYNC_TRANSACTIONSCOUNTS'			=> 'Resynchroniser les compteurs de transactions',
	'STAT_RESYNC_TRANSACTIONSCOUNTS_EXPLAIN'	=> 'Seul les transactions journalisées seront prises en considération.',
	'STAT_RESYNC_TRANSACTIONSCOUNTS_CONFIRM'	=> 'Etes-vous sûr de vouloir resynchroniser les compteurs de transactions ?',
));

/**
* mode: configuration
*/
$lang = array_merge($lang, array(
	'DONATION_CONFIG'				=> 'Configuration',
	'DONATION_CONFIG_EXPLAIN'		=> '',
	'DONATION_SAVED'				=> 'Les paramètres de PayPal Donation ont été sauvegardés',
	'MODE_CURRENCY'					=> 'devise',
	'MODE_DONATION_PAGES'			=> 'pages de dons',

	// Global Donation settings
	'DONATION_ENABLE'						=> 'Activer PayPal Donation',
	'DONATION_ENABLE_EXPLAIN'				=> 'Active ou désactive le MOD PayPal Donation.',
	'DONATION_ACCOUNT_ID'					=> 'ID du compte PayPal',
	'DONATION_ACCOUNT_ID_EXPLAIN'			=> 'Saisir l’adresse email ou l’ID de compte marchand.',
	'DONATION_DEFAULT_CURRENCY'				=> 'Devise par défaut',
	'DONATION_DEFAULT_CURRENCY_EXPLAIN'		=> 'Défini quelle devise sera sélectionnée par défaut.',
	'DONATION_DEFAULT_VALUE'				=> 'Valeur de don par défaut',
	'DONATION_DEFAULT_VALUE_EXPLAIN'		=> 'Défini quelle valeur de don sera suggérée par défaut.',
	'DONATION_DROPBOX_ENABLE'				=> 'Activer la liste déroulante',
	'DONATION_DROPBOX_ENABLE_EXPLAIN'		=> 'Si activée, elle remplacera la zonte de texte par un menu déroulant.',
	'DONATION_DROPBOX_VALUE'				=> 'Valeurs de la liste déroulante',
	'DONATION_DROPBOX_VALUE_EXPLAIN'		=> 'Définissez les nombres que vous voulez voir dans la liste déroulante.<br />Séparez chaques valeurs par une virgule (",") et sans espaces.',
	'DONATION_SEND_CONFIRMATION'			=> 'Envoyer le statut de la transaction',
	'DONATION_SEND_CONFIRMATION_EXPLAIN'	=> 'Envoi une confirmation sur le statut de la transaction par MP, E-mail ou les deux.<br />Par défaut cette option est désactivée',
	'NONE'									=> 'Désactivé',
	'BOTH'									=> 'Les deux',
	'DONATION_DONORS_GROUP'					=> 'Groupe donateurs',
	'DONATION_DONORS_GROUP_EXPLAIN'			=> 'Sélectioner un groupe, le membre donateur y sera automatiquement ajouté.<br />Désactiver Groupe donateurs en maintenant <samp>CTRL</samp> ou <samp>COMMAND</samp> et en cliquant sur le choix sélectionné.',
	'DONATION_GROUP_AS_DEFAULT'				=> 'Groupe donateurs par défaut',
	'DONATION_GROUP_AS_DEFAULT_EXPLAIN'		=> 'Activer pour définir le groupe donateurs comme groupe par défaut pour les utilisateurs ayant fait un don',

	// PayPal sandbox settings
	'SANDBOX_SETTINGS'						=> 'Paramètres PayPal Sandbox',
	'SANDBOX_ENABLE'						=> 'Tester avec PayPal',
	'SANDBOX_ENABLE_EXPLAIN'				=> 'Activez cette option si vous voulez utiliser PayPal Sandbox au lieu des Services PayPal.<br />Pratique pour les développeurs/testeurs. Toutes les transactions sont fictives.',
	'SANDBOX_FOUNDER_ENABLE'				=> 'Sandbox pour les fondateurs',
	'SANDBOX_FOUNDER_ENABLE_EXPLAIN'		=> 'Si activé, PayPal Sandbox ne sera visible que par les fondateurs du forum.',
	'SANDBOX_ADDRESS'						=> 'Adresse PayPal Sandbox',
	'SANDBOX_ADDRESS_EXPLAIN'				=> 'Inscrire votre addresse e-mail de vendeur PayPal Sandbox',

	// IPN settings
	'IPN_SETTINGS'							=> 'Paramètres IPN',
	'PAYPAL_IPN_ENABLE'						=> 'Activer IPN',
	'PAYPAL_IPN_ENABLE_EXPLAIN'				=> 'Activer cette option pour utiliser les Notification Instantannée de Paiement',
	'PAYPAL_IPN_LOGGING'					=> 'Journaux des erreurs',
	'PAYPAL_IPN_LOGGING_EXPLAIN'			=> 'Enregistrer les erreurs et les données liée à PayPal IPN dans <strong>/store/transaction.log</strong>',

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
));

/**
* mode: donation pages
* Info: language keys are prefixed with 'DONATION_DP_' for 'DONATION_DONATION_PAGES_'
*/
$lang = array_merge($lang, array(
	// Donation Page settings
	'DONATION_DP_CONFIG'			=> 'Donation pages',
	'DONATION_DP_CONFIG_EXPLAIN'	=> 'Permet d’améliorer le rendu des pages personalisables du MOD.',

	'DONATION_DP_PAGE'				=> 'Type de page',
	'DONATION_DP_LANG'				=> 'Langue',

	// Donation Page Body settings
	'DONATION_BODY_SETTINGS'	=> 'Paramètres de la page principale',
	'DONATION_BODY'				=> 'Page principale',
	'DONATION_BODY_EXPLAIN'		=> 'Saisir le texte que vous souhaitez afficher sur la page principale.',

	// Donation Success settings
	'DONATION_SUCCESS_SETTINGS'	=> 'Paramètres de la page des dons validés',
	'DONATION_SUCCESS'			=> 'Page des dons validés',
	'DONATION_SUCCESS_EXPLAIN'	=> 'Saisir le texte que vous souhaitez afficher sur la page des dons validés.',

	// Donation Cancel settings
	'DONATION_CANCEL_SETTINGS'	=> 'Paramètres de la page des dons annulés',
	'DONATION_CANCEL'			=> 'Page des dons annulés',
	'DONATION_CANCEL_EXPLAIN'	=> 'Saisir le texte que vous souhaitez afficher sur la page des dons annulés.',

	// Donation Page Template vars
	'DONATION_DP_PREDEFINED_VARS'	=> 'Variables prédéfinies',
	'DONATION_DP_VAR_EXAMPLE'		=> 'Exemple',
	'DONATION_DP_VAR_NAME'			=> 'Nom',
	'DONATION_DP_VAR_VAR'			=> 'Variable',

	'DONATION_DP_BOARD_CONTACT'		=> 'E-mail de contact',
	'DONATION_DP_BOARD_EMAIL'		=> 'E-mail du forum',
	'DONATION_DP_BOARD_SIG'			=> 'Signature du forum',
	'DONATION_DP_SITE_DESC'			=> 'Description du site',
	'DONATION_DP_SITE_NAME'			=> 'Nom du site',
	'DONATION_DP_USER_ID'			=> 'ID de l’utilisateur',
	'DONATION_DP_USERNAME'			=> 'Nom de l’utilisateur',
));

/**
* mode: currency
* Info: language keys are prefixed with 'DONATION_DC_' for 'DONATION_DONATION_CURRENCY_'
*/
$lang = array_merge($lang, array(
	// Currency Management
	'DONATION_DC_CONFIG'			=> 'Gestion des devises',
	'DONATION_DC_CONFIG_EXPLAIN'	=> 'Permet de gérer les devises pour faire un don',
	'DONATION_DC_NAME'				=> 'Nom de la devise',
	'DONATION_DC_NAME_EXPLAIN'		=> 'Exemple : Euro',
	'DONATION_DC_ISO_CODE'			=> 'Code ISO 4217',
	'DONATION_DC_ISO_CODE_EXPLAIN'	=> 'Code alpabetique de la devise.<br />En savoir plus sur la norme ISO 4217… reportez-vous à la <a href="http://www.phpbb.com/customise/db/mod/paypal_donation_mod/faq/f_746" title="FAQ du MOD PayPal Donation">FAQ du MOD PayPal Donation</a> (lien externe en anglais)',
	'DONATION_DC_SYMBOL'			=> 'Symbole de la devise',
	'DONATION_DC_SYMBOL_EXPLAIN'	=> 'Inscire le symbole de la devise.<br />Exemple : <strong>€</strong> pour Euro',
	'DONATION_DC_ENABLED'			=> 'Activer la devise',
	'DONATION_DC_ENABLED_EXPLAIN'	=> 'Si activée, la devise sera disponible dans les listes de sélection',
	'DONATION_DC_CREATE_CURRENCY'	=> 'Ajouter une nouvelle devise',
));

/**
* mode: transactions
* Info: language keys are prefixed with 'DONATION_DT_' for 'DONATION_DONATION_TRANSACTION_'
*/
$lang = array_merge($lang, array(
	// Main
	'DONATION_DT_LOG'					=> 'Journal des transactions',
	'DONATION_DT_LOG_EXPLAIN'			=> 'Ici vous pouvez voir les détails des transacations PayPal',
	'DONATION_DT_TXN_ID'				=> 'Numéro transaction',
	'DONATION_DT_TXN_ID_EXPLAIN'		=> '',
	'DONATION_DT_USERNAME'				=> 'Donateur',
	'DONATION_DT_IPN_STATUS'			=> 'Statut IPN',
	'DONATION_DT_PAYMENT_STATUS'		=> 'Etat du paiement',
/*
* TRANSLATORS PLEASE NOTE 
* The line below has a special note.
* "## For the translation:" followed by one "Don't" and one "Yes"
* "Don't" means do not change this column, and "Yes" means you can translate this column.
*/

##	For translate :							 Don't					Yes
	'DONATION_DT_PAYMENT_STATUS_VALUES'	=> array(
											'canceled_reversal'	=> 'Annulation invalidée',
											'completed'			=> 'Effectué',
											'created'			=> 'Créé',
											'denied'			=> 'Rejeté',
											'expired'			=> 'Expiré',
											'failed'			=> 'Echoué',
											'pending'			=> 'En attente',
											'refunded'			=> 'Remboursé',
											'reversed'			=> 'Annulé',
											'processed'			=> 'Accepté',
											'voided'			=> 'Annulé',
											),

	'DONATION_DT_PAYMENT_DATE'			=> 'Date du paiment',
	'DONATION_DT_SORT_TXN_ID'			=> 'Numéro transaction',
	'DONATION_DT_SORT_DONORS'			=> 'Donateur',
	'DONATION_DT_SORT_IPN_STATUS'		=> 'Statut IPN',
	'DONATION_DT_SORT_PAYMENT_STATUS'	=> 'Etat du paiement',
	'DONATION_DT_VERIFIED'				=> 'Vérifié',
	'DONATION_DT_UNVERIFIED'			=> 'Non vérifié',

	'DONATION_DT_DETAILS'				=> 'Détails de la transaction',
	'DONATION_DT_BOARD_USERNAME'		=> 'Donateur',
	'DONATION_DT_NAME'					=> 'Nom',
	'DONATION_DT_PAYER_EMAIL'			=> 'E-mail',
	'DONATION_DT_PAYER_ID'				=> 'Identifiant de l’émetteur du paiement',
	'DONATION_DT_PAYER_STATUS'			=> 'Etat de l’émetteur du paiement',

	'DONATION_DT_RECEIVER_EMAIL'		=> 'Paiement envoyé à',
	'DONATION_DT_RECEIVER_ID'			=> 'Ident. compte marchand',

	'DONATION_DT_TOTAL_AMMOUNT'			=> 'Montant total',
	'DONATION_DT_TOTAL_AMMOUNT_EXPLAIN'	=> '',
	'DONATION_DT_FEE_AMMOUNT'			=> 'Montant de la commission',
	'DONATION_DT_FEE_AMMOUNT_EXPLAIN'	=> '',
	'DONATION_DT_NET_AMMOUNT'			=> 'Montant net',
	'DONATION_DT_NET_AMMOUNT_EXPLAIN'	=> '',

	'DONATION_DT_CONVERT_FROM'			=> 'Conversion de',
	'DONATION_DT_CONVERT_FROM_EXPLAIN'	=> '',
	'DONATION_DT_SETTLE_AMOUNT'			=> 'Conversion en',
	'DONATION_DT_SETTLE_AMOUNT_EXPLAIN'	=> '',
	'DONATION_DT_EXCHANGE_RATE'			=> 'Taux de change',
	'DONATION_DT_EXCHANGE_RATE_EXPLAIN'	=> '',

	'DONATION_DT_ITEM_NAME'				=> 'Titre de l’objet',
	'DONATION_DT_ITEM_NUMBER'			=> 'Numéro d’objet',

	'DONATION_DT_MEMO'					=> 'Mémo',
	'DONATION_DT_MEMO_EXPLAIN'			=> '',
));

/**
* logs
*/
$lang = array_merge($lang, array(
	//logs
	'LOG_DONATION_UPDATED'					=> '<strong>PayPal Donation: Configuration mise à jour.</strong>',
	'LOG_DONATION_PAGES_UPDATED'			=> '<strong>PayPal Donation: Pages de dons mises à jour.</strong>',
	'LOG_ITEM_ADDED'						=> '<strong>PayPal Donation: %1$s ajouté(e)</strong><br />» %2$s',
	'LOG_ITEM_UPDATED'						=> '<strong>PayPal Donation: %1$s ajouté(e)</strong><br />» %2$s',
	'LOG_ITEM_REMOVED'						=> '<strong>PayPal Donation: %1$s supprimé(e)</strong>',
	'LOG_ITEM_MOVE_DOWN'					=> '<strong>PayPal Donation: Déplacement de la %1$s. </strong> %2$s <strong>après</strong> %3$s',
	'LOG_ITEM_MOVE_UP'						=> '<strong>PayPal Donation: Déplacement de la %1$s. </strong> %2$s <strong>avant</strong> %3$s',
	'LOG_ITEM_ENABLED'						=> '<strong>PayPal Donation: %1$s activé(e)</strong><br />» %2$s',
	'LOG_ITEM_DISABLED'						=> '<strong>PayPal Donation: %1$s désactivé(e)</strong><br />» %2$s',
	'LOG_CLEAR_DONATION_TXN'				=> '<strong>PayPal Donation: Purge du journal des transactions</strong>',
	'LOG_STAT_RESET_DATE'					=> '<strong>PayPal Donation: Data d’installation réinitialisée</strong>',
	'LOG_STAT_RESYNC_DONORSCOUNTS'			=> '<strong>PayPal Donation: Synchronisation des compteurs de donateurs</strong>',
	'LOG_STAT_RESYNC_TRANSACTIONSCOUNTS'	=> '<strong>PayPal Donation: Synchronisation des compteurs de transactions</strong>',

	// Confirm box
	'DONATION_DC_ENABLED'		=> 'Une devise a été activée',
	'DONATION_DC_DISABLED'		=> 'Une devise a été désactivée.',
	'DONATION_DC_ADDED'			=> 'Une nouvelle devise a été ajoutée.',
	'DONATION_DC_UPDATED'		=> 'Une devise a été mise à jour.',
	'DONATION_DC_REMOVED'		=> 'Une devise a été supprimée.',
	'DONATION_DP_LANG_ADDED'	=> 'Une langue de page de dons a été ajoutée',
	'DONATION_DP_LANG_UPDATED'	=> 'Une langue de page de dons a été mise à jour',
	'DONATION_DP_LANG_REMOVED'	=> 'Une langue de page de dons a été supprimée',

	// Errors
	'MUST_SELECT_ITEM'			=> 'L’objet sélectionné n’existe pas',
	'DONATION_DC_ENTER_NAME'	=> 'Entrez un nom de devise',
));
?>