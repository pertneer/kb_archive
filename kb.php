<?php
/**
*
* @package phpBB Knowledge Base Mod (KB)
* @version $Id: kb.php 357 2009-11-10 15:49:48Z softphp $
* @copyright (c) 2009 Andreas Nexmann, Tom Martin
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/kb.' . $phpEx);
include($phpbb_root_path . 'includes/constants_kb.' . $phpEx);
include($phpbb_root_path . 'includes/functions_kb.' . $phpEx);
include($phpbb_root_path . 'includes/functions_plugins_kb.' . $phpEx);

// Init session etc, we will just add lang files along the road
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/kb');

// Bug in update function, this will correct it
if($config['kb_version'] == '1.0.1RC1')
{
	set_config('kb_version', '1.0.0RC2');
}

// Lets start the install/update of the kb
// Automatically install or update if required
if ((!isset($config['kb_version']) || $config['kb_version'] != KB_VERSION) && $auth->acl_get('a_') && !empty($user->data['is_registered']))
{
	if(confirm_box(true))
	{
		$old_version = (isset($config['kb_version'])) ? $config['kb_version'] : '';
		
		if (!class_exists('umil'))
		{
			$umil_file = $phpbb_root_path . 'umil/umil.' . $phpEx;
			if (!file_exists($umil_file))
			{
				trigger_error('KB_UPDATE_UMIL', E_USER_ERROR);
			}
	
			include($umil_file);
		}
			
		// Log the action
		$install_mode = request_var('install_mode', 'install');
		if($install_mode == 'install')
		{
			$message = sprintf($user->lang['KB_INSTALLED'], KB_VERSION);
			add_log('admin', 'LOG_KB_INSTALL', KB_VERSION);
		}
		else
		{
			$message = sprintf($user->lang['KB_UPDATED'], KB_VERSION);
			add_log('admin', 'LOG_KB_UPDATED', KB_VERSION, $old_version);
		}
		
		$umil = new umil(true);
		
		include($phpbb_root_path . 'includes/functions_install_kb.' . $phpEx);
		$versions = get_kb_versions();
	
		$umil->run_actions($install_mode, $versions, 'kb_version');
		
		unset($versions);
		trigger_error($message);
	}
	else
	{
		$message = (isset($config['kb_version'])) ? 'UPDATE_KB' : 'INSTALL_KB';
		$hidden_fields = build_hidden_fields(array(
			'install_mode'	=> (isset($config['kb_version'])) ? 'update' : 'install'
		));
		confirm_box(false, $message, $hidden_fields);
	}
	
	redirect(kb_append_sid("{$phpbb_root_path}index.$phpEx"));
}
else if(!isset($config['kb_version']) || $config['kb_version'] != KB_VERSION)
{
	trigger_error('KB_NOT_ENABLE');
}

if (!isset($config['kb_enable']) || !$config['kb_enable'] && !($auth->acl_get('a_') || $auth->acl_get('m_kb')))
{
	trigger_error('KB_NOT_ENABLE');
}

// set cat id here needed for search and random article to work properly
$cat_id = request_var('c', 0);

// For search
$cat_search = ($cat_id == 0) ? '' : '&amp;cat_ids[]=' . $cat_id;

// Some default template variables
$template->assign_vars(array(
	'U_MCP'				=> ($auth->acl_get('m_kb')) ? kb_append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=kb') : false,
));

// Handle all knowledge base related stuff, this file is only to call it, makes the user able to move it around
gen_kb_auth_level($cat_id);
$kb = new knowledge_base($cat_id);

?>