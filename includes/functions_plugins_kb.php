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

/**
*
* DOCUMENTATION OF KB PLUGIN SYSTEM
*
**
* FUNCTION NAMING
* Filename after kb_ has to be the function name that it calls
* E.G. kb_latest_article will call the function latest_article()
* For versions please use '_versions' after function e.g. latest_article_versions
* All other function names are up to you, but it is prefered that you keep FILENAME_*.* e.g. latest_article_*.*
*
**
* BASIC CONFIG
* Two config have to be set
* 1. kb_FILENAME_enable e.g. kb_latest_article_enable
* 2. kb_FILENAME_menu e.g. kb_latest_article_menu
* 
**
* ACP OPTIONS
* At the top of the plugin file you must have the following
* 
// Only add these options if in acp
if (defined('IN_KB_PLUGIN'))
{
	$acp_options['legend1'] 					= 'LATEST_ARTICLES';
	$acp_options['kb_latest_article_enable'] 	= array('lang' => 'ENABLE_LATEST_ARTICLES',		'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' 	=> false);
	$acp_options['kb_latest_article_menu']		= array('lang' => 'WHICH_MENU',					'validate' => 'int',	'type' => 'custom', 		'function' 	=> 'select_menu_check', 	'explain' 	=> false);
		
	$details = array(
		'PLUGIN_NAME'			=> 'Latest Article',
		'PLUGIN_DESC'			=> 'Adds a latest article to your knowledge base',
		'PLUGIN_COPY'			=> '&copy; 2009 Andreas Nexmann, Tom Martin',
		'PLUGIN_VERSION'		=> '1.0.2',
		'PLUGIN_MENU'			=> RIGHT_MENU,
		'PLUGIN_PAGE_PERM'		=> array('add'), // Optional, this is only used if your plugin has to be called on certain pages to make it work. If it isn't required please remove the line
	);
}
* Add as many $acp_options as you like, it uses build_cfg_template function located in phpbb_root_path/adm/index.php you should be able to work most of the stuff out from there
*
**
* INSTALLING/UPDATING/UNINSTALLING
* You must use UMIL to do all these functions in the kb plugin system as makes integration much easier
* Documentation on UMIL can be found in this zip located at the phpbb svn - http://code.phpbb.com/attachments/download/19/UMIL_Documentation.zip Or here http://wiki.phpbb.com/Umil
* Below an example function for UMIL installing, including the basic configs you need
function latest_article_versions()
{
	$versions = array(
		'1.0.0'	=> array(			
			'config_add'	=> array(
				array('kb_latest_article_enable', 1),
				array('kb_latest_article_menu', RIGHT_MENU),
				array('kb_latest_article', 0, true),
			),
		),
		
		// Just adding perm pages
		'1.0.1'	=> array(			
			array(),
		),
		
		// Just a change to perm pages
		'1.0.2'	=> array(			
			array(),
		),
	);

	return $versions;
}
*
*/

/**
* This file holds functions to-do with the plug-in system
*/

/**
* This creates the left right and center menu all order can be changed in acp
* Can feed into this as going to be stored in a database
* Loads of work needs doing here as you will be able to change where the article is placed etc
* $params is going to be what page we are on as you can set what displays for what page
*/
function generate_menu($page = 'index', $cat_id = 0)
{
	global $template, $phpbb_root_path, $phpEx, $config;
	
	// Article injection variables
	global $on_article_post, $on_article_del, $on_article_edit;
	
	$plugin_loc = $phpbb_root_path . 'includes/kb_plugins/';
	
	if ($page == '')
	{
		$page = 'index';
	}
	
	if ($page == 'edit')
	{
		$page = 'add';
	}
	
	if ($page == 'delete')
	{
		$page = 'add';
	}
	
	$left_menu = cached_plugins('left');
	foreach ($left_menu as $plugin)
	{
		if (!$config['kb_' . $plugin['FILE'] . '_enable'])
		{
			continue;
		}
	
		$show_pages = unserialize($plugin['PERM']);
		if (in_array($page, $show_pages))
		{	
			include($plugin_loc . 'kb_' . $plugin['FILE'] . '.' . $phpEx);
			
			$template->assign_block_vars('left_menu', array(
				'CONTENT'		=> $plugin['FILE']($cat_id),
			));
		}
	}
	
	$center_menu = cached_plugins('center');
	foreach ($center_menu as $plugin)
	{
		if (!$config['kb_' . $plugin['FILE'] . '_enable'])
		{
			continue;
		}
	
		$show_pages = unserialize($plugin['PERM']);
		if (in_array($page, $show_pages))
		{	
			include($plugin_loc . 'kb_' . $plugin['FILE'] . '.' . $phpEx);
			
			$template->assign_block_vars('center_menu', array(
				'CONTENT'		=> $plugin['FILE']($cat_id),
			));
		}
	}
	
	$right_menu = cached_plugins('right');
	foreach ($right_menu as $plugin)
	{
		if (!$config['kb_' . $plugin['FILE'] . '_enable'])
		{
			continue;
		}
	
		$show_pages = unserialize($plugin['PERM']);
		if (in_array($page, $show_pages))
		{	
			include($plugin_loc . 'kb_' . $plugin['FILE'] . '.' . $phpEx);
			
			$template->assign_block_vars('right_menu', array(
				'CONTENT'		=> $plugin['FILE']($cat_id),
			));
		}
	}
}

