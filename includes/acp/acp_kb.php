<?php
/**
*
* @package phpBB Knowledge Base Mod (KB)
* @copyright (c) 2009 Andreas Nexmann, Tom Martin
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	// Avoid Hacking attempts.
	exit;
}

/**
* @package acp
*/
class acp_kb
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $user, $template, $config, $phpbb_root_path, $phpEx, $table_prefix;

		$user->add_lang('mods/kb');
		include($phpbb_root_path . 'includes/constants_kb.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_kb.' . $phpEx);

		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;		
		$error = array();

		$form_key = 'acp_kb';
		add_form_key($form_key);
		
		// Set templates
		switch ($mode)
		{
			case 'settings':
				$this->tpl_name = 'acp_kb';
				$this->page_title = 'ACP_KB_' . strtoupper($mode);
			break;
			
			case 'health_check':
				$this->tpl_name = 'acp_kb_health';
				$this->page_title = 'ACP_KB_HEALTH_CHECK';
			break;
		}

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writable), path (relative path, but able to escape the root), wpath (writable)
		*/
		switch ($mode)
		{
			case 'settings':
				$display_vars = array(
					'title'	=> 'ACP_KB_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_KB_SETTINGS',
						'kb_enable'				=> array('lang' => 'KB_ENABLE',			'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),						
						'kb_allow_subscribe'	=> array('lang' => 'KB_ALLOW_SUB',		'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),
						'kb_allow_bookmarks'	=> array('lang' => 'KB_ALLOW_BOOK',		'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),
						'kb_articles_per_page'	=> array('lang' => 'KB_ART_PER_PAGE',	'validate' => 'int',	'type' => 'text:3:5', 		'explain' => false),
						'kb_comments_per_page'	=> array('lang' => 'KB_COM_PER_PAGE',	'validate' => 'int',	'type' => 'text:3:5', 		'explain' => false),
						
						'legend2'				=> 'ACP_KB_POST_SETTINGS',
						'kb_allow_attachments'	=> array('lang'	=> 'KB_ALLOW_ATTACH',	'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),
						'kb_allow_sig'			=> array('lang' => 'KB_ALLOW_SIG',		'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),
						'kb_allow_bbcode'		=> array('lang'	=> 'KB_ALLOW_BBCODE',	'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),
						'kb_allow_smilies'		=> array('lang' => 'KB_ALLOW_SMILES',	'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),
						'kb_allow_post_flash'	=> array('lang' => 'KB_ALLOW_FLASH',	'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),
						'kb_allow_post_links'	=> array('lang' => 'KB_ALLOW_LINKS',	'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false),
					)
				);
			break;
			
			case 'health_check':
				// Get current and latest version
				$errstr = '';
				$errno = 0;

				$info = get_remote_file('www.kb.softphp.dk', '/version_check', 'kb.txt', $errstr, $errno);

				if ($info === false)
				{
					trigger_error($errstr, E_USER_WARNING);
				}

				$info = explode("\n", $info);
				
				// Update vars
				$latest_version = trim($info[0]);
				$announcement_url = trim($info[1]);
				$download_url = trim($info[2]);

				$current_version = $config['kb_version'];
				
				$kb_path = generate_board_url() . '/kb.' . $phpEx;

				$up_to_date = (version_compare(str_replace('rc', 'RC', strtolower($current_version)), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;

				$template->assign_vars(array(
					'S_UP_TO_DATE'		=> $up_to_date,
					'S_VERSION_CHECK'	=> true,
					'U_ACTION'			=> $this->u_action,

					'LATEST_VERSION'	=> $latest_version,
					'CURRENT_VERSION'	=> $current_version,

					'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['UPDATE_INSTRUCTIONS'], $announcement_url, $download_url, $kb_path),
				));
				
				$uninstall = (isset($_POST['uninstall'])) ? true : false;	
				if ($uninstall)
				{
					if(confirm_box(true))
					{
						if (!file_exists($phpbb_root_path . 'umil/umil_frontend.' . $phpEx))
						{
							trigger_error('KB_UPDATE_UMIL', E_USER_ERROR);
						}

						include($phpbb_root_path . 'umil/umil_frontend.' . $phpEx);
						$umil = new umil(true);
		
						include($phpbb_root_path . 'includes/functions_install_kb.' . $phpEx);
						$versions = get_kb_versions();
			
						$umil->run_actions('uninstall', $versions, 'kb_version');
						unset($versions);
						
						trigger_error('KB_UNINSTALLED');
					}
					else
					{
						$hidden_fields = build_hidden_fields(array(
							'uninstall'	=> true,
						));
						confirm_box(false, 'UNINSTALL_KB', $hidden_fields);
					}
				}
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
		
		// prevent CSRF attacks
		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}

		// Do not submit if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}
		
		if ($mode == 'settings')
		{
			$this->new_config = $config;
			$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;

			// We validate the complete config if whished
			validate_config_vars($display_vars['vars'], $cfg_array, $error);			

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
				add_log('admin', 'LOG_CONFIG_' . strtoupper($mode));

				trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
			}

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
					'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
				));

				unset($display_vars['vars'][$config_key]);
			}
		}
	}	
}

?>