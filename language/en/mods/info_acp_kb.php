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
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_KB'						=> 'Knowledge Base Configuration',
	'ACP_KB_HEALTH_CHECK'			=> 'Health Check',
	'ACP_KB_HEALTH_CHECK_EXPLAIN'	=> 'This is where you can check that the knowledge base is up to date as well as run check to make sure it is running properly. You can also uninstall the Knowledge Base from here.',
	'ACP_KB_MANAGEMENT'				=> 'Knowledge Base Management',
	'ACP_KB_ARTICLE_TYPES'			=> 'Article Types Management',
	'ACP_KB_SETTINGS'				=> 'Settings',
	'ACP_KB_SETTINGS_EXPLAIN'		=> 'Customise your knowledge base settings here',
	'ACP_MANAGE_CATS'				=> 'Manage Categories',
	'ACP_MANAGE_KB_TYPES'			=> 'Manage Article Types',

	'LOG_CAT_ADD'					=> '<strong>Category added</strong><br /> - %1$s',
	'LOG_CAT_EDIT'					=> '<strong>Category edited</strong><br /> - %1$s',
	'LOG_CAT_MOVE_DOWN'				=> '<strong>Category %1$s moved down below</strong> <br /> - %2$s',
	'LOG_CAT_MOVE_UP'				=> '<strong>Category %1$s moved up above</strong> <br /> - %2$s',
	'LOG_TYPE_ADD'					=> '<strong>Article type added</strong><br /> - %1$s ',
	'LOG_TYPE_EDIT'					=> '<strong>Article type edited</strong><br /> - %1$s ',
	'LOG_TYPE_MOVE_DOWN'			=> '<strong>Article type %1$s moved down</strong>',
	'LOG_TYPE_MOVE_UP'				=> '<strong>Article type %1$s moved up </strong>',
	'LOG_TYPE_DELETE'				=> '<strong>Articel type deleted </strong>',
));

?>