// Not used going to be used to cache left right menus etc
function cached_plugins($mode)
{
	global $cache, $db, $phpbb_root_path, $phpEx, $table_prefix;
	
	$recache = false;
	
	switch ($mode)
	{
		case 'left':
			if (($left_menu = $cache->get('_kb_plugin_left_menu')) === false)
			{
				$recache = true;
			}
			else
			{
				return $left_menu;
			}
		break;
		
		case 'center':
			if (($center_menu = $cache->get('_kb_plugin_center_menu')) === false)
			{
				$recache = true;
			}
			else
			{
				return $center_menu;
			}
		break;
		
		case 'right':
			if (($right_menu = $cache->get('_kb_plugin_right_menu')) === false)
			{
				$recache = true;
			}
			else
			{
				return $right_menu;
			}
		break;
	}
	
	// Cache them all as they will all need doing within seconds anyway
	// Plus saves sql queries
	if ($recache)
	{
		// Must destroy them all as it will just add them otherwise
		$cache->destroy('_kb_plugin_left_menu');
		$cache->destroy('_kb_plugin_center_menu');
		$cache->destroy('_kb_plugin_right_menu');
	
		if (!defined('KB_PLUGIN_TABLE'))
		{
			include($phpbb_root_path . 'includes/constants_kb.' . $phpEx);
		}
		
		$sql = 'SELECT plugin_pages, plugin_filename, plugin_menu, plugin_order, plugin_pages_perm
			FROM ' . KB_PLUGIN_TABLE . ' 
			ORDER BY plugin_order ASC';
		$result = $db->sql_query($sql);		
		$rows = $db->sql_fetchrowset($result);
		
		$kb_left_plugins = array();
		$kb_center_plugins = array();
		$kb_right_plugins = array();
		
		foreach($rows as $row)
		{
			$file = $row['plugin_filename'];
			$view = $row['plugin_pages'];
			$perm = $row['plugin_pages_perm'];
			
			// Unserialize so can be merged
			$view = unserialize($view);
			$perm = unserialize($perm);
			
			// Merge arrays
			$merge = array_merge($view, $perm);
			
			// Make sure there are no duplicates
			$result = array_unique($merge);
			$result = serialize($result);
		
			if ($row['plugin_menu'] == LEFT_MENU)
			{
				$kb_left_plugins[] = array(
					'FILE'	=> $file,
					'PERM'	=> $result,
				);
			}
			
			if ($row['plugin_menu'] == CENTER_MENU)
			{
				$kb_center_plugins[] = array(
					'FILE'	=> $file,
					'PERM'	=> $result,
				);
			}
			
			if ($row['plugin_menu'] == RIGHT_MENU)
			{
				$kb_right_plugins[] = array(
					'FILE'	=> $file,
					'PERM'	=> $result,
				);
			}
		}
		
		$cache->put('_kb_plugin_left_menu', $kb_left_plugins);
		$cache->put('_kb_plugin_center_menu', $kb_center_plugins);
		$cache->put('_kb_plugin_right_menu', $kb_right_plugins);
		
		switch ($mode)
		{
			case 'left':
				return $kb_left_plugins;
			break;
			
			case 'center':
				return $kb_center_plugins;
			break;
			
			case 'right':
				return $kb_right_plugins;
			break;
		}
	}
}

/**
* This scans the directory for plugins
* Also stores all information needed
*/
function available_plugins()
{
	global $phpbb_root_path, $phpEx, $db;
	
	$plugin_loc = $phpbb_root_path . 'includes/kb_plugins/';
	
	$filenames = array();
	$dh = @opendir($plugin_loc);

	if ($dh)
	{
		while (($file = readdir($dh)) !== false)
		{
			if (strpos($file, 'kb_') === 0 && substr($file, -(strlen($phpEx) + 1)) === '.' . $phpEx)
			{
				$file_name = substr($file, 3, -(strlen($phpEx) + 1));

				$filenames[] = $file_name;			
			}
		}

		closedir($dh);
	}
	
	return $filenames;
}

