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

// Only add these options if in acp
if (defined('IN_KB_PLUGIN'))
{
	$acp_options['legend1'] 				= 'KB_CATS';
	$acp_options['kb_categories_enable'] 	= array('lang' => 'ENABLE_CATS',		'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' 	=> false);
	$acp_options['kb_categories_menu']		= array('lang' => 'WHICH_MENU',			'validate' => 'int',	'type' => 'custom', 		'function' 	=> 'select_menu_check', 	'explain' 	=> false);
		
	$details = array(
		'PLUGIN_NAME'			=> 'List Categories',
		'PLUGIN_DESC'			=> 'Adds a categories list to your menu for easy navigation',
		'PLUGIN_COPY'			=> '&copy; 2009 Andreas Nexmann, Tom Martin',
		'PLUGIN_VERSION'		=> '1.0.0',
		'PLUGIN_MENU'			=> LEFT_MENU,
	);
}

/**
* Show cats here
* Moved from main kb.php
* Might pass cat_id later so selected cat is in bold
*/
function categories($cat_id = 0)
{
	global $template, $phpbb_root_path, $phpEx, $config, $auth;
	
	if (!$config['kb_categories_enable'])
	{
		return;
	}
	
	$cats = make_cat_select($cat_id, false, false, true);
	foreach ($cats as $cat)
	{
		$template->assign_block_vars('cat_list', array(
			'CAT_SEL'				=> $cat['selected'],
			'CAT_NAME'				=> $cat['padding'] . $cat['cat_name'],
			'U_VIEW_CAT'			=> kb_append_sid("{$phpbb_root_path}kb.$phpEx", "c=" . $cat['cat_id']),
		));
	}
	unset($cats);
	
	$content = kb_parse_template('categories', 'categories.html');
	
	unset($template->_tpldata['cat_list']);
	
	return $content;
}

function categories_versions()
{
	$versions = array(
		'1.0.0'	=> array(			
			'config_add'	=> array(
				array('kb_categories_enable', 1),
				array('kb_categories_menu', LEFT_MENU),
			),
		),
	);

	return $versions;
}
?>