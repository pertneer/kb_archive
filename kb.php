<?php
/**
*
* @package phpBB Knowledge Base Mod (KB)
* @version $Id$
* @copyright (c) 2009 Andreas Nexmann
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

// Init session etc, we will just add lang files along the road
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/kb');

// Lets start the install/update of the kb
// Automatically install or update if required
if ((!isset($config['kb_version']) || $config['kb_version'] != KB_VERSION) && $auth->acl_get('a_') && !empty($user->data['is_registered']))
{
	if(confirm_box(true))
	{
		if (!class_exists('umil'))
		{
			$umil_file = $phpbb_root_path . 'umil/umil.' . $phpEx;
			if (!file_exists($umil_file))
			{
				trigger_error('KB_UPDATE_UMIL', E_USER_ERROR);
			}
	
			include($umil_file);
		}
	
		$umil = new umil(true);
	
		$versions = get_kb_versions();
	
		$umil->run_actions('update', $versions, 'kb_version');
		unset($versions);
		
		$install_mode = request_var('install_mode', 'install');
		$message = ($install_mode == 'install') ? 'KB_INSTALLED' : 'KB_UPDATED';
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
	
	redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
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
	'U_KB_SEARCH'		=> append_sid("{$phpbb_root_path}kb.$phpEx", 'i=search' . $cat_search),
	'U_KB_SEARCH_ADV'	=> append_sid("{$phpbb_root_path}kb.$phpEx", 'i=search'),
	'TOTAL_KB_CAT'		=> $config['kb_total_cats'],
	'TOTAL_KB_ARTICLES'	=> $config['kb_total_articles'],
	'TOTAL_KB_COMMENTS'	=> $config['kb_total_comments'],
	'LAST_UPDATED'		=> $user->format_date($config['kb_last_updated']),
	'U_MCP'				=> ($auth->acl_get('m_kb')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=kb') : false,
));


// Call random article & latest article
get_random_article($cat_id);
get_latest_article();

// Handle all knowledge base related stuff, this file is only to call it, makes the user able to move it around
gen_kb_auth_level();
$kb = new knowledge_base($cat_id);

?>