/**
* Adds a plugin to the table
*/
function add_plugin($filename, $details)
{
	global $db, $cache;
	
	$data = array(
		'plugin_name'		=> $details['PLUGIN_NAME'],
		'plugin_filename'	=> $filename,
		'plugin_desc'		=> $details['PLUGIN_DESC'],
		'plugin_copy'		=> $details['PLUGIN_COPY'],
		'plugin_version'	=> $details['PLUGIN_VERSION'],
		'plugin_menu'		=> $details['PLUGIN_MENU'],
		'plugin_pages'		=> 'a:0:{}',
		'plugin_pages_perm'	=> (!empty($details['PLUGIN_PAGE_PERM'])) ? serialize($details['PLUGIN_PAGE_PERM']) : 'a:0:{}',
		'plugin_perm'		=> (empty($details['PLUGIN_PERM'])) ? false : $details['PLUGIN_PERM'],
	);
	
	$sql = 'INSERT INTO ' . KB_PLUGIN_TABLE . ' ' . $db->sql_build_array('INSERT', $data);
	$db->sql_query($sql);	
	
	sort_plugin_order('add', $details['PLUGIN_MENU'], $filename);
	
	$cache->destroy('_kb_plugin_left_menu');
	$cache->destroy('_kb_plugin_center_menu');
	$cache->destroy('_kb_plugin_right_menu');
}

/**
* Updates a plugin to the table
*/
function update_plugin_table($filename, $details)
{
	global $db, $cache;
	
	$data = array(
		'plugin_name'		=> $details['PLUGIN_NAME'],
		'plugin_desc'		=> $details['PLUGIN_DESC'],
		'plugin_copy'		=> $details['PLUGIN_COPY'],
		'plugin_version'	=> $details['PLUGIN_VERSION'],
		'plugin_pages_perm'	=> (!empty($details['PLUGIN_PAGE_PERM'])) ? serialize($details['PLUGIN_PAGE_PERM']) : 'a:0:{}',
	);
	
	$sql = 'UPDATE ' . KB_PLUGIN_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $data) . "
		WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
	$db->sql_query($sql);
	
	$cache->destroy('config');
	$cache->destroy('_kb_plugin_left_menu');
	$cache->destroy('_kb_plugin_center_menu');
	$cache->destroy('_kb_plugin_right_menu');
}

/**
* Delete the plugin
*/
function del_plugin($filename)
{
	global $db, $cache;
	
	sort_plugin_order('delete', '', $filename);
	
	$sql = 'DELETE FROM ' . KB_PLUGIN_TABLE . " 
		WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
	$db->sql_query($sql);
	
	$cache->destroy('config');
	$cache->destroy('_kb_plugin_left_menu');
	$cache->destroy('_kb_plugin_center_menu');
	$cache->destroy('_kb_plugin_right_menu');
}

/**
* Update plugin menu
*/
function update_plugin_menu($filename, $config_value)
{
	global $db, $cache;
	
	// Call this first so errors aren't happening
	$add = sort_plugin_order('move', $config_value, $filename);
	
	// Means plugin has moved sides
	if ($add)
	{	
		$sql = 'UPDATE ' . KB_PLUGIN_TABLE . " SET  
			plugin_menu = '"  . $db->sql_escape($config_value) . "'
			WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
		$db->sql_query($sql);	
		
		// Has been moved add it to bottom of order
		sort_plugin_order('add', $config_value, $filename);
		
		$cache->destroy('_kb_plugin_left_menu');
		$cache->destroy('_kb_plugin_center_menu');
		$cache->destroy('_kb_plugin_right_menu');
	}
}

/**
* Updates a plugin pages to the table
*/
function update_pages($filename, $pages)
{
	global $db, $cache;
	
	$data = array(
		'plugin_pages'		=> $pages,
	);
	
	$sql = 'UPDATE ' . KB_PLUGIN_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $data) . "
		WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
	$db->sql_query($sql);
	
	$cache->destroy('_kb_plugin_left_menu');
	$cache->destroy('_kb_plugin_center_menu');
	$cache->destroy('_kb_plugin_right_menu');
}

