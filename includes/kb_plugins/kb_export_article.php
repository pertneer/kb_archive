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
	$acp_options['legend1'] 			= 'EXPORT_ARTICLE';
	$acp_options['kb_export_article']	= array('lang' => 'KB_EXP_ARTICLE',	'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' => false);
	$acp_options['kb_export_menu']		= array('lang' => 'WHICH_MENU',			'validate' => 'int',	'type' => 'custom', 		'function' 	=> 'select_menu_check', 	'explain' 	=> false);
		
	$details = array(
		'PLUGIN_NAME'			=> 'Export options on view article page',
		'PLUGIN_DESC'			=> 'Adds an export options box on the view article page',
		'PLUGIN_COPY'			=> '&copy; 2009 Andreas Nexmann, Tom Martin',
		'PLUGIN_VERSION'		=> '1.0.0',
		'PLUGIN_MENU'			=> RIGHT_MENU,
		'PLUGIN_PERM'			=> true,
		'PLUGIN_PAGES'			=> array('view_article'),
	);
}

// Get latest article
function export_article()
{
	global $template;
	
	$content = kb_parse_template('export_article', 'export_article.html');
	
	return $content;
}

function export_article_versions()
{
	$versions = array(
		'1.0.0'	=> array(			
			'config_add'	=> array(
				array('kb_export_menu', RIGHT_MENU),
			),
		),
	);

	return $versions;
}
?>