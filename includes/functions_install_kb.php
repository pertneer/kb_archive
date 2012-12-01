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
	exit;
}

// Versions function
// Contains ALL information about installing/updating the mod
function get_kb_versions()
{
	$versions = array(
		'0.0.1'		=> array(
			'table_add'	=> array(
				array('phpbb_articles', array(
					'COLUMNS'		=> array(
						'article_id'				=> array('UINT', NULL, 'auto_increment'),
						'cat_id'					=> array('UINT', 0),
						'article_title'				=> array('VCHAR', ''),
						'article_desc'				=> array('TEXT_UNI', ''),
						'article_desc_bitfield'		=> array('VCHAR', ''),
						'article_desc_options'		=> array('UINT:11', 7),
						'article_desc_uid'			=> array('VCHAR:8', ''),
						'article_text'				=> array('MTEXT_UNI', ''),
						'article_checksum'			=> array('VCHAR:32', ''),
						'article_status'			=> array('TINT:1', 0),
						'article_attachment'		=> array('BOOL', 0),
						'article_views'				=> array('UINT', 0),
						'article_comments'			=> array('UINT', 0),
						'article_user_id'			=> array('UINT', 0),
						'article_user_name'			=> array('VCHAR_UNI:255', ''),
						'article_user_color'		=> array('VCHAR:6', ''),
						'article_time'				=> array('TIMESTAMP', 0),
						'article_tags'				=> array('VCHAR', ''),
						'article_icon'				=> array('UINT', 0),
						'enable_bbcode'				=> array('BOOL', 1),
						'enable_smilies'			=> array('BOOL', 1),
						'enable_magic_url'			=> array('BOOL', 1),
						'enable_sig'				=> array('BOOL', 1),
						'bbcode_bitfield'			=> array('VCHAR', ''),
						'bbcode_uid'				=> array('VCHAR:8', ''),
						'article_last_edit_time'	=> array('TIMESTAMP', 0),
						'article_last_edit_id'		=> array('UINT', 0),
					),
					'PRIMARY_KEY'	=> 'article_id'
				)),

				array('phpbb_article_attachments', array(
					'COLUMNS'		=> array(
						'attach_id'			=> array('UINT', NULL, 'auto_increment'),
						'article_id'		=> array('UINT', 0),
						'comment_id'		=> array('UINT', 0),
						'poster_id'			=> array('UINT', 0),
						'is_orphan'			=> array('BOOL', 1),
						'physical_filename'	=> array('VCHAR', ''),
						'real_filename'		=> array('VCHAR', ''),
						'download_count'	=> array('UINT', 0),
						'attach_comment'	=> array('TEXT_UNI', ''),
						'extension'			=> array('VCHAR:100', ''),
						'mimetype'			=> array('VCHAR:100', ''),
						'filesize'			=> array('UINT:20', 0),
						'filetime'			=> array('TIMESTAMP', 0),
						'thumbnail'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'attach_id',
					'KEYS'			=> array(
						'filetime'			=> array('INDEX', 'filetime'),
						'article_id'		=> array('INDEX', 'article_id'),
						'comment_id'		=> array('INDEX', 'comment_id'),
						'poster_id'			=> array('INDEX', 'poster_id'),
						'is_orphan'			=> array('INDEX', 'is_orphan'),
					),
				)),

				array('phpbb_article_cats', array(
					'COLUMNS'		=> array(
						'cat_id'				=> array('UINT', NULL, 'auto_increment'),
						'parent_id'				=> array('UINT', 0),
						'left_id'				=> array('UINT', 0),
						'right_id'				=> array('UINT', 0),
						'cat_name'				=> array('VCHAR', ''),	
						'cat_desc'				=> array('TEXT_UNI', ''),
						'cat_desc_bitfield'		=> array('VCHAR', ''),
						'cat_desc_options'		=> array('UINT:11', 7),
						'cat_desc_uid'			=> array('VCHAR:8', ''),
						'cat_image'				=> array('VCHAR', ''),
						'cat_articles'			=> array('UINT', 0),
					),
					'PRIMARY_KEY'	=> 'cat_id',
					'KEYS'			=> array(
						'cat_name'			=> array('INDEX', 'cat_name'),
						'cat_desc'			=> array('INDEX', 'cat_desc'),
					),
				)),

				array('phpbb_article_comments', array(
					'COLUMNS'		=> array(
						'comment_id'			=> array('UINT', NULL, 'auto_increment'),
						'article_id'			=> array('UINT', 0),
						'comment_title'			=> array('VCHAR', ''),
						'comment_text'			=> array('MTEXT_UNI', ''),
						'comment_checksum'		=> array('VCHAR:32', ''),
						'comment_type'			=> array('TINT:1', 0),
						'comment_user_id'		=> array('UINT', 0),
						'comment_user_name'		=> array('VCHAR_UNI:255', ''),
						'comment_user_color'	=> array('VCHAR:6', ''),
						'comment_time'			=> array('TIMESTAMP', 0),
						'comment_edit_time'		=> array('TIMESTAMP', 0),
						'comment_edit_id'		=> array('UINT', 0),
						'comment_edit_name'		=> array('VCHAR_UNI:255', ''),
						'comment_edit_color'	=> array('VCHAR:6', ''),
						'enable_bbcode'			=> array('BOOL', 1),
						'enable_smilies'		=> array('BOOL', 1),
						'enable_magic_url'		=> array('BOOL', 1),
						'enable_sig'			=> array('BOOL', 1),
						'bbcode_bitfield'		=> array('VCHAR', ''),
						'bbcode_uid'			=> array('VCHAR:8', ''),
						'comment_attachment'	=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'comment_id'
				)),

				array('phpbb_article_edits', array(
					'COLUMNS'		=> array(
						'edit_id'						=> array('UINT', NULL, 'auto_increment'),
						'article_id'					=> array('UINT', 0),
						'parent_id'						=> array('UINT', 0),
						'edit_user_id'					=> array('UINT', 0),
						'edit_user_name'				=> array('VCHAR_UNI:255', ''),
						'edit_user_color'				=> array('VCHAR:6', ''),
						'edit_time'						=> array('TIMESTAMP', 0),
						'edit_article_title'			=> array('VCHAR', ''),
						'edit_article_desc'				=> array('TEXT_UNI', ''),
						'edit_article_desc_bitfield'	=> array('VCHAR', ''),
						'edit_article_desc_options'		=> array('UINT:11', 7),
						'edit_article_desc_uid'			=> array('VCHAR:8', ''),
						'edit_article_text'				=> array('MTEXT_UNI', ''),
						'edit_article_checksum'			=> array('VCHAR:32', ''),
						'edit_enable_bbcode'			=> array('BOOL', 1),
						'edit_enable_smilies'			=> array('BOOL', 1),
						'edit_enable_magic_url'			=> array('BOOL', 1),
						'edit_enable_sig'				=> array('BOOL', 1),
						'edit_bbcode_bitfield'			=> array('VCHAR', ''),
						'edit_bbcode_uid'				=> array('VCHAR:8', ''),
						'edit_article_status'			=> array('TINT:1', 1),
						'comment_id'					=> array('UINT', 0),
						'edit_moderated'				=> array('TINT:1', 0),
					),
					'PRIMARY_KEY'	=> 'edit_id'
				)),
				
				array('phpbb_article_rate', array(
					'COLUMNS'		=> array(
						'article_id'	=> array('UINT', 0),
						'user_id'		=> array('UINT', 0),
						'rate_time'		=> array('TIMESTAMP', 0),
						'rating'		=> array('TINT:2', 0),
					),
				)),
				
				array('phpbb_article_tags', array(
					'COLUMNS'		=> array(
						'article_id'	=> array('UINT', 0),
						'tag_name'		=> array('VCHAR:30', ''),
						'tag_name_lc'	=> array('VCHAR:30', ''),
					),
				)),
				
				array('phpbb_article_track', array(
					'COLUMNS'		=> array(
						'article_id'	=> array('UINT', 0),
						'user_id'		=> array('UINT', 0),
						'subscribed'	=> array('TINT:1', 0),
						'bookmarked'	=> array('TINT:1', 0),
						'notify_by'		=> array('TINT:1', 0),
						'notify_on'		=> array('TINT:1', 0),
					),
				)),
			),
		),
		
		'0.0.2'		=> array(
			'permission_add'	=> array(
				array('u_kb_read', true),
				array('u_kb_download', true),
				array('u_kb_comment', true),
				array('u_kb_add', true),
				array('u_kb_delete', true),
				array('u_kb_edit', true),
				array('u_kb_bbcode', true),
				array('u_kb_flash', true),
				array('u_kb_smilies', true),
				array('u_kb_img', true),
				array('u_kb_sigs', true),
				array('u_kb_attach', true),
				array('u_kb_icons', true),
				array('u_kb_rate', true),
				array('m_kb', true),
			),
		),
		
		'0.0.3'		=> array(
			'config_add'	=> array(
				array('kb_allow_attachments', 1),
				array('kb_allow_sig', 1),
				array('kb_allow_smilies', 1),
				array('kb_allow_bbcode', 1),
				array('kb_allow_post_flash', 1),
				array('kb_allow_post_links', 1),
				array('kb_enable', 1),
				array('kb_articles_per_page', 25),
				array('kb_comments_per_page', 25),
				array('kb_allow_subscribe', 1),
				array('kb_allow_bookmarks', 1),
			),
		),
		
		'0.0.4' => array(
			// Alright, now lets add some modules to the ACP
			'module_add' => array(
				// First, lets add a new category
				array('acp', 'ACP_CAT_DOT_MODS', 'ACP_KB'),

				// Now we will add the settings and features modes.
				array('acp', 'ACP_KB', array(
						'module_basename'		=> 'kb',
						'modes'					=> array('settings'),
					),
				),
			),
		),

		'0.0.5' => array(
			'config_add'	=> array(
				array('kb_last_article', 0, true),
				array('kb_last_updated', time(), true),
				array('kb_total_articles', 0, true),
				array('kb_total_comments', 0, true),
				array('kb_total_cats', 0),
			),
			
			'module_add' => array(
				array('ucp', '', 'UCP_KB'),
				
				array('ucp', 'UCP_KB', array(
						'module_basename'			=> 'kb',
						'modes'						=> array('front', 'subscribed', 'bookmarks', 'articles'),
					),
				),
			),
		),
		
		'0.0.6' => array(
			// Alright, now lets add some modules to the ACP
			'module_add' => array(
				array('acp', 'ACP_KB', array(
						'module_basename'		=> 'kb',
						'modes'					=> array('health_check'),
					),
				),
			),
		),
		
		'0.0.7' => array(
			// Alright, now lets add some modules to the ACP
			'module_add' => array(
				array('acp', 'ACP_KB', array(
						'module_basename'		=> 'kb_cats',
						'modes'					=> array('manage'),
					),
				),
			),
		),
		
		'0.0.8' => array(
			'table_column_remove' => array(
				array(KB_EDITS_TABLE, 'comment_id'), // Unused column
			),
			'table_column_add' => array(
				array(KB_TABLE, 'article_edit_reason', array('MTEXT_UNI', '')),
				array(KB_TABLE, 'article_edit_reason_global', array('BOOL', 0)),
				array(KB_EDITS_TABLE, 'edit_reason', array('MTEXT_UNI', '')),
				array(KB_EDITS_TABLE, 'edit_reason_global', array('BOOL', 0)),
			),
			
			'module_add' => array(
				array('mcp', '', 'MCP_KB'),
				array('mcp', 'MCP_KB', array(
						'module_basename'	=> 'kb',
						'modes'				=> array('queue', 'articles'),
					),
				),
			),
		),
		
		'0.0.9' => array(
			'table_column_add' => array(
				array(KB_CATS_TABLE, 'latest_ids', array('TEXT_UNI', NULL)),
				array(KB_CATS_TABLE, 'latest_titles', array('TEXT_UNI', NULL)),
			),
		),
		
		'0.0.10' => array(
			'table_column_add' => array(
				array(EXTENSION_GROUPS_TABLE, 'allow_in_kb', array('BOOL', 0)),
			),
		),
		
		'0.0.11' => array(
			'table_column_add' => array(
				// Adding some new DB tables to lessen db queries
				array(USERS_TABLE, 'user_articles', array('UINT', 0)),
				array(KB_TABLE, 'article_votes', array('UINT', 0)),
			),
			// New permission to add articles without approval
			'permission_add' => array(
				array('u_kb_add_wa', true), // Add articles without approval
				array('u_kb_viewhistory', true), // View history of articles
			),
		),
		
		'0.0.12' => array(
			'custom'	=> 'kb_update_0_0_11_to_0_0_12',
						  
			'table_column_remove' => array(
				array(KB_CATS_TABLE, 'latest_titles'), // Now unused
				array(KB_TABLE, 'article_icon'),
			),
			
			'table_column_add' => array(
				array(KB_TABLE, 'article_type', array('UINT', 0)),
			),
			
			// Add permissions for types as well as requests
			'permission_add'	=> array(
				array('u_kb_request', true),
				array('u_kb_types', true),
			),
			
			// Add 2 new tables for requests and article types
			'table_add'	=> array(
				array('phpbb_article_requests', array(
					'COLUMNS'		=> array(
						'request_id'				=> array('UINT', NULL, 'auto_increment'),
						'article_id'				=> array('UINT', 0),
						'request_accepted'			=> array('UINT', 0),
						'request_title'				=> array('VCHAR', ''),
						'request_text'				=> array('MTEXT_UNI', ''),
						'request_checksum'			=> array('VCHAR:32', ''),
						'request_status'			=> array('TINT:1', 0),
						'request_user_id'			=> array('UINT', 0),
						'request_user_name'			=> array('VCHAR_UNI:255', ''),
						'request_user_color'		=> array('VCHAR:6', ''),
						'request_time'				=> array('TIMESTAMP', 0),
						'bbcode_bitfield'			=> array('VCHAR', ''),
						'bbcode_uid'				=> array('VCHAR:8', ''),
					),
					'PRIMARY_KEY'	=> 'request_id'
				)),
				
				array('phpbb_article_types', array(
					'COLUMNS'		=> array(
						'type_id'					=> array('UINT', NULL, 'auto_increment'),
						'icon_id'					=> array('UINT', 0),
						'type_title'				=> array('VCHAR', ''),
						'type_before'				=> array('VCHAR', ''),
						'type_after'				=> array('VCHAR', ''),
						'type_image'				=> array('VCHAR', ''),
						'type_img_w'				=> array('TINT:4', 0),
						'type_img_h'				=> array('TINT:4', 0),
						'type_order'				=> array('TINT:4', 0),
					),
					'PRIMARY_KEY'	=> 'type_id'
				)),
			),
			
			'module_add' => array(
				array('acp', 'ACP_KB', array(
						'module_basename'		=> 'kb_types',
						'modes'					=> array('manage'),
					),
				),
			),
		),
		
		// Updating to 0.1.0 with no changes since 0.0.12
		'0.1.0'	=> array(
			array(),
		),

		'0.1.1'	=> array(
			'module_add' => array(
				array('acp', 'ACP_KB', array(
						'module_basename'		=> 'kb',
						'modes'					=> array('plugins'),
					),
				),
			),
			
			'config_add'	=> array(
				array('kb_latest_article_enable', 1),
				array('kb_latest_article_menu', LEFT_MENU),
			),
		),			
		
		// Adding localized permissions, therefore 0.2.0 as it is major change
		'0.2.0' => array(
			'table_column_add'	=> array(
				array(ACL_USERS_TABLE, 'kb_auth', array('BOOL', 0)),
				array(ACL_GROUPS_TABLE, 'kb_auth', array('BOOL', 0)),
			),
			
			// Add ACP modules
			'module_add' => array(
				array('acp', 'ACP_KB', array(
						'module_basename'		=> 'kb_permissions',
						'modes'					=> array('set_permissions', 'set_roles'),
					),
				),
			),
			
			// Delete permissions and add localized ones later
			'permission_remove'	=> array(
				array('u_kb_read', true),
				array('u_kb_add', true),
				array('u_kb_add_wa', true),
				array('u_kb_attach', true),
				array('u_kb_bbcode', true),
				array('u_kb_comment', true),
				array('u_kb_delete', true),
				array('u_kb_download', true),
				array('u_kb_edit', true),
				array('u_kb_flash', true),
				array('u_kb_icons', true),
				array('u_kb_img', true),
				array('u_kb_rate', true),
				array('u_kb_sigs', true),
				array('u_kb_smilies', true),
				array('u_kb_types', true),
			),
			
			// Now add them as local permissions
			'permission_add'	=> array(
				array('u_kb_read', false),
				array('u_kb_add', false),
				array('u_kb_add_wa', false),
				array('u_kb_attach', false),
				array('u_kb_bbcode', false),
				array('u_kb_comment', false),
				array('u_kb_delete', false),
				array('u_kb_download', false),
				array('u_kb_edit', false),
				array('u_kb_flash', false),
				array('u_kb_img', false),
				array('u_kb_rate', false),
				array('u_kb_sigs', false),
				array('u_kb_smilies', false),
				array('u_kb_types', false),
				array('u_kb_search', false),
			),
		),
	
		'0.2.1'	=> array(
			'table_add'	=> array(
				array('phpbb_article_plugins', array(
					'COLUMNS'		=> array(
						'plugin_id'					=> array('UINT', NULL, 'auto_increment'),
						'plugin_name'				=> array('VCHAR', ''),
						'plugin_filename'			=> array('VCHAR', ''),
						'plugin_desc'				=> array('TEXT_UNI', ''),
						'plugin_copy'				=> array('VCHAR', ''),
						'plugin_version'			=> array('VCHAR:20', ''),
						'plugin_menu'				=> array('BOOL', NO_MENU),
						'plugin_order'				=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'plugin_id'
				)),
			),			
			
			'config_remove'	=> array(
				array('kb_latest_article_enable'),
				array('kb_latest_article_menu'),
				array('kb_last_article'),
			),
		),	
		
		'0.2.2' => array(
			'table_column_add'	=> array(
				array(KB_PLUGIN_TABLE, 'plugin_pages', array('TEXT_UNI', 'a:0:{}'))
			),
		),
		
		'0.2.3' => array(
			'table_column_add'	=> array(
				array(KB_PLUGIN_TABLE, 'plugin_pages_perm', array('TEXT_UNI', 'a:0:{}'))
			),
		),
		
		'0.2.4' => array(
			'table_column_add'	=> array(
				array(KB_PLUGIN_TABLE, 'plugin_perm', array('BOOL', 0))
			),
			
			'custom'	=> 'kb_install_perm_plugins',
		),
		
		'0.2.5' => array(
			'custom'	=> 'kb_install_perm_plugins',
		),
		
		'0.2.6' => array(
			'custom'	=> 'kb_install_perm_plugins',
		),
		
		'0.3.0' => array(
			// New release ;)
		),
		
		'0.3.1' => array(
			// Code change ;)
			'custom'	=> 'kb_delete_permission_roles',
		),
		
		'0.3.2' => array(
			// making search better, I hope
			'table_column_add' => array(
				array(KB_TABLE, 'article_title_clean', array('VCHAR', '')),
			),
			'custom' => 'kb_update_031_to_032',
		),
		
		'0.3.3' => array(
			// New acp settings & improved the somewhat confusing permission system a bit
			'config_add' => array(
				array('kb_desc_min_chars', 0),
				array('kb_desc_max_chars', 0),
			),
			
			'permission_remove' => array(
				array('u_kb_viewhistory', true),
			),
			
			'permission_add' => array(
				array('u_kb_viewhistory', false),
				array('u_kb_view', false),
			),
		),
	);

	return $versions;
}

//
// ALL FUNCTIONS BELOW THIS LINE ARE CUSTOM UPDATE FUNCTIONS
// (expect long and annoying function names)
//
function kb_delete_permission_roles($action , $version)
{
	global $phpbb_root_path, $db;

	if ($action == 'uninstall')
	{
		$sql = 'DELETE FROM ' . ACL_ROLES_TABLE . " 
			WHERE role_type = 'u_kb_'";
		$db->sql_query($sql);
	}
	else
	{
		return;
	}
}

function kb_install_perm_plugins($action , $version)
{
	global $phpbb_root_path;

	if ($action == 'uninstall')
	{
		return;
	}
	
	if(!defined('IN_KB_PLUGIN')) // Killing notice when updating through several versions all using this function
	{
		define('IN_KB_PLUGIN', true);
	}
	
	switch ($version)
	{
		case '0.2.4':
			$plugins = array('search');
		break;
		
		case '0.2.5':
			$plugins = array('stats');
		break;
		
		case '0.2.6':
			$plugins = array('request_list');
		break;
	}
	
	if (empty($plugins))
	{
		return;
	}
	
	foreach ($plugins as $plugin)
	{
		install_plugin($plugin, $phpbb_root_path . 'includes/kb_plugins/');
	}
}

function kb_update_0_0_11_to_0_0_12($action, $version)
{
	global $db, $table_prefix;
	
	// Only run function when updating
	if($action != 'update')
	{
		return;
	}
	
	$combination = 'kbdev69htygfhdslo908';
	$sql = 'SELECT cat_id, latest_ids, latest_titles 
			FROM ' . $table_prefix . 'article_cats
			ORDER BY cat_id DESC';
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
	{
		$new_data = array();
		if($row['latest_ids'] == '' || $row['latest_titles'] == '')
		{
			// Empty, continue
			continue;
		}
		
		$latest_ids = explode($combination, $row['latest_ids']);
		$latest_titles = explode($combination, $row['latest_titles']);
		
		for($i = 0; $i < count($latest_ids); $i++)
		{
			if(isset($latest_ids[$i]) && isset($latest_titles[$i]))
			{
				$new_data[] = array(
					'article_id'	=> $latest_ids[$i],
					'article_title'	=> $latest_titles[$i],
				);
			}
		}
		
		$sql = 'UPDATE ' . $table_prefix . "article_cats
				SET latest_ids = '" . serialize($new_data) . "'
				WHERE cat_id = '" . $row['cat_id'] . "'";
		$db->sql_query($sql);
	}
	$db->sql_freeresult($result);
}

/*
Write article titles into the new clean tag
*/
function kb_update_031_to_032($action, $version)
{
	global $db, $table_prefix;
	
	// Only run function when updating
	if($action != 'update')
	{
		return;
	}
	
	$sql = "SELECT article_id, article_title
			FROM " . $table_prefix . "articles";
	$result = $db->sql_query($sql);
	
	$articles = array();
	while($row = $db->sql_fetchrow($result))
	{
		$articles[$row['article_id']] = utf8_clean_string($row['article_title']);
	}
	$db->sql_freeresult($result);
	
	foreach($articles as $article_id => $clean_title)
	{
		$sql = 'UPDATE ' . $table_prefix . 'articles  
				SET article_title_clean = "' . $clean_title . '"
				WHERE article_id = ' . $article_id;
		$db->sql_query($sql);
	}

}
?>