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
	'TOTAL_CAT'			=> $config['kb_total_cats'],
	'TOTAL_ARTICLES'	=> $config['kb_total_articles'],
	'TOTAL_COMMENTS'	=> $config['kb_total_comments'],
	'LAST_UPDATED'		=> $user->format_date($config['kb_last_updated']),
));

// Call random article & latest article
get_random_article($cat_id);
get_latest_article();

// Handle all knowledge base related stuff, this file is only to call it, makes the user able to move it around
gen_kb_auth_level();
$kb = new knowledge_base($cat_id);

// Get latest article
function get_latest_article()
{
	global $config, $db, $template, $phpbb_root_path, $phpEx;

	$sql = 'SELECT article_title, article_desc, article_desc_bitfield, article_desc_options, article_desc_uid, article_views, article_comments 
		FROM ' . KB_TABLE . '
		WHERE article_id = ' . $config['kb_last_article'];
	$result = $db->sql_query($sql, 3600);
	$row = $db->sql_fetchrow($result);
	if (!$db->sql_affectedrows())	
	{
		$template->assign_vars(array(
			'NO_LAST_ARTICLE'		=> true,
		));
		
		return;
	}	
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'LAST_ARTICLE_TITLE'		=> $row['article_title'],
		'LAST_ARTICLE_DESC'			=> generate_text_for_display($row['article_desc'], $row['article_desc_uid'], $row['article_desc_bitfield'], $row['article_desc_options']),
		'LAST_ARTICLE_COMMENTS'		=> $row['article_comments'],
		'LAST_ARTICLE_VIEWS'		=> $row['article_views'],
		
		'U_LAST_ARTICLE'			=> append_sid("{$phpbb_root_path}kb.$phpEx", 'a=' . $config['kb_last_article']),
	));
}

// Random article - need all ids first, May as well cache the results for an hour to add later after debuggig
function get_random_article($cat_id, $second_pass = false)
{
	global $db, $template, $phpbb_root_path, $phpEx;

	$random_where = ($cat_id == 0) ? '' : " WHERE cat_id = '" . $db->sql_escape($cat_id) . "'";

	$all_ids = array();

	$sql = 'SELECT article_id 
		FROM ' . KB_TABLE . "
		$random_where";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
	{
		$all_ids[] = $row['article_id'];
	}
	
	if (!$db->sql_affectedrows())	
	{
		if (!$second_pass)
		{
			// Call again with second pass saves writing another sql
			get_random_article(0, true);
		}
		else
		{
			$template->assign_vars(array(
				'NO_ARTICLE'		=> true,
			));
		}
		
		return;
	}	
	$db->sql_freeresult($result);
	
	$value = array_rand($all_ids);
	$article_id = $all_ids[$value];

	$sql = 'SELECT article_title, article_desc, article_desc_bitfield, article_desc_options, article_desc_uid, article_views, article_comments 
		FROM ' . KB_TABLE . '
		WHERE article_id = ' . $article_id;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'RAN_ARTICLE_TITLE'		=> $row['article_title'],
		'RAN_ARTICLE_DESC'		=> generate_text_for_display($row['article_desc'], $row['article_desc_uid'], $row['article_desc_bitfield'], $row['article_desc_options']),
		'RAN_ARTICLE_COMMENTS'	=> $row['article_comments'],
		'RAN_ARTICLE_VIEWS'		=> $row['article_views'],
		
		'U_RAN_ARTICLE'			=> append_sid("{$phpbb_root_path}kb.$phpEx", 'a=' . $article_id),
	));
}

?>