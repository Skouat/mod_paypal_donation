<?php
/**
*
* @package phpBB3
* @version $Id: $
* @copyright (c) 2007 phpBB Group
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
* Calculate donation percent goal number.
*
*/
function donation_goal_number ()
{
	global $config, $template;

	if ($config['donation_goal'] > 0)
		{
		$donation_goal_number = ($config['donation_raised'] * 100) / $config['donation_goal'];
		$template->assign_vars(array(
			'DONATION_GOAL_NUMBER' => round($donation_goal_number, 2),
		));
	}
}

/**
* Calculate donation percent used number.
*
*/
function donation_used_number ()
{
	global $config, $template;

	if ($config['donation_raised'] > 0 && $config['donation_used'] > 0)
		{
		$donation_used_number = ($config['donation_used'] * 100) / $config['donation_raised'];
		$template->assign_vars(array(
			'DONATION_USED_NUMBER' => round($donation_used_number, 2),
		));
	}
}
/**
* Generate currency list.
*
* @param int $default = 1
* @param str $type
* @param str $format = ''
* @param str $Lang_key = 'USD'
*/
function donation_item_list($default = 1, $type, $format = '', $Lang_key = 'USD')
{
	global $db;

	// Build SQL_AND for determine the default currency
	$sql = 'SELECT item_id
	FROM ' . DONATION_ITEM_TABLE . '
	WHERE item_id = ' . $default;
	$default_currency_check = get_info($sql);

	$sql_and = '';

	if ($format == 'default_currency' && $default_currency_check)
	{
		$sql_and = 'AND di.item_id = ' . $default;
	}

	// SQL Build array
	$sql_ary = array(
		'SELECT'	=> 'di.item_id, di.item_name, di.item_iso_code, di.item_symbol',
		'FROM'		=> array(DONATION_ITEM_TABLE => 'di'),
		'WHERE'		=> 'di.item_type = "' . $type . '"
			AND di.item_enable = 1 '
			. $sql_and,
		'ORDER_BY'	=> 'di.left_id',
	);
	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result = $db->sql_query($sql);

	$item_list_options = '';

	while ($row = $db->sql_fetchrow($result))
	{
		$selected = ($row['item_id'] == $default) ? ' selected="selected"' : '' ;

		if ($format == 'acp')
		{
			// Build ACP list
			$item_list_options .= '<option value="' . $row['item_id'] . '"' . $selected . '>' . $row['item_name'] . '</option>';
		}
		elseif ($format == 'default_currency')
		{
			// Build tats default currency
			$item_list_options .= $row['item_iso_code'];
		}
		else
		{
			//Build main list
			$item_list_options .= '<option value="' . $row['item_iso_code'] . '"' . $selected . '>' . $row['item_symbol'] . ' ' . $row['item_iso_code'] . '</option>';
		}
	};

	// Assign default value if SQL result is empty 
	if (empty($item_list_options) && $format == 'default_currency')
	{
		$item_list_options = $Lang_key;
	}
	elseif (empty($item_list_options))
	{
		$item_list_options = '<option value="' . $Lang_key . '" selected="selected">' . $Lang_key . '</option>';
	}
	return $item_list_options;
	$db->sql_freeresult($result);
}

/**
* Get table details
*
* @param  str $sql
*/
function get_info($sql)
{
	global $db;

	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if (!$row)
	{
		$row = false;
	}

	return $row;
}
?>