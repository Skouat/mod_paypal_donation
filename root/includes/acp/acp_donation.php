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
* @package acp
*/
class acp_donation
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $template;
		global $phpbb_root_path, $phpEx;

		require($phpbb_root_path . 'includes/functions_donation.' . $phpEx);

		$user->add_lang(array('acp/board'));
		$action	= request_var('action', '');

		$this->tpl_name = 'acp_donation';
		$this->page_title = $user->lang['ACP_DONATION_MOD'];
		$form_key = 'acp_donation';
		add_form_key($form_key);

		$submit = (isset($_POST['submit'])) ? true : false;

		switch ($mode)
		{
			case 'configuration':
				$display_vars = array(
					'title'	=> 'DONATION_CONFIG',
					'vars'	=> array(
						'legend1'						=> 'GENERAL_SETTINGS',
						'donation_enable'				=> array('lang' => 'DONATION_ENABLE',				'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => true,),
						'donation_account_id'			=> array('lang' => 'DONATION_ACCOUNT_ID',			'validate' => 'string',		'type' => 'text:40:255', 'explain' => true,),
						'donation_default_currency'		=> array('lang' => 'DONATION_DEFAULT_CURRENCY',		'validate' => 'int',		'type' => 'select', 'function' => 'donation_item_list', 'params' => array('{CONFIG_VALUE}', 'currency', 'acp'), 'explain' => true,),

						'legend2'						=> 'SANDBOX_SETTINGS',
						'paypal_sandbox_enable'			=> array('lang' => 'SANDBOX_ENABLE',				'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => false),
						'paypal_sandbox_address'		=> array('lang' => 'SANDBOX_ADDRESS',				'validate' => 'string',		'type' => 'text:40:255', 'explain' => true),

						'legend3'						=> 'DONATION_STATS_SETTINGS',
						'donation_stats_index_enable'	=> array('lang' => 'DONATION_STATS_INDEX_ENABLE',	'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => true,),
						'donation_raised_enable'		=> array('lang' => 'DONATION_RAISED_ENABLE',		'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => false,),
						'donation_raised'				=> array('lang' => 'DONATION_RAISED',				'validate' => 'int:0',		'type' => 'text:10:50', 'explain' => true,),
						'donation_goal_enable'			=> array('lang' => 'DONATION_GOAL_ENABLE',			'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => false,),
						'donation_goal'					=> array('lang' => 'DONATION_GOAL',					'validate' => 'int:0',		'type' => 'text:10:50', 'explain' => true,),
						'donation_used_enable'			=> array('lang' => 'DONATION_USED_ENABLE',			'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => false,),
						'donation_used'					=> array('lang' => 'DONATION_USED',					'validate' => 'int:0',		'type' => 'text:10:50', 'explain' => true,),
						'donation_currency_enable'		=> array('lang' => 'DONATION_CURRENCY_ENABLE',		'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => true,),

						'legend6'				=> 'ACP_SUBMIT_CHANGES',
					)
				);
				if (isset($display_vars['lang']))
				{
					$user->add_lang($display_vars['lang']);
				}

				$this->new_config = $config;
				$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
				$error = array();

				// We validate the complete config if whished
				validate_config_vars($display_vars['vars'], $cfg_array, $error);

				if ($submit && !check_form_key($form_key))
				{
					$error[] = $user->lang['FORM_INVALID'];
				}
				// Do not write values if there is an error
				if (sizeof($error))
				{
					$submit = false;
				}

				// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
				foreach ($display_vars['vars'] as $config_name => $null)
				{
					if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
					{
						continue;
					}

					$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

					if ($submit)
					{
						set_config($config_name, $config_value);
					}
				}

				if ($submit)
				{
					add_log('admin', 'LOG_DONATION_UPDATED');

					trigger_error($user->lang['DONATION_SAVED'] . adm_back_link($this->u_action));
				}

				$this->tpl_name = 'acp_board';
				$this->page_title = $display_vars['title'];

				$template->assign_vars(array(
					'L_TITLE'			=> $user->lang[$display_vars['title']],
					'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],

					'S_ERROR'			=> (sizeof($error)) ? true : false,
					'ERROR_MSG'			=> implode('<br />', $error),

					'U_ACTION'			=> $this->u_action)
				);

				// Output relevant page
				foreach ($display_vars['vars'] as $config_key => $vars)
				{
					if (!is_array($vars) && strpos($config_key, 'legend') === false)
					{
						continue;
					}

					if (strpos($config_key, 'legend') !== false)
					{
						$template->assign_block_vars('options', array(
							'S_LEGEND'		=> true,
							'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
						);

						continue;
					}

					$type = explode(':', $vars['type']);

					$l_explain = '';
					if ($vars['explain'] && isset($vars['lang_explain']))
					{
						$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
					}
					else if ($vars['explain'])
					{
						$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
					}

					$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

					if (empty($content))
					{
						continue;
					}

					$template->assign_block_vars('options', array(
						'KEY'			=> $config_key,
						'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
						'S_EXPLAIN'		=> $vars['explain'],
						'TITLE_EXPLAIN'	=> $l_explain,
						'CONTENT'		=> $content,
						)
					);

					unset($display_vars['vars'][$config_key]);
				}
			break;

			case 'donation_pages':

				if ($submit)
				{
					if (check_form_key($form_key))
					{
						$donation_pages_vars = request_var('pages', array('' => ''));

						$sql = 'SELECT *
							FROM ' . DONATION_ITEM_TABLE . '
							WHERE item_type= "' . $mode . '"';
						$result = $db->sql_query($sql);

						while ($row = $db->sql_fetchrow($result))
						{
							$item_name = strtolower($row['item_name']);

							if (array_key_exists($item_name, $donation_pages_vars))
							{
								$sql = 'UPDATE ' . DONATION_ITEM_TABLE . "
									SET item_text = '" . $db->sql_escape($donation_pages_vars[$item_name]) . "'
									WHERE item_name = '" . $item_name . "'";
								$db->sql_query($sql);
							}
							unset($item_name);
						}
						$db->sql_freeresult($result);
					}
					else
					{
						trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action));
					}

					add_log('admin', 'LOG_' . strtoupper($mode));

					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}

				$this->page_title = 'DONATION_' . strtoupper($mode) . '_CONFIG';

				// build sql query with alias field
				$sql = 'SELECT item_name AS donation_title, item_text AS donation_content
					FROM ' . DONATION_ITEM_TABLE . '
					WHERE item_type= "' . $mode . '"';
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('donation_pages', array(
						'L_ITEM_NAME'						=> $user->lang[strtoupper($row['donation_title']) . '_SETTINGS'],
						'L_DONATION_PAGES_TITLE'			=> $user->lang[strtoupper($row['donation_title'])],
						'L_DONATION_PAGES_TITLE_EXPLAIN'	=> $user->lang[strtoupper($row['donation_title']) . '_EXPLAIN'],
						'S_DONATION_TYPE'					=> $row['donation_title'],
						'DONATION_BODY'						=> $row['donation_content'],
					));
				}
				$db->sql_freeresult($result);

				$template->assign_vars(array(
					'U_ACTION'			=> append_sid($this->u_action),

					'L_TITLE'			=> $user->lang[$this->page_title],
					'L_TITLE_EXPLAIN'	=> $user->lang[$this->page_title . '_EXPLAIN'],
					'S_MODE'			=> ($mode == 'donation_pages') ? $mode : false,
					)
				);
			break;

			case 'currency':
				$this->page_title = 'DONATION_' . strtoupper($mode) . '_CONFIG';

				$action = isset($_POST['add']) ? 'add' : (isset($_POST['save']) ? 'save' : $action);
				$item_id = request_var('id', 0);

				$s_hidden_fields = '';

				$template->assign_vars(array(
					'U_ACTION'			=> append_sid($this->u_action),

					'L_TITLE'			=> $user->lang[$this->page_title],
					'L_TITLE_EXPLAIN'	=> $user->lang[$this->page_title . '_EXPLAIN'],
					'L_NAME'			=> $user->lang['DONATION_' . strtoupper($mode) . '_NAME'],
					'L_CREATE_ITEM'		=> $user->lang['DONATION_CREATE_' . strtoupper($mode)],
					)
				);

				switch ($action)
				{
					case 'edit':
						if (!$item_id)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}
						$sql = 'SELECT * FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id =' . (int) $item_id . ' AND item_type ="' . $mode . '"';
						$currency_ary = get_info($sql);

						if (!$currency_ary)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$s_hidden_fields .= '<input type="hidden" name="id" value="' . $item_id . '" />';

					case 'add':
						$template->assign_vars(array(
							'S_EDIT'			=> true,
							'S_MODE'			=> ($mode == 'currency') ? $mode : false,
							'U_ACTION'			=> $this->u_action,
							'U_BACK'			=> $this->u_action,

							'L_ITEM_NAME'					=> $user->lang[ 'DONATION_' . strtoupper($mode) . '_NAME'],
							'L_ITEM_NAME_EXPLAIN'			=> $user->lang[ 'DONATION_' . strtoupper($mode) . '_NAME_EXPLAIN'],
							'L_ITEM_ISO_CODE'				=> $user->lang[ 'DONATION_' . strtoupper($mode) . '_ISO_CODE'],
							'L_ITEM_ISO_CODE_EXPLAIN'		=> $user->lang[ 'DONATION_' . strtoupper($mode) . '_ISO_CODE_EXPLAIN'],
							'L_ITEM_SYMBOL'					=> $user->lang[ 'DONATION_' . strtoupper($mode) . '_SYMBOL'],
							'L_ITEM_SYMBOL_EXPLAIN'			=> $user->lang[ 'DONATION_' . strtoupper($mode) . '_SYMBOL_EXPLAIN'],
							'L_ACP_ITEM_ENABLED'			=> $user->lang[ 'DONATION_' . strtoupper($mode) . '_ENABLED'],
							'L_ACP_ITEM_ENABLED_EXPLAIN'	=> $user->lang[ 'DONATION_' . strtoupper($mode) . '_ENABLED_EXPLAIN'],

							'ITEM_NAME'			=> isset($currency_ary['item_name']) ? $currency_ary['item_name'] : utf8_normalize_nfc(request_var('item_name', '', true)),
							'ITEM_ISO_CODE'		=> isset($currency_ary['item_iso_code']) ? $currency_ary['item_iso_code'] : utf8_normalize_nfc(request_var('item_iso_code', '', true)),
							'ITEM_SYMBOL'		=> isset($currency_ary['item_symbol']) ? $currency_ary['item_symbol'] : utf8_normalize_nfc(request_var('item_symbol', '', true)),
							'ITEM_ENABLED'		=> isset($currency_ary['item_enable']) ? $currency_ary['item_enable'] : 1,

							'S_HIDDEN_FIELDS'		=> $s_hidden_fields,
						));
						return;
					break;

					case 'save':

						$item_name = utf8_normalize_nfc(request_var('item_name', '', true));
						$item_iso_code = utf8_normalize_nfc(request_var('item_iso_code', '', true));
						$item_symbol = utf8_normalize_nfc(request_var('item_symbol','',true));
						$item_enable = request_var('item_enable', 0);

						if ( !$item_name && !$item_id)
						{
							trigger_error($user->lang['ENTER_' . strtoupper($mode) . '_NAME'] . adm_back_link($this->u_action . '&amp;action=add'), E_USER_WARNING);
						}
						elseif ( !$item_name)
						{
							trigger_error($user->lang['ENTER_' . strtoupper($mode) . '_NAME'] . adm_back_link($this->u_action . '&amp;action=edit&amp;id=' . $item_id ), E_USER_WARNING);
						}

						$sql_ary = array(
							'item_name'			=> $item_name,
							'item_iso_code'	=> $item_iso_code,
							'item_symbol'	=> $item_symbol,
							'item_enable'		=> $item_enable,
							'item_type'			=> $mode,
							'item_text'			=> '',
						);

						if ( $item_id )
						{
							$db->sql_query('UPDATE ' . DONATION_ITEM_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . ' WHERE item_id = ' . (int) $item_id);
						}
						else
						{
							$sql = 'SELECT right_id FROM ' . DONATION_ITEM_TABLE . ' ORDER BY right_id DESC LIMIT 1';
							$result = $db->sql_query($sql);
							$right_id = (string) $db->sql_fetchfield('right_id');
							$db->sql_freeresult($result);
							$sql_ary['left_id'] = $right_id + 1;
							$sql_ary['right_id'] = $right_id + 2;

							$db->sql_query('INSERT INTO ' . DONATION_ITEM_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));
						}


						$log_action = ($item_id ? 'LOG_ITEM_UPDATED' : 'LOG_ITEM_ADDED');
						add_log('admin', $log_action, $user->lang['MODE_' . strtoupper($mode)], $item_name);

						$message = $item_id ? $user->lang[strtoupper($mode) . '_UPDATED'] : $user->lang[strtoupper($mode) . '_ADDED'];
						trigger_error($message . adm_back_link($this->u_action));

					break;

					case 'delete':
						if (!$item_id)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						if (confirm_box(true))
						{

							$sql = 'DELETE FROM ' . DONATION_ITEM_TABLE . "
								WHERE item_id = $item_id";
							$db->sql_query($sql);

							add_log('admin', 'LOG_ITEM_REMOVED', $user->lang['MODE_' . strtoupper($mode)]);

							trigger_error($user->lang[strtoupper($mode) . '_REMOVED'] . adm_back_link($this->u_action));
						}
						else
						{
							confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
								'i'			=> $id,
								'mode'		=> $mode,
								'item_id'	=> $item_id,
								'action'	=> 'delete',
							)));
						}
					break;

					case 'move_up':
					case 'move_down':
						if (!$item_id)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$sql = 'SELECT * FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id =' . (int) $item_id . " AND item_type ='$mode'";
						$row = get_info($sql);

						$move_item_name = $this->move_items_by($row, $action, 1);

						if ( $move_item_name !== false )
						{
							add_log('admin', 'LOG_ITEM_' . strtoupper($action), $user->lang['MODE_' . strtoupper($mode)], $row['item_name'], $move_item_name);
						}

					break;

					case 'enable':
					case 'disable':

						if (!$item_id)
						{
							trigger_error($user->lang['NO_' . strtoupper($mode)] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						if (($action == 'disable') || $action == 'enable')
						{
							$sql = 'UPDATE ' . DONATION_ITEM_TABLE . '
								SET item_enable = ' . (($action == 'enable') ? 1 : 0) . '
								WHERE item_id = ' . $item_id;
							$db->sql_query($sql);
						}

						$sql = 'SELECT * FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id =' . (int) $item_id . " AND item_type ='$mode'";
						$row = get_info($sql);

						add_log('admin', (($action == 'enable') ? 'LOG_ITEM_ENABLED' : 'LOG_ITEM_DISABLED'), $user->lang['MODE_' . strtoupper($mode)], $row['item_name']);
						trigger_error($user->lang[($action == 'enable') ? strtoupper($mode) . '_ENABLED' : strtoupper($mode) . '_DISABLED'] . adm_back_link($this->u_action));
					break;
				}

				$sql = 'SELECT *
					FROM ' . DONATION_ITEM_TABLE . "
					WHERE item_type= '$mode'
					ORDER BY left_id";
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('items', array(
					'ITEM_NAME'			=> $row['item_name'],
					'ITEM_ENABLED'		=> ($row['item_enable']) ? true : false,

					// links
					'U_EDIT'			=> $this->u_action . '&amp;action=edit&amp;id=' . $row['item_id'],
					'U_MOVE_UP'			=> $this->u_action . '&amp;action=move_up&amp;id=' . $row['item_id'],
					'U_MOVE_DOWN'		=> $this->u_action . '&amp;action=move_down&amp;id=' . $row['item_id'],
					'U_DELETE'			=> $this->u_action . '&amp;action=delete&amp;id=' . $row['item_id'],
					'U_ENABLE'			=> $this->u_action . '&amp;action=enable&amp;id=' . $row['item_id'],
					'U_DISABLE'			=> $this->u_action . '&amp;action=disable&amp;id=' . $row['item_id'],
					));
				};
				$db->sql_freeresult($result);
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
	}
	/**
	* Move item position by $steps up/down
	*/
	function move_items_by($item_row, $action = 'move_up', $steps = 1)
	{
		global $db;

		/**
		* Fetch all the siblings between the item's current spot
		* and where we want to move it to. If there are less than $steps
		* siblings between the current spot and the target then the
		* item will move as far as possible
		*/
		$sql = 'SELECT item_id, item_name, left_id, right_id
			FROM ' . DONATION_ITEM_TABLE . "
			WHERE " . (($action == 'move_up') ? "right_id < {$item_row['right_id']} ORDER BY right_id DESC" : "left_id > {$item_row['left_id']} ORDER BY left_id ASC");
		$result = $db->sql_query_limit($sql, $steps);

		$target = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$target = $row;
		}
		$db->sql_freeresult($result);

		if ( !sizeof($target) )
		{
			// The item is already on top or bottom
			return false;
		}

		/**
		* $left_id and $right_id define the scope of the nodes that are affected by the move.
		* $diff_up and $diff_down are the values to substract or add to each node's left_id
		* and right_id in order to move them up or down.
		* $move_up_left and $move_up_right define the scope of the nodes that are moving
		* up. Other nodes in the scope of ($left_id, $right_id) are considered to move down.
		*/
		if ($action == 'move_up')
		{
			$left_id = $target['left_id'];
			$right_id = $item_row['right_id'];

			$diff_up = $item_row['left_id'] - $target['left_id'];
			$diff_down = $item_row['right_id'] + 1 - $item_row['left_id'];

			$move_up_left = $item_row['left_id'];
			$move_up_right = $item_row['right_id'];
		}
		else
		{
			$left_id = $item_row['left_id'];
			$right_id = $target['right_id'];

			$diff_up = $item_row['right_id'] + 1 - $item_row['left_id'];
			$diff_down = $target['right_id'] - $item_row['right_id'];

			$move_up_left = $item_row['right_id'] + 1;
			$move_up_right = $target['right_id'];
		}

		// Now do the dirty job
		$sql = 'UPDATE ' . DONATION_ITEM_TABLE . "
			SET left_id = left_id + CASE
				WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END,
			right_id = right_id + CASE
				WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END
			WHERE left_id BETWEEN {$left_id} AND {$right_id}
				AND right_id BETWEEN {$left_id} AND {$right_id}";
		$db->sql_query($sql);

		return $target['item_name'];
	}
}

?>