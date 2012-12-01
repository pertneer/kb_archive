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
	$acp_options['legend1'] 					= 'REQUEST_ARTICLES';
	$acp_options['kb_request_list_enable'] 		= array('lang' => 'ENABLE_REQUEST_ARTICLES',		'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' 	=> false);
	$acp_options['kb_request_list_menu']		= array('lang' => 'WHICH_MENU',						'validate' => 'int',	'type' => 'custom', 		'function' 	=> 'select_menu_check', 	'explain' 	=> false);
	$acp_options['kb_request_list_limit']		= array('lang' => 'REQUEST_ARTICLES_LIMIT',			'validate' => 'int',	'type' => 'text:5:2',		'explain'	=> false);
	
	$details = array(
		'PLUGIN_NAME'			=> 'Request Articles',
		'PLUGIN_DESC'			=> 'Allow users to request articles they want to see in the knowledge base',
		'PLUGIN_COPY'			=> '&copy; 2009 Andreas Nexmann, Tom Martin',
		'PLUGIN_VERSION'		=> '1.0.0',
		'PLUGIN_MENU'			=> RIGHT_MENU,
		'PLUGIN_PERM'			=> true,
		'PLUGIN_PAGES'			=> array('all'),
	);
}

// Get most popular articles, based on views
function request_list($cat_id)
{
	global $db, $template, $phpbb_root_path, $phpEx, $config;
	
	if (!$config['kb_request_list_enable'])
	{
		return;
	}
	
	show_request_list(true, $config['kb_request_list_limit']);
	
	$content = kb_parse_template('request_list', 'request_list.html');
	
	return $content;
}

function request_list_versions()
{
	$versions = array(
		'1.0.0'	=> array(			
			// Initial install, I suppose nothing is done here beside adding the config
			'config_add'	=> array(
				array('kb_request_list_enable', 1),
				array('kb_request_list_menu', RIGHT_MENU),
				array('kb_request_list_limit', 5),
			),
		),
	);

	return $versions;
}

?>