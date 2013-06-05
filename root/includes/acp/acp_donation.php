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
* @package acp
*/
class acp_donation
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $template;
		global $phpbb_root_path, $phpEx;

		include($phpbb_root_path . 'includes/functions_donation.' . $phpEx);

		$user->add_lang(array('posting','acp/board'));
		$action	= request_var('action', '');

		$this->tpl_name = 'acp_donation';
		$this->page_title = $user->lang['ACP_DONATION_MOD'];
		$form_key = 'acp_donation';
		add_form_key($form_key);

		$submit = (isset($_POST['submit'])) ? true : false;

		switch ($mode)
		{
			case 'configuration':
				$user->add_lang('mods/donate');
				$display_vars = array(
					'title'	=> 'DONATION_CONFIG',
					'vars'	=> array(
						'legend1'						=> 'GENERAL_SETTINGS',
						'donation_enable'				=> array('lang' => 'DONATION_ENABLE',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true,),
						'donation_account_id'			=> array('lang' => 'DONATION_ACCOUNT_ID',			'validate' => 'string',	'type' => 'text:40:255', 'explain' => true,),
						'donation_default_currency'		=> array('lang' => 'DONATION_DEFAULT_CURRENCY',		'validate' => 'int',	'type' => 'select', 'function' => 'donation_item_list', 'params' => array('{CONFIG_VALUE}', 'currency', 'acp',  $user->lang['CURRENCY_DEFAULT']), 'explain' => true,),
						'donation_default_value'		=> array('lang' => 'DONATION_DEFAULT_VALUE',		'validate' => 'int:0',	'type' => 'text:10:50', 'explain' => true,),
						'donation_dropbox_enable'		=> array('lang' => 'DONATION_DROPBOX_ENABLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true,),
						'donation_dropbox_value'		=> array('lang' => 'DONATION_DROPBOX_VALUE',		'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),

						'legend2'						=> 'SANDBOX_SETTINGS',
						'paypal_sandbox_enable'			=> array('lang' => 'SANDBOX_ENABLE',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_sandbox_founder_enable'	=> array('lang' => 'SANDBOX_FOUNDER_ENABLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_sandbox_address'		=> array('lang' => 'SANDBOX_ADDRESS',				'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),

						'legend3'						=> 'DONATION_STATS_SETTINGS',
						'donation_stats_index_enable'	=> array('lang' => 'DONATION_STATS_INDEX_ENABLE',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true,),
						'donation_raised_enable'		=> array('lang' => 'DONATION_RAISED_ENABLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false,),
						'donation_raised'				=> array('lang' => 'DONATION_RAISED',				'validate' => 'int:0',	'type' => 'text:10:50', 'explain' => true,),
						'donation_goal_enable'			=> array('lang' => 'DONATION_GOAL_ENABLE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false,),
						'donation_goal'					=> array('lang' => 'DONATION_GOAL',					'validate' => 'int:0',	'type' => 'text:10:50', 'explain' => true,),
						'donation_used_enable'			=> array('lang' => 'DONATION_USED_ENABLE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false,),
						'donation_used'					=> array('lang' => 'DONATION_USED',					'validate' => 'int:0',	'type' => 'text:10:50', 'explain' => true,),

						'legend4'						=> 'ACP_SUBMIT_CHANGES',
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
						// Cleaning 'donation_dropbox_value' to conserve only numeric value
						if ($config_name == 'donation_dropbox_value' && !empty($config_value))
						{
							$donation_arr_value = explode(',', $config_value);
							if (!empty($donation_arr_value))
							{
								$donation_merge_value = array();

								foreach ($donation_arr_value as $value)
								{
									$int_value = (int) $value;
									if (!empty($int_value) && ($int_value == $value))
									{
										$donation_merge_value[] = $int_value;
									}
								}
								unset($value);

								$config_value = (!empty($donation_merge_value)) ? implode(',', $donation_merge_value) : '';
							}
						}

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

					'U_ACTION'			=> $this->u_action,
				));

				if ( sizeof($error) )
				{
					$template->assign_vars(array(
						'S_ERROR' => true,
						'ERROR_MSG' => implode('<br />', $error),
					));
				}

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
				$user->add_lang('mods/donate_custom');

				if (!function_exists('generate_smilies'))
				{
					include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
				}

				if (!function_exists('display_custom_bbcodes'))
				{
					include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);
				}

				$preview = (isset($_POST['preview'])) ? true : false;
				$donation_draft = 'donation_draft';

				$donation_pages_vars = utf8_normalize_nfc(request_var('pages', array('' => ''), true));

				if ($submit || $preview)
				{
					if (!check_form_key($form_key))
					{
						trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action));
					}
				}

				if ($submit)
				{
					$uid_text = $bitfield_text = $options_text = ''; // will be modified by generate_text_for_storage
					$uid_draft = $bitfield_draft = $options_draft = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;

					$sql = 'SELECT item_name FROM ' . DONATION_ITEM_TABLE . " WHERE item_type= 'donation_pages'";
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$item_name = strtolower($row['item_name']);

						if (array_key_exists($item_name, $donation_pages_vars))
						{
							generate_text_for_storage($donation_pages_vars[$item_name], $uid_text, $bitfield_text, $options_text, $allow_bbcode, $allow_urls, $allow_smilies);

							$sql_ary = array(
								'item_text'						=> $donation_pages_vars[$item_name],
								'item_text_bbcode_uid'			=> $uid_text,
								'item_text_bbcode_bitfield'		=> $bitfield_text,
								'item_text_bbcode_options'		=> (int) $options_text,
								);

							$sql = 'UPDATE ' . DONATION_ITEM_TABLE . '
									SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
									WHERE item_name = '" . $db->sql_escape($item_name) . "'";
							$db->sql_query($sql);
						}
						unset($item_name);
					}
					$db->sql_freeresult($result);

					add_log('admin', 'LOG_DONATION_PAGES_UPDATED');

					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}

				$draft_preview = '';

				if ($preview)
				{
					if (array_key_exists($donation_draft, $donation_pages_vars))
					{
						$draft_preview = isset($user->lang[strtoupper($donation_pages_vars[$donation_draft])]) ? $user->lang[strtoupper($donation_pages_vars[$donation_draft])] : $donation_pages_vars[$donation_draft];
						$draft_preview = $this->preview_announcement($draft_preview);
					}
				}

				$this->page_title = 'DONATION_DP_CONFIG';

				// Build sql query with alias field
				$sql = 'SELECT item_name AS donation_title, item_text AS donation_content, item_text_bbcode_uid, item_text_bbcode_bitfield, item_text_bbcode_options
					FROM ' . DONATION_ITEM_TABLE . "
					WHERE item_type= 'donation_pages'";
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{

					if ($row['donation_title'] == 'donation_draft')
					{
						$donation_draft_preview = isset($user->lang[strtoupper($row['donation_content'])]) ? $user->lang[strtoupper($row['donation_content'])] : $row['donation_content'];
						$donation_draft_preview = generate_text_for_display($donation_draft_preview, $row['item_text_bbcode_uid'], $row['item_text_bbcode_bitfield'], $row['item_text_bbcode_options']);

						decode_message($row['donation_content'], $row['item_text_bbcode_uid']);

						$template->assign_vars(array(
							'DONATION_DRAFT_PREVIEW'	=> $draft_preview ? $draft_preview : $donation_draft_preview,
							'DONATION_DRAFT'			=> $draft_preview ? $donation_pages_vars[$donation_draft] : $row['donation_content'],
						));
					}
					else
					{
						decode_message($row['donation_content'], $row['item_text_bbcode_uid']);

						$donation_title = strtoupper($row['donation_title']);

						$template->assign_block_vars('donation_pages', array(
							'L_ITEM_NAME'						=> $user->lang[$donation_title . '_SETTINGS'],
							'L_DONATION_PAGES_TITLE'			=> $user->lang[$donation_title],
							'L_DONATION_PAGES_TITLE_EXPLAIN'	=> $user->lang[$donation_title . '_EXPLAIN'],
							'L_COPY_TO'							=> $user->lang['COPY_TO_' . $donation_title],
							'S_DONATION_TYPE'					=> $row['donation_title'],
							'DONATION_BODY'						=> $row['donation_content'],
						));
					}
				}
				$db->sql_freeresult($result);

				// Generate smilies on inline displaying
				generate_smilies('inline', '');

				// Assigning custom bbcodes
				display_custom_bbcodes();

				$template->assign_vars(array(
					'L_TITLE'			=> $user->lang[$this->page_title],
					'L_TITLE_EXPLAIN'	=> $user->lang[$this->page_title . '_EXPLAIN'],

					'S_DONATION_PAGES'	=> $mode,

					'U_ACTION'			=> $this->u_action,
				));
			break;

			case 'currency':
				if ($submit && !check_form_key($form_key))
				{
					trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action));
				}

				$this->page_title = 'DONATION_DC_CONFIG';

				$action = isset($_POST['add']) ? 'add' : (isset($_POST['save']) ? 'save' : $action);
				$item_id = request_var('id', 0);

				$template->assign_vars(array(
					'L_TITLE'			=> $user->lang[$this->page_title],
					'L_TITLE_EXPLAIN'	=> $user->lang[$this->page_title . '_EXPLAIN'],
					'L_NAME'			=> $user->lang['DONATION_DC_NAME'],
					'L_CREATE_ITEM'		=> $user->lang['DONATION_DC_CREATE_CURRENCY'],

					'S_CURRENCY'		=> $mode,

					'U_ACTION'			=> $this->u_action,
				));

				//skip this code if $action is used
				if (!$action)
				{
					$sql = 'SELECT *
						FROM ' . DONATION_ITEM_TABLE . "
						WHERE item_type= 'currency'
						ORDER BY left_id";
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$row['item_id'] = (int) $row['item_id'];

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
				}

				switch ($action)
				{
					case 'edit':
						if (!$item_id)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$sql = 'SELECT * FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id =' . (int) $item_id . " AND item_type = 'currency'";
						$result = $db->sql_query($sql);
						$currency_ary = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if (!$currency_ary)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$s_hidden_fields = build_hidden_fields(array(
							'id'			=> $item_id,
							'action'		=> 'save',
							));

					case 'add':
						$template->assign_vars(array(
							'S_EDIT'			=> true,
							'S_MODE'			=> $mode,

							'U_ACTION'			=> $this->u_action,
							'U_BACK'			=> $this->u_action,

							'ITEM_NAME'			=> isset($currency_ary['item_name']) ? $currency_ary['item_name'] : utf8_normalize_nfc(request_var('item_name', '', true)),
							'ITEM_ISO_CODE'		=> isset($currency_ary['item_iso_code']) ? $currency_ary['item_iso_code'] : utf8_normalize_nfc(request_var('item_iso_code', '', true)),
							'ITEM_SYMBOL'		=> isset($currency_ary['item_symbol']) ? $currency_ary['item_symbol'] : utf8_normalize_nfc(request_var('item_symbol', '', true)),
							'ITEM_ENABLED'		=> isset($currency_ary['item_enable']) ? $currency_ary['item_enable'] : true,

							'S_HIDDEN_FIELDS'	=> isset($s_hidden_fields) ? $s_hidden_fields : '',
						));
						return;
					break;

					case 'save':

						$item_name = utf8_normalize_nfc(request_var('item_name', '', true));
						$item_iso_code = utf8_normalize_nfc(request_var('item_iso_code', '', true));
						$item_symbol = utf8_normalize_nfc(request_var('item_symbol','',true));
						$item_enable = request_var('item_enable', 0);

						if ( empty($item_name) )
						{
							$trigger_url = !$item_id ? '&amp;action=add' : '&amp;action=edit&amp;id=' . (int) $item_id;

							trigger_error($user->lang['ENTER_CURRENCY_NAME'] . adm_back_link($this->u_action . $trigger_url), E_USER_WARNING);
						}

						$sql_ary = array(
							'item_name'			=> $item_name,
							'item_iso_code'		=> $item_iso_code,
							'item_symbol'		=> $item_symbol,
							'item_enable'		=> $item_enable,
							'item_type'			=> $mode,
							'item_text'			=> '',
						);

						if ($item_id)
						{
							$db->sql_query('UPDATE ' . DONATION_ITEM_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . ' WHERE item_id = ' . (int) $item_id);
						}
						else
						{
							$sql = 'SELECT MAX(right_id) AS right_id FROM ' . DONATION_ITEM_TABLE; 
							$result = $db->sql_query($sql);
							$right_id = (string) $db->sql_fetchfield('right_id');
							$db->sql_freeresult($result);
							$sql_ary['left_id'] = $right_id + 1;
							$sql_ary['right_id'] = $right_id + 2;

							$db->sql_query('INSERT INTO ' . DONATION_ITEM_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));
						}

						$item_action = $item_id ? 'UPDATED' : 'ADDED';
						add_log('admin', 'LOG_ITEM_' . $item_action, $user->lang['MODE_CURRENCY'], $item_name);
						trigger_error($user->lang['CURRENCY_' . $item_action] . adm_back_link($this->u_action));

					break;

					case 'delete':
						if (!$item_id)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						if (confirm_box(true))
						{

							$sql = 'DELETE FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id = ' . (int) $item_id;
							$db->sql_query($sql);

							add_log('admin', 'LOG_ITEM_REMOVED', $user->lang['MODE_CURRENCY']);

							trigger_error($user->lang['CURRENCY_REMOVED'] . adm_back_link($this->u_action));
						}
						else
						{
							confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
								'mode'		=> $mode,
								'item_id'	=> (int) $item_id,
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

						$sql = 'SELECT * FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id = ' . (int) $item_id . " AND item_type = 'currency'";
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						$move_item_name = $this->move_items_by($row, $action, 1);

						if ($move_item_name !== false )
						{
							add_log('admin', 'LOG_ITEM_' . strtoupper($action), $user->lang['MODE_CURRENCY'], $row['item_name'], $move_item_name);
						}

					break;

					case 'enable':
					case 'disable':

						if (!$item_id)
						{
							trigger_error($user->lang['NO_CURRENCY'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						if ($action == 'enable')
						{
							// SQL Build array
							$sql = 'SELECT item_id
									FROM ' . DONATION_ITEM_TABLE . "
									WHERE item_type = 'currency'
										AND item_enable = 1";
							$result = $db->sql_query($sql);
							$default_currency_check = $db->sql_fetchrow($result);
							$db->sql_freeresult($result);


							if (!$default_currency_check)
							{
								set_config('donation_default_currency', (int) $item_id);
							}
						}

						if ($action)
						{
							$item_enable = ($action == 'enable') ? true : false;
							$sql = 'UPDATE ' . DONATION_ITEM_TABLE . ' SET item_enable = ' . (int) $item_enable . ' WHERE item_id = ' . (int) $item_id;
							$db->sql_query($sql);
						}

						$sql = 'SELECT * FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id = ' . (int) $item_id . " AND item_type = 'currency'";
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						$item_action = ($action == 'enable') ? 'ENABLED' : 'DISABLED';

						add_log('admin', 'LOG_ITEM_' . $item_action, $user->lang['MODE_CURRENCY'], $row['item_name']);
						trigger_error($user->lang['CURRENCY_' . $item_action] . adm_back_link($this->u_action));
					break;
				}

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
			FROM ' . DONATION_ITEM_TABLE . '
			WHERE ' . (($action == 'move_up') ? 'right_id < ' . (int) $item_row['right_id'] . ' ORDER BY right_id DESC' : 'left_id > ' . (int) $item_row['left_id'] . ' ORDER BY left_id ASC');
		$result = $db->sql_query_limit($sql, $steps);

		$target = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$target = $row;
		}
		$db->sql_freeresult($result);

		if (!sizeof($target))
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
			$left_id = (int) $target['left_id'];
			$right_id = (int) $item_row['right_id'];

			$diff_up = (int) ($item_row['left_id'] - $target['left_id']);
			$diff_down = (int) ($item_row['right_id'] + 1 - $item_row['left_id']);

			$move_up_left = (int) $item_row['left_id'];
			$move_up_right = (int) $item_row['right_id'];
		}
		else
		{
			$left_id = (int) $item_row['left_id'];
			$right_id = (int) $target['right_id'];

			$diff_up = (int) ($item_row['right_id'] + 1 - $item_row['left_id']);
			$diff_down = (int) ($target['right_id'] - $item_row['right_id']);

			$move_up_left = (int) ($item_row['right_id'] + 1);
			$move_up_right = (int) $target['right_id'];
		}

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

	/**
	* prepares the preview text
	*/
	function preview_announcement($text)
	{
		$uid			= $bitfield			= $options	= '';
		$allow_bbcode	= $allow_smilies	= true;
		$allow_urls		= false;
		//lets (mis)use generate_text_for_storage to create some uid, bitfield... for our preview
		generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
		//now we created it, lets show it
		$text			= generate_text_for_display($text, $uid, $bitfield, $options);

		return $text;
	}
}
?>