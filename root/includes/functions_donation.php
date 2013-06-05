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
* Calculate donations stats percent number
*
* @param str $type = ''
* @param int $multiplicand
* @param int $dividend
*/
function donation_percent_stats ($type = '', $multiplicand, $dividend)
{
	global $template;

	$donation_percent_stats = ($multiplicand * 100) / $dividend;
	$template->assign_vars(array(
		'DONATION_' . $type	=> round($donation_percent_stats, 2),
		'S_' . $type 		=> !empty($type) ? true : false,
	));
}

/**
* Generate currency list.
*
* @param int $default = 4		ID of the default currency
* @param str $type				Corresponds to 'item_type' value. Can be 'device' or 'donation_pages'
* @param str $format = ''		Determine the output format. Can be 'acp', 'default_currency' or empty
* @param str $lang_key = 'USD'	Retreive from language key file the language key nammed 'CURRENCY_DEFAULT'. If it doesn't exist, USD will be the default value.
*/
function donation_item_list($default = 4, $type, $format = '', $lang_key = 'USD')
{
	global $db;

	// Build $default_currency_check to determine the default currency
	$sql = 'SELECT item_id FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id = ' . (int) $default;
	$result = $db->sql_query($sql);
	$default_currency_check = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$item_select = '';
	if ($format == 'default_currency' && $default_currency_check)
	{
		$item_select .= ' AND item_id = ' . (int) $default;
	}

	$sql = 'SELECT item_id, item_name, item_iso_code, item_symbol
		FROM ' . DONATION_ITEM_TABLE . "
		WHERE item_type = '" . $db->sql_escape($type) . "'
			AND item_enable = 1
			$item_select
		ORDER BY left_id";
	$result = $db->sql_query($sql);

	$item_list_options = '';

	// Build output
	while ($row = $db->sql_fetchrow($result))
	{
		$selected = '';

		$row['item_id'] = (int) $row['item_id'];
		if ($row['item_id'] == (int) $default)
		{
			$selected = ' selected="selected"';
		}

		if ($format == 'acp')
		{
			// Build ACP list
			$item_list_options .= '<option value="' . $row['item_id'] . '"' . $selected . '>' . $row['item_name'] . '</option>';
		}
		elseif ($format == 'default_currency')
		{
			// Build stats default currency
			$item_list_options .= $row['item_iso_code'];
		}
		else
		{
			//Build main donation list
			$item_list_options .= '<option value="' . $row['item_iso_code'] . '"' . $selected . '>' . $row['item_symbol'] . ' ' . $row['item_iso_code'] . '</option>';
		}
	};

	$db->sql_freeresult($result);

	// Assign default value if SQL result is empty and if is a currency type
	if (empty($item_list_options) && $format == 'default_currency' && $type =='currency')
	{
		$item_list_options = $lang_key;
	}
	elseif (empty($item_list_options) && $type =='currency')
	{
		$item_list_options = '<option value="' . $lang_key . '">' . $lang_key . '</option>';
	}
	return $item_list_options;
}
?>