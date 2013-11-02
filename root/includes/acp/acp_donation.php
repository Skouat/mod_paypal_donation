<?php
/**
*
* @package PayPal Donation MOD
* @copyright (c) 2013 Skouat
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
	const CHECK_HOST = 'http://skouat31.free.fr';

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

		// $mode is used in SQL requests, so for extra safety we will use sql_escape()
		$mode = $db->sql_escape($mode);

		switch ($mode)
		{
			case 'overview':

				global $phpbb_admin_path, $auth;

				$this->page_title = 'DONATION_OVERVIEW';

				if ($action)
				{
					if (!confirm_box(true))
					{
						switch ($action)
						{
							case 'date':
								$confirm = true;
								$confirm_lang = 'STAT_RESET_DATE_CONFIRM';
							break;
							case 'transactions':
								$confirm = true;
								$confirm_lang = 'STAT_RESYNC_TRANSACTIONSCOUNTS_CONFIRM';
							break;
							case 'donors':
								$confirm = true;
								$confirm_lang = 'STAT_RESYNC_DONORSCOUNTS_CONFIRM';
							break;

							default:
								$confirm = true;
								$confirm_lang = 'CONFIRM_OPERATION';
						}

						if ($confirm)
						{
							confirm_box(false, $user->lang[$confirm_lang], build_hidden_fields(array(
								'i'			=> $id,
								'mode'		=> $mode,
								'action'	=> $action,
							)));
						}
					}
					else
					{
						switch ($action)
						{

							case 'date':
								if (!$auth->acl_get('a_board'))
								{
									trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
								}

								set_config('donation_install_date', time() - 1);
								add_log('admin', 'LOG_STAT_RESET_DATE');
							break;

							case 'transactions':
								if (!$auth->acl_get('a_board'))
								{
									trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
								}

								update_transactions_stats();

								add_log('admin', 'LOG_STAT_RESYNC_TRANSACTIONSCOUNTS');
							break;

							case 'donors':
								if (!$auth->acl_get('a_board'))
								{
									trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
								}

								// Count the number of donors based on the user_id, except anonymous. 
								update_known_donors_stats();

								// Count the number of anonymous users. Based on the PayPal Payer ID
								update_anonymous_donors_stats();

								add_log('admin', 'LOG_STAT_RESYNC_DONORSCOUNTS');

							break;

						}
					}
				}

				// Check if a new version of this MOD is available
				$latest_version_info = $this->obtain_latest_version_info(request_var('donation_versioncheck_force', false));

				if ($latest_version_info === false || !function_exists('phpbb_version_compare'))
				{
					$template->assign_vars(array(
						'S_DONATION_VERSIONCHECK_FAIL'	=> true,
						'L_VERSIONCHECK_FAIL'			=> sprintf($user->lang['VERSIONCHECK_FAIL'], $latest_version_info),
					));
				}
				else
				{
					$latest_version_info = explode("\n", $latest_version_info);

					$template->assign_vars(array(
						'S_DONATION_VERSION_UP_TO_DATE'	=> phpbb_version_compare(trim($latest_version_info[0]), $config['donation_mod_version'], '<='),
						'U_DONATION_VERSIONCHECK'		=> $latest_version_info[1],
					));
				}

				// Check if fsockopen and cURL are available and display it in stats
				$info_curl = $info_fsockopen = $user->lang['INFO_NOT_DETECTED'];
				$s_curl = $s_fsockopen = false;

				if (function_exists('fsockopen'))
				{
					$url = parse_url($this::CHECK_HOST);

					$fp = @fsockopen($url['host'], 80);

					if ($fp)
					{
						$info_fsockopen = $user->lang['INFO_DETECTED'];
						$s_fsockopen = true;
					}
				}

				if (function_exists('curl_init') && function_exists('curl_exec'))
				{

					$ch = curl_init($this::CHECK_HOST);

					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					$response = curl_exec($ch);
					$response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

					curl_close ($ch);

					if ($response !== false || $response_status != '0')
					{
						$info_curl = $user->lang['INFO_DETECTED'];
						$s_curl = true;
					}
				}

				// Get MOD statistics
				$total_transactions = $config['donation_num_transactions'];
				$total_known_donors = $config['donation_num_known_donors'];
				$total_anonymous_donors = $config['donation_num_anonymous_donors'];

				$donation_install_date = $user->format_date($config['donation_install_date']);

				$moddays = (time() - $config['donation_install_date']) / 86400;

				$transactions_per_day = sprintf('%.2f', $total_transactions / $moddays);
				$known_donors_per_day = sprintf('%.2f', $total_known_donors / $moddays);
				$donors_anonymous_per_day = sprintf('%.2f', $total_anonymous_donors / $moddays);

				if ($transactions_per_day > $total_transactions)
				{
					$transactions_per_day = $total_transactions;
				}

				if ($known_donors_per_day > $total_known_donors)
				{
					$known_donors_per_day = $total_known_donors;
				}

				if ($donors_anonymous_per_day > $total_anonymous_donors)
				{
					$donors_anonymous_per_day = $total_anonymous_donors;
				}

				$template->assign_vars(array(
					'DONATION_INSTALL_DATE'		=> $donation_install_date,
					'DONATION_VERSION'			=> $config['donation_mod_version'],
					'TOTAL_TRANSACTIONS'		=> $total_transactions,
					'TRANSACTIONS_PER_DAY'		=> $transactions_per_day,
					'TOTAL_KNOWN_DONORS'		=> $total_known_donors,
					'KNOWN_DONORS_PER_DAY'		=> $known_donors_per_day,
					'TOTAL_ANONYMOUS_DONORS'	=> $total_anonymous_donors,
					'ANONYMOUS_DONORS_PER_DAY'	=> $donors_anonymous_per_day,
					'INFO_CURL'					=> $info_curl,
					'INFO_FSOCKOPEN'			=> $info_fsockopen,

					'U_DONATION_VERSIONCHECK_FORCE'	=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=donation&amp;mode=' . $mode . '&amp;donation_versioncheck_force=1'),
					'U_ACTION'						=> $this->u_action,

					'S_ACTION_OPTIONS'		=> ($auth->acl_get('a_board')) ? true : false,
					'S_FSOCKOPEN'			=> $s_fsockopen,
					'S_CURL'				=> $s_curl,
					'S_OVERVIEW'			=> $mode,
				));

			break;

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

						'legend2'						=> 'IPN_SETTINGS',
						'paypal_ipn_enable'				=> array('lang' => 'PAYPAL_IPN_ENABLE',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_ipn_logging'			=> array('lang' => 'PAYPAL_IPN_LOGGING',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'donation_donors_group_id'		=> array('lang' => 'DONATION_DONORS_GROUP',			'validate' => 'int',	'type' => 'select:8', 'function' => 'group_select_options', 'params' => array('{CONFIG_VALUE}', false, false), 'explain' => true),
						'donation_group_as_default'		=> array('lang' => 'DONATION_GROUP_AS_DEFAULT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true,),
						'donation_send_confirmation'	=> array('lang' => 'DONATION_SEND_CONFIRMATION',	'validate' => 'int',	'type' => 'select', 'function' => 'build_messenger_select', 'params' => array('{CONFIG_VALUE}'), 'explain' => true,),

						'legend3'						=> 'SANDBOX_SETTINGS',
						'paypal_sandbox_enable'			=> array('lang' => 'SANDBOX_ENABLE',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_sandbox_founder_enable'	=> array('lang' => 'SANDBOX_FOUNDER_ENABLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_sandbox_address'		=> array('lang' => 'SANDBOX_ADDRESS',				'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),

						'legend4'						=> 'DONATION_STATS_SETTINGS',
						'donation_stats_index_enable'	=> array('lang' => 'DONATION_STATS_INDEX_ENABLE',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true,),
						'donation_raised_enable'		=> array('lang' => 'DONATION_RAISED_ENABLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false,),
						'donation_raised'				=> array('lang' => 'DONATION_RAISED',				'validate' => 'float:0','type' => 'text:10:50', 'explain' => true,),
						'donation_goal_enable'			=> array('lang' => 'DONATION_GOAL_ENABLE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false,),
						'donation_goal'					=> array('lang' => 'DONATION_GOAL',					'validate' => 'float:0',	'type' => 'text:10:50', 'explain' => true,),
						'donation_used_enable'			=> array('lang' => 'DONATION_USED_ENABLE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false,),
						'donation_used'					=> array('lang' => 'DONATION_USED',					'validate' => 'float:0',	'type' => 'text:10:50', 'explain' => true,),

						'legend5'						=> 'ACP_SUBMIT_CHANGES',
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

				// We go compare $display_vars with $cfg_array to determine if the type "select" does not have a choice selected.
				if ($submit)
				{
					$display_vars_diff = array_diff_key($display_vars['vars'] , $cfg_array);

					foreach ($display_vars_diff as $config_name_diff => $vars_diff)
					{
						if (strpos($vars_diff['type'], 'select') === false)
						{
							continue;
						}

						$cfg_array[$config_name_diff] = '';
					}
					unset($config_name_diff);
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

				if (sizeof($error))
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

				global $cache;

				$ppdm = new ppdm_main();

				$this->page_title = 'DONATION_DP_CONFIG';

				$item_id = request_var('id', 0);
				$preview = request_var('preview', false);
				$add = request_var('add', false);
				$donation_name = request_var('donation_name', '');
				$action = $add ? 'add' : ($preview ? 'preview' : $action);

				// Retrieve available board language
				$langs = $this->get_languages();

				switch ($action)
				{
					case 'add':
					case 'edit':
					case 'preview':
						// okay, show the editor

						if (!function_exists('generate_smilies'))
						{
							include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
						}

						if (!function_exists('display_custom_bbcodes'))
						{
							include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
						}

						foreach ($langs as $lang => $entry)
						{
							$template->assign_block_vars('langs', array(
								'ISO' => $lang,
								'NAME' => $entry['name'],
							));
						}

						$input_pages = utf8_normalize_nfc(request_var('input_pages', '', true));
						$input_lang = request_var('lang_iso', '', true);

						$error = array();
						$dp_preview = false;

						// Initiate donation page data array
						$dp_data = array(
							'item_type'					=> $mode,
							'item_name'					=> $donation_name,
							'item_iso_code'				=> $input_lang,
							'item_text'					=> $input_pages,
							'item_text_bbcode_uid'		=> '',
							'item_text_bbcode_bitfield'	=> '',
						);

						if ($submit || $preview)
						{
							if (!class_exists('parse_message'))
							{
								include ($phpbb_root_path . 'includes/message_parser.' . $phpEx);
							}

							$message_parser = new parse_message($input_pages);

							// Allowing Quote BBCode
							$message_parser->parse(true, true, true, true, true, true, true, true, $mode);

							if (sizeof($message_parser->warn_msg))
							{
								$error[] = implode('<br />', $message_parser->warn_msg);
							}

							if (!check_form_key($form_key))
							{
								$error = 'FORM_INVALID';
							}

							if (!sizeof($error) && $submit && check_form_key($form_key))
							{
								$dp_data = array_merge($dp_data, array(
									'item_text'					=> (string) $message_parser->message,
									'item_text_bbcode_uid'		=> (string) $message_parser->bbcode_uid,
									'item_text_bbcode_bitfield'	=> (string) $message_parser->bbcode_bitfield,
								));

								if ($this->validate_input($dp_data))
								{
									if ($item_id || $item_id = $this->acp_exist_item_data($dp_data))
									{
										$this->acp_update_item_data($dp_data, $item_id);
									}
									else
									{
										$this->acp_add_item_data($dp_data);
									}

									$item_action = $item_id ? 'UPDATED' : 'ADDED';
									add_log('admin', 'LOG_ITEM_' . $item_action, $user->lang['MODE_DONATION_PAGES'], $user->lang[strtoupper($dp_data['item_name'])]);
									trigger_error($user->lang['DONATION_DP_LANG_' . $item_action] . adm_back_link($this->u_action));
								}

							}

							// Replace "error" strings with their real, localised form
							$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);

							if ($preview)
							{
								// Now parse it for displaying
								$dp_preview = $message_parser->format_display(true, true, true, false);
								unset($message_parser);
							}
						}

						if ($item_id && !$preview)
						{
							if (!$dp_data = $this->acp_get_item_data($item_id, $mode))
							{
								trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action));
							}
						}

						decode_message($dp_data['item_text'], $dp_data['item_text_bbcode_uid']);

						$s_hidden_fields = build_hidden_fields(array(
							'id'			=> $item_id,
							'action'		=> $action,
							'donation_name'	=> $dp_data['item_name'],
						));

						// Get predifined vars
						$ppdm->get_vars(true);

						for($i = 0; $i < sizeof($ppdm->vars); $i++)
						{
							$dp_vars[$ppdm->vars[$i]['var']] = $ppdm->vars[$i]['value'];
						}

						// Assigging predefined variables in a template block vars
						for ($i = 0, $size = sizeof($ppdm->vars); $i < $size; $i++)
						{
							$template->assign_block_vars('dp_vars', array(
								'NAME'		=> $ppdm->vars[$i]['name'],
								'VARIABLE'	=> $ppdm->vars[$i]['var'],
								'EXAMPLE'	=> $ppdm->vars[$i]['value'])
							);
						}

						$template->assign_vars(array(
							'DONATION_DRAFT_PREVIEW'	=> str_replace(array_keys($dp_vars), array_values($dp_vars), $dp_preview),
							'DONATION_BODY'				=> $dp_data['item_text'],
							'LANG_ISO'					=> !empty($item_id) ? $dp_data['item_iso_code'] : $input_lang,

							'L_DONATION_PAGES_TITLE'			=> !empty($dp_data['item_name']) ? $user->lang[strtoupper($dp_data['item_name'])] : $user->lang[$this->page_title],
							'L_DONATION_PAGES_TITLE_EXPLAIN'	=> !empty($dp_data['item_name']) ? $user->lang[strtoupper($dp_data['item_name']) . '_EXPLAIN'] : '',

							'S_EDIT_DP'			=> true,
							'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
						));

						// Generate smilies on inline displaying
						generate_smilies('inline', '');

						// Assigning custom bbcodes
						display_custom_bbcodes();
					break;

					case 'delete':
						if (!$item_id)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$sql = 'SELECT item_name
							FROM ' . DONATION_ITEM_TABLE . '
							WHERE item_id = ' . (int) $item_id;
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

							if (confirm_box(true))
							{
								$db->sql_query('DELETE FROM ' . DONATION_ITEM_TABLE . ' WHERE item_id = '. (int) $item_id);
								$cache->destroy('sql', DONATION_ITEM_TABLE);
								add_log('admin', 'LOG_ITEM_REMOVED', $user->lang[strtoupper($row['item_name'])]);
								trigger_error($user->lang['DONATION_DP_LANG_REMOVED'] . adm_back_link($this->u_action));
							}
							else
							{
								confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
									'item_id'	=> $item_id,
									'i'			=> $id,
									'mode'		=> $mode,
									'action'	=> $action,
									))
								);
							}
					break;
				}
				$template->assign_vars(array(
					'L_TITLE'			=> $user->lang[$this->page_title],
					'L_TITLE_EXPLAIN'	=> $user->lang[$this->page_title . '_EXPLAIN'],

					'S_DONATION_PAGES'	=> $mode,

					'U_ACTION'			=> $this->u_action,
				));

				// Show the list
				if (!$action || $action === 'delete')
				{
					// Template available language
					foreach ($langs as $lang => $entry)
					{
						$template->assign_block_vars('langs', array(
							'ISO' => $lang,
							'NAME' => $entry['name'],
						));

						// Build sql query with alias field
						$sql = 'SELECT item_id, item_name AS donation_title, item_iso_code AS lang_iso
							FROM ' . DONATION_ITEM_TABLE . "
							WHERE item_type = '" . $mode . "'
								AND item_iso_code = '" . $db->sql_escape($lang) . "'";
						$result = $db->sql_query($sql);

						while ($row = $db->sql_fetchrow($result))
						{
							$row['item_id'] = (int) $row['item_id'];

							$template->assign_block_vars('langs.dp_list', array(
								'DP_TITLE'			=> $user->lang[strtoupper($row['donation_title'])],
								'DP_LANG'			=> $row['lang_iso'],

								'U_DELETE'			=> $this->u_action . '&amp;action=delete&amp;id=' . $row['item_id'],
								'U_EDIT'			=> $this->u_action . '&amp;action=edit&amp;id=' . $row['item_id'],
							));
						}
						$db->sql_freeresult($result);
					}
				}

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
						WHERE item_type= '" . $mode . "'
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

						$sql = 'SELECT *
								FROM ' . DONATION_ITEM_TABLE . '
								WHERE item_id = ' . (int) $item_id . "
									AND item_type = '" . $mode . "'";
						$result = $db->sql_query($sql);
						$currency_ary = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if (!$currency_ary)
						{
							trigger_error($user->lang['MUST_SELECT_ITEM'] . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$s_hidden_fields = array('id' => $item_id);

					case 'add':
						if (empty($s_hidden_fields) || !is_array($s_hidden_fields))
						{
							$s_hidden_fields = array();
						}

						$s_hidden_fields = array_merge($s_hidden_fields, array('action' => 'save',));

						$template->assign_vars(array(
							'S_EDIT'			=> true,
							'S_MODE'			=> $mode,

							'U_ACTION'			=> $this->u_action,
							'U_BACK'			=> $this->u_action,

							'ITEM_NAME'			=> isset($currency_ary['item_name']) ? $currency_ary['item_name'] : utf8_normalize_nfc(request_var('item_name', '', true)),
							'ITEM_ISO_CODE'		=> isset($currency_ary['item_iso_code']) ? $currency_ary['item_iso_code'] : utf8_normalize_nfc(request_var('item_iso_code', '', true)),
							'ITEM_SYMBOL'		=> isset($currency_ary['item_symbol']) ? $currency_ary['item_symbol'] : utf8_normalize_nfc(request_var('item_symbol', '', true)),
							'ITEM_ENABLED'		=> isset($currency_ary['item_enable']) ? $currency_ary['item_enable'] : true,

							'S_HIDDEN_FIELDS'	=> build_hidden_fields($s_hidden_fields),
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

							trigger_error($user->lang['DONATION_DC_ENTER_NAME'] . adm_back_link($this->u_action . $trigger_url), E_USER_WARNING);
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
						trigger_error($user->lang['DONATION_DC_' . $item_action] . adm_back_link($this->u_action));

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

							trigger_error($user->lang['DONATION_DC_REMOVED'] . adm_back_link($this->u_action));
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

						$sql = 'SELECT *
								FROM ' . DONATION_ITEM_TABLE . '
								WHERE item_id = ' . (int) $item_id . "
									AND item_type = '" . $mode . "'";
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
									WHERE item_type = '" . $mode . "'
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

						$sql = 'SELECT *
								FROM ' . DONATION_ITEM_TABLE . '
								WHERE item_id = ' . (int) $item_id . "
									AND item_type = '" . $mode . "'";
						$result = $db->sql_query($sql);
						$row = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						$item_action = ($action == 'enable') ? 'ENABLED' : 'DISABLED';

						add_log('admin', 'LOG_ITEM_' . $item_action, $user->lang['MODE_CURRENCY'], $row['item_name']);
						trigger_error($user->lang['DONATION_DC_' . $item_action] . adm_back_link($this->u_action));
					break;
				}
			break;

			case 'transactions':
				if ($submit && !check_form_key($form_key))
				{
					trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action));
				}

				global $auth;

				$this->page_title = 'DONATION_DT_LOG';

				// Set up general vars
				$data_id	= request_var('id', 0);
				$start		= request_var('start', 0);
				$deletemark = (!empty($_POST['delmarked'])) ? true : false;
				$deleteall	= (!empty($_POST['delall'])) ? true : false;
				$marked		= request_var('mark', array(0));

				// Sort keys
				$sort_days	= request_var('st', 0);
				$sort_key	= request_var('sk', 't');
				$sort_dir	= request_var('sd', 'd');

				// Delete entries if requested and able
				if (($deletemark || $deleteall) && $auth->acl_get('a_clearlogs'))
				{
					if (confirm_box(true))
					{
						$where_sql = '';

						if ($deletemark && sizeof($marked))
						{
							$sql_in = array();
							foreach ($marked as $mark)
							{
								$sql_in[] = $mark;
							}
							$where_sql = ' WHERE ' . $db->sql_in_set('transaction_id', $sql_in);
							unset($sql_in);
						}

						if ($where_sql || $deleteall)
						{
							$sql = 'DELETE FROM ' . DONATION_DATA_TABLE . "$where_sql";
							$db->sql_query($sql);

							update_transactions_stats();
							update_known_donors_stats();
							update_anonymous_donors_stats();

							add_log('admin', 'LOG_CLEAR_DONATION_TXN');
						}
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'start'		=> $start,
							'delmarked'	=> $deletemark,
							'delall'	=> $deleteall,
							'mark'		=> $marked,
							'st'		=> $sort_days,
							'sk'		=> $sort_key,
							'sd'		=> $sort_dir,
							'i'			=> $id,
							'mode'		=> $mode,
							'action'	=> $action))
						);
					}
				}

				// Action: view
				if ($action == 'view')
				{
					$id = request_var('id', 0);

					$sql = 'SELECT dd.*, u.username
						FROM ' . DONATION_DATA_TABLE . ' dd
						INNER JOIN  ' . USERS_TABLE . ' u
							ON dd.user_id = u.user_id
						WHERE dd.transaction_id = ' . (int) $id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					// Initiate vars to retrieve the 'payment_status' translation from the language key
					$payment_status_ary = $user->lang['DONATION_DT_PAYMENT_STATUS_VALUES'];
					$payment_status_name = strtolower($row['payment_status']);

					$template->assign_vars(array(
						'L_TITLE'			=> $user->lang[$this->page_title],
						'L_TITLE_EXPLAIN'	=> $user->lang[$this->page_title . '_EXPLAIN'],

						'U_ACTION'			=> $this->u_action,
						'U_BACK'			=> $this->u_action,

						'S_VIEW'			=> true,
						'S_TXN'				=> true,
						'S_CONVERT'			=> ($row['settle_amount'] === '0.00' && empty($row['exchange_rate'])) ? false : true,
//						'S_MEMO'			=> (empty($row['memo'])) ? false : true,

						'TXN_ID'			=> $row['txn_id'],

						'BOARD_USERNAME'	=> $row['username'],
						'NAME'				=> $row['first_name'] . ' ' . $row['last_name'],
						'PAYER_EMAIL'		=> $row['payer_email'],
						'PAYER_ID'			=> $row['payer_id'],
						'PAYER_STATUS'		=> ($row['payer_status']) ? $user->lang['DONATION_DT_VERIFIED'] : $user->lang['DONATION_DT_UNVERIFIED'],

						'RECEIVER_EMAIL'	=> $row['receiver_email'],
						'RECEIVER_ID'		=> $row['receiver_id'],

						'MC_GROSS'			=> $row['mc_gross'] . ' ' . $row['mc_currency'],
						'MC_FEE'			=> '-' . $row['mc_fee'] . ' ' . $row['mc_currency'],
						'MC_NET'			=> $row['net_amount'] . ' ' . $row['mc_currency'],

						'CONVERT_FROM'		=> '-' . $row['net_amount'] . ' ' . $row['mc_currency'],
						'SETTLE_AMOUNT'		=> $row['settle_amount'] . ' ' . $row['settle_currency'],
						'EXCHANGE_RATE'		=> '1 ' . $row['mc_currency'] . ' = ' . $row['exchange_rate'] . ' ' . $row['settle_currency'],

						'ITEM_NAME'			=> $row['item_name'],
						'ITEM_NUMBER'		=> $row['item_number'],
						'PAYMENT_DATE'		=> $user->format_date(strtotime($row['payment_date'])),
						'PAYMENT_STATUS'	=> $payment_status_ary[$payment_status_name],

//						'MEMO'				=> $row['memo'],
					));
				}

				//skip this code if action is used
				if (!$action)
				{
					// Sorting
					$limit_days = array(0 => $user->lang['ALL_ENTRIES'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);
					$sort_by_text = array('txn' => $user->lang['DONATION_DT_SORT_TXN_ID'], 'u' => $user->lang['DONATION_DT_SORT_DONORS'], 'ipn' => $user->lang['DONATION_DT_SORT_IPN_STATUS'], 'ps' => $user->lang['DONATION_DT_SORT_PAYMENT_STATUS'], 't' => $user->lang['SORT_DATE']);
					$sort_by_sql = array('txn' => 'dd.txn_id', 'u' => 'u.username_clean', 'ipn' => 'dd.confirmed', 'ps' => 'dd.payment_status', 't' => 'dd.payment_time');

					$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
					gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

					// Define where and sort sql for use in displaying transactions
					$sql_where = ($sort_days) ? (time() - ($sort_days * 86400)) : 0;
					$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

					$keywords = utf8_normalize_nfc(request_var('keywords', '', true));
					$keywords_param = !empty($keywords) ? '&amp;keywords=' . urlencode(htmlspecialchars_decode($keywords)) : '';

					// Grab log data
					$log_data = array();
					$log_count = 0;
					$this->view_txn_log($log_data, $log_count, $config['topics_per_page'], $start, 0, $sql_where, $sql_sort, $keywords);

					$template->assign_vars(array(
						'L_TITLE'			=> $user->lang[$this->page_title],
						'L_TITLE_EXPLAIN'	=> $user->lang[$this->page_title . '_EXPLAIN'],

						'PAGINATION'		=> generate_pagination($this->u_action . "&amp;$u_sort_param$keywords_param", $log_count, $config['topics_per_page'], $start, true),

						'S_TXN'				=> $mode,
						'S_ON_PAGE'			=> on_page($log_count, $config['topics_per_page'], $start),
						'S_KEYWORDS'		=> $keywords,
						'S_LIMIT_DAYS'		=> $s_limit_days,
						'S_SORT_KEY'		=> $s_sort_key,
						'S_SORT_DIR'		=> $s_sort_dir,
						'S_CLEARLOGS'		=> $auth->acl_get('a_clearlogs'),

						'U_ACTION'			=> $this->u_action . "&amp;$u_sort_param$keywords_param&amp;start=$start",
					));

					foreach ($log_data as $row)
					{
						// Initiate vars to retrieve the 'payment_status' translation from the language key
						$payment_status_ary = $user->lang['DONATION_DT_PAYMENT_STATUS_VALUES'];
						$payment_status_name = strtolower($row['payment_status']);

						$template->assign_block_vars('log', array(
							'TNX_ID'			=> $row['txn_id'],
							'USERNAME'			=> $row['username_full'],
							'DATE'				=> $user->format_date(strtotime($row['payment_date'])),
							'ID'				=> $row['transaction_id'],
							'CONFIRMED'			=> ($row['confirmed']) ? $user->lang['DONATION_DT_VERIFIED'] : $user->lang['DONATION_DT_UNVERIFIED'],
							'PAYMENT_STATUS'	=> $payment_status_ary[$payment_status_name],

							'S_CONFIRMED'		=> ($row['confirmed']) ? false : true,
							'S_PAYMENT_STATUS'	=> ($payment_status_name === 'completed') ? false : true,
						));
						unset($payment_status_name);
					}
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
	* Grab an item and bring it into a format the editor understands
	*/
	function acp_get_item_data($item_id, $item_type)
	{
		global $db;

		if ($item_id)
		{
			$sql = 'SELECT *
				FROM ' . DONATION_ITEM_TABLE . '
				WHERE item_id = ' . $item_id . "
					AND item_type= '" . $db->sql_escape($item_type) ."'";
			$result = $db->sql_query($sql);
			$item = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!$item)
			{
				return false;
			}

			return $item;
		}
	}

	/**
	* Grab item_id if $data match
	*/
	function acp_exist_item_data($data)
	{
		global $db;

		$sql = 'SELECT item_id
			FROM ' . DONATION_ITEM_TABLE . "
			WHERE item_type= '" . $db->sql_escape($data['item_type']) ."'
				AND item_name = '" . $db->sql_escape($data['item_name']) . "'
				AND item_iso_code = '" . $db->sql_escape($data['item_iso_code']) . "'";
		$result = $db->sql_query($sql);
		$item = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$item)
		{
			return false;
		}
		return $item['item_id'];
	}

	/**
	* List the installed language packs
	*/
	function get_languages()
	{
		global $db;

		$sql = 'SELECT *
			FROM ' . LANG_TABLE;
		$result = $db->sql_query($sql);

		$langs = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$langs[$row['lang_iso']] = array(
				'name'	=> $row['lang_local_name'],
				'id'	=> (int) $row['lang_id'],
			);
		}
		$db->sql_freeresult($result);

		return $langs;
	}

	/**
	* Insert an item.
	* param mixed $data : an array as created from acp_get_item_data
	*/
	function acp_add_item_data($data)
	{
		global $db, $cache;

		$sql = 'INSERT INTO ' . DONATION_ITEM_TABLE . ' ' . $db->sql_build_array('INSERT', $data);
		$db->sql_query($sql);

		$cache->destroy('sql', DONATION_ITEM_TABLE);
	}

	/**
	* Update an item.
	* param mixed $data : an array as created from acp_get_item_data
	*/
	function acp_update_item_data($data, $item_id)
	{
		global $db, $cache;

		$sql = 'UPDATE ' . DONATION_ITEM_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $data) . '
			WHERE item_id = ' . (int) $item_id;
		$db->sql_query($sql);

		$cache->destroy('sql', DONATION_ITEM_TABLE);
	}

	/**
	* Check if the entered data can be inserted/used
	* param mixed $data : an array as created from acp_get_item_data
	*/
	function validate_input($data)
	{
		$langs = $this->get_languages();

		if (!isset($data['item_iso_code']) ||
			!isset($data['item_name']) ||
			!isset($data['item_text']))
		{
			return false;
		}

		if (!isset($langs[$data['item_iso_code']]) ||
			!strlen($data['item_name']))
		{
			return false;
		}

		return true;
	}

	/**
	* View transaction log
	* If $log_count is set to false, we will skip counting all entries in the database.
	*/
	function view_txn_log(&$log, &$log_count, $limit = 0, $offset = 0, $user_id = 0, $limit_days = 0, $sort_by = 'dd.payment_time DESC', $keywords = '')
	{
		global $db, $user, $auth, $phpEx, $phpbb_root_path, $phpbb_admin_path;

		$topic_id_list = $reportee_id_list = $is_auth = $is_mod = array();

		if (defined('IN_ADMIN'))
		{
			$profile_url = append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview');
			$txn_url = append_sid("{$phpbb_admin_path}index.$phpEx", 'i=donation&amp;mode=transactions');
		}
		else
		{
			$profile_url = append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile');
			$txn_url = '';
		}

		// Use no preg_quote for $keywords because this would lead to sole backslashes being added
		// We also use an OR connection here for spaces and the | string. Currently, regex is not supported for searching (but may come later).
		$keywords = preg_split('#[\s|]+#u', utf8_strtolower($keywords), 0, PREG_SPLIT_NO_EMPTY);
		$sql_keywords = '';

		if (!empty($keywords))
		{
			// Build keywords...
			for ($i = 0, $num_keywords = sizeof($keywords); $i < $num_keywords; $i++)
			{
				$keywords_pattern[] = preg_quote($keywords[$i], '#');
				$keywords[$i] = $db->sql_like_expression($db->any_char . $keywords[$i] . $db->any_char);
			}

			$sql_keywords = 'AND (LOWER(dd.txn_id) ' . implode(' OR LOWER(dd.txn_id) ', $keywords) . '
							OR LOWER(u.username) ' . implode(' OR LOWER(u.username) ', $keywords) . ')';
		}

		if ($log_count !== false)
		{
			$sql = 'SELECT COUNT(dd.transaction_id) AS total_entries
				FROM ' . DONATION_DATA_TABLE . ' dd
				INNER JOIN ' . USERS_TABLE . " u
					ON dd.user_id = u.user_id
				WHERE dd.payment_time >= $limit_days
					$sql_keywords";
			$result = $db->sql_query($sql);
			$log_count = (int) $db->sql_fetchfield('total_entries');
			$db->sql_freeresult($result);
		}

		// $log_count may be false here if false was passed in for it,
		// because in this case we did not run the COUNT() query above.
		// If we ran the COUNT() query and it returned zero rows, return;
		// otherwise query for logs below.
		if ($log_count === 0)
		{
			// Save the queries, because there are no logs to display
			return 0;
		}

		if ($offset >= $log_count)
		{
			$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
		}

		$sql = 'SELECT dd.transaction_id, dd.txn_id, dd.confirmed, dd.payment_date, dd.payment_status, dd.user_id, u.username, u.user_colour
			FROM ' . DONATION_DATA_TABLE . ' dd, ' . USERS_TABLE . ' u
			WHERE u.user_id = dd.user_id
				' . (($limit_days) ? "AND dd.payment_time >= $limit_days" : '') . "
				$sql_keywords
			ORDER BY $sort_by";
		$result = $db->sql_query_limit($sql, $limit, $offset);

		$i = 0;
		$log = array();

		while ($row = $db->sql_fetchrow($result))
		{
			$log[$i] = array(
				'transaction_id'	=> $row['transaction_id'],
				'txn_id'			=> $this->build_transaction_url($row['transaction_id'], $row['txn_id'], $txn_url, $row['confirmed']),
				'confirmed'			=> $row['confirmed'],
				'payment_status'	=> $row['payment_status'],
				'payment_date'		=> $row['payment_date'],

				'username_full'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], false, $profile_url),
			);

			$i++;
		}
		$db->sql_freeresult($result);

		return $offset;
	}

	/**
	* Build transaction url for placing into templates.
	*
	* @param int $id The users transaction id
	* @param string $txn_id The txn number id
	* @param string $custom_url optional parameter to specify a profile url. The transaction id get appended to this url as &amp;id={id}
	*
	* @return string A string consisting of what is wanted.
	*/
	function build_transaction_url($id, $txn_id, $custom_url = '', $color = false)
	{
		static $_profile_cache;

		// We cache some common variables we need within this function
		if (empty($_profile_cache))
		{
			$_profile_cache['tpl_nourl'] = '{TRANSACTION}';
			$_profile_cache['tpl_url'] = '<a href="{TXN_URL}">{TRANSACTION}</a>';
		}

		// Build correct transaction url
		$txn_url = '';
		if ($txn_id)
		{
			$txn_url = ($custom_url !== '') ? $custom_url . '&amp;action=view&amp;id=' . $id : $txn_id;
		}

		// Return 
		if (!$txn_url)
		{
			return str_replace('{TRANSACTION}', $txn_id, $_profile_cache['tpl_nourl']);
		}

		return str_replace(array('<a', '{TXN_URL}', '{TRANSACTION}'), array((!$color ? '<a style="color:#FF0000;"' : '<a'), $txn_url, $txn_id), $_profile_cache['tpl_url']);
	}

	/**
	 * Obtains the latest version information
	 *
	 * @param bool $force_update Ignores cached data. Defaults to false.
	 * @param bool $warn_fail Trigger a warning if obtaining the latest version information fails. Defaults to false.
	 * @param int $ttl Cache version information for $ttl seconds. Defaults to 86400 (24 hours).
	 *
	 * @return string | false Version info on success, false on failure.
	 */
	function obtain_latest_version_info($force_update = false, $warn_fail = false, $ttl = 86400)
	{
		global $cache;

		$info = $cache->get('donationversioncheck');

		if ($info === false || $force_update)
		{
			$errstr = '';
			$errno = 0;

			$info = get_remote_file('skouat31.free.fr', '/phpbb', 'paypal_donation_11x.txt', $errstr, $errno);

			if ($info === false)
			{
				$cache->destroy('donationversioncheck');
				if ($warn_fail)
				{
					trigger_error($errstr, E_USER_WARNING);
				}
				return false;
			}

			$cache->put('donationversioncheck', $info, $ttl);
		}

		return $info;
	}
}
?>