/**
* Install Plugin
*/
function install_plugin($filename, $plugin_loc, $u_action = false)
{
	global $phpbb_root_path, $phpEx, $config;
	
	if (!file_exists($plugin_loc . 'kb_' . $filename . '.' . $phpEx))
	{
		$link = ($u_action) ? adm_back_link($u_action) : false;
	
		trigger_error($user->lang['NO_PLUGIN_FILE'] . $link, E_USER_WARNING);
	}
	
	include($plugin_loc . 'kb_' . $filename . '.' . $phpEx);
	
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
	
	$function = $filename . '_versions';
	
	$versions = $function();

	$umil->run_actions('install', $versions, 'kb_' . $filename . '_version');
	unset($versions);		
							
	// Add to the plugins table
	add_plugin($filename, $details);	
	
	unset($details);
}

/**
* Update Plugin
*/
function update_plugin($filename, $plugin_loc, $u_action, $details)
{
	global $phpbb_root_path, $phpEx, $config;
	
	if (!defined('IN_KB_PLUGIN'))
	{
		if (!file_exists($plugin_loc . 'kb_' . $filename . '.' . $phpEx))
		{
			trigger_error($user->lang['NO_PLUGIN_FILE'] . adm_back_link($u_action), E_USER_ERROR);
		}
		
		include($plugin_loc . 'kb_' . $filename . '.' . $phpEx);
	}
	
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
	
	$function = $filename . '_versions';
	
	$versions = $function();

	$umil->run_actions('update', $versions, 'kb_' . $filename . '_version');
	unset($versions);	

	// Add to the plugins table
	update_plugin_table($filename, $details);	
	
	unset($details);
}

/**
* Uninstall Plugin
*/
function uninstall_plugin($filename, $plugin_loc, $u_action)
{
	global $phpbb_root_path, $phpEx, $config;
	
	if (!file_exists($phpbb_root_path . 'umil/umil.' . $phpEx))
	{
		trigger_error('KB_UPDATE_UMIL', E_USER_ERROR);
	}

	include($phpbb_root_path . 'umil/umil.' . $phpEx);
	$umil = new umil(true);

	$function = $filename . '_versions';
	
	$versions = $function();

	$umil->run_actions('uninstall', $versions, 'kb_' . $filename . '_version');
	unset($versions);
	
	// Delete the plugins from table
	del_plugin($filename);
	
	unset($details);
}

/**
* Sort plugin menu order
*/
function sort_plugin_order($mode, $menu, $filename, $action = 'move_up')
{
	global $db, $cache;
	
	switch ($mode)
	{
		case 'add':
			$sql = 'SELECT plugin_filename, plugin_menu, plugin_order
				FROM ' . KB_PLUGIN_TABLE . " 
				WHERE plugin_menu = '"  . $db->sql_escape($menu) . "'
					AND plugin_filename <> '" . $db->sql_escape($filename) . "'
				ORDER BY plugin_order DESC";
			$result = $db->sql_query($sql);
			
			if (!$db->sql_affectedrows())
			{
				$sql = 'UPDATE ' . KB_PLUGIN_TABLE . " SET 
					plugin_order = 1 
					WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
				$db->sql_query($sql);
			}
			else
			{	
				$menu_order = array();
			
				while ($row = $db->sql_fetchrow($result))
				{
					$menu_order[] = $row['plugin_order'];
				}		
				$order_num = $menu_order[0] + 1;
				
				$sql = 'UPDATE ' . KB_PLUGIN_TABLE . " SET  
					plugin_order = " . $order_num . " 
					WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
				$db->sql_query($sql);
			}	
			$db->sql_freeresult($result);
		break;
		
		case 'update':
			$sql = 'SELECT plugin_order, plugin_menu
				FROM ' . KB_PLUGIN_TABLE . " 
				WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		
			switch ($action)
			{
				case 'move_up':
					$prev_num = $row['plugin_order'] - 1;
					
					// Move higher one down
					$sql = 'UPDATE ' . KB_PLUGIN_TABLE . ' SET  
						plugin_order = plugin_order + 1 
						WHERE plugin_menu = ' . $row['plugin_menu'] . '
							AND plugin_order = ' . $prev_num;
					$db->sql_query($sql);
				
					// Move lower one up
					$sql = 'UPDATE ' . KB_PLUGIN_TABLE . " SET  
						plugin_order = plugin_order - 1 
						WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
					$db->sql_query($sql);
				break;
				
				case 'move_down':
					$next_num = $row['plugin_order'] + 1;
					
					// Move higher one up
					$sql = 'UPDATE ' . KB_PLUGIN_TABLE . ' SET  
						plugin_order = plugin_order - 1 
						WHERE plugin_menu = ' . $row['plugin_menu'] . '
							AND plugin_order = ' . $next_num;
					$db->sql_query($sql);
				
					// Move lower one down
					$sql = 'UPDATE ' . KB_PLUGIN_TABLE . " SET  
						plugin_order = plugin_order + 1 
						WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
					$db->sql_query($sql);
				break;
			}
		break;
		
		case 'delete':
		case 'move':
			// Get current position
			$sql = 'SELECT plugin_order, plugin_menu
				FROM ' . KB_PLUGIN_TABLE . " 
				WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			// Don't update if the same!
			if ($menu == $row['plugin_menu'] && $mode == 'move')
			{
				return false;
			}
			
			$sql = 'UPDATE ' . KB_PLUGIN_TABLE . ' SET  
				plugin_order = plugin_order - 1 
				WHERE plugin_menu = ' . $row['plugin_menu'] . '
					AND plugin_order > ' . $row['plugin_order'];
			$db->sql_query($sql);
			
			return true;
		break;
	}	

	$cache->destroy('_kb_plugin_left_menu');
	$cache->destroy('_kb_plugin_center_menu');
	$cache->destroy('_kb_plugin_right_menu');
}

/**
* Used for appending kb_sid
* Plugin going to be for seo mod
*/
function kb_append_sid($page, $params = '')
{
	return append_sid($page, $params);
}

// Makes a list of pages for acp select
function make_page_list($filename, $page_list = false)
{
	global $user, $db;
	
	$page_options = array(
		'index'			=> $user->lang['KB_INDEX'],
		'view_cat'		=> $user->lang['VIEW_CAT'],
		'request'		=> $user->lang['REQUEST'],
		'view_article'	=> $user->lang['VIEW_ARTICLE'],		
		'search'		=> $user->lang['SEARCH'],
		'history'		=> $user->lang['HISTORY'],
		'add'			=> $user->lang['POSTING'],
	);
	
	if (!$page_list)
	{
		$sql = 'SELECT plugin_pages
			FROM ' . KB_PLUGIN_TABLE . " 
			WHERE plugin_filename = '" . $db->sql_escape($filename) . "'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
		// Don't show perm pages as user may get confussed
		$show_pages = unserialize($row['plugin_pages']);	
	
		$options = '';
		foreach ($page_options as $mode => $lang)
		{
			$selected = (in_array($mode, $show_pages)) ? '" selected="selected"' : '';
			$options .= '<option value="' . $mode . '" ' . $selected . '>' . $lang . '</option>';
		}
	}
	else
	{
		$options = array();
		foreach ($page_options as $mode => $lang)
		{
			$options[] = $mode;
		}
	}
	
	return $options;
}

/**
* This function gets called when any posting happens
* $type = article - only called if approve or comment or request
* $mode = adding or edit or delete
* $data = data from that post
*
* Need to rethink this maybe add an array in files that it knows to call, hmm that would work
*/
function on_posting($type, $mode, $data = false)
{
	switch ($type)
	{
		case 'article':
			switch ($mode)
			{
				case 'add':
					global $on_article_post;
					
					if(sizeof($on_article_post))
					{
						foreach ($on_article_post as $null => $function)
						{
							$function($data);
						}
					}
				break;
				
				case 'edit':
					global $on_article_edit;
					
					if(sizeof($on_article_edit))
					{
						foreach ($on_article_edit as $null => $function)
						{
							$function($data);
						}
					}
				break;
				
				case 'delete':
					global $on_article_del;
					
					if(sizeof($on_article_del))
					{
						foreach ($on_article_del as $null => $function)
						{
							$function($data);
						}
					}
				break;
			}
		break;
		
		case 'comment':
			switch ($mode)
			{
				case 'add':
				
				break;
				
				case 'edit':
				
				break;
				
				case 'delete':
				
				break;
			}
		break;
		
		case 'request':
			switch ($mode)
			{
				case 'add':
				
				break;
				
				case 'edit':
				
				break;
				
				case 'delete':
				
				break;
			}
		break;
	}
}

/**
* Parse a template and return the parsed text
*
* This function should be used for ALL plugins that normally use $template->assign_display to output data with the plugin system.
*/
function kb_parse_template($filename, $template_file)
{
	global $phpbb_root_path, $template, $user;

	$tpl_path = $phpbb_root_path . 'styles/' . $user->theme['template_path'] . '/template/kb/plugins/';
	
	// If the template file does not exist
	if (!file_exists($tpl_path . $template_file))
	{
		$error = sprintf($user->lang['PLUGIN_TEMPLATE_MISSING'], $template_file);
	
		trigger_error($error);
	}
	
	$template_path = 'kb/plugins/';

	$template->set_filenames(array(
		$filename		=> $template_path . $template_file,
	));

	// return the output
	return $template->assign_display($filename);
}

?>