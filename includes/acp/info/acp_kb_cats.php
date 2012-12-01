<?php
/**
*
* @package phpBB Knowledge Base Mod (KB)
* @version $Id: acp_kb_cats.php 342 2009-10-28 14:05:22Z tom.martin60@btinternet.com $
* @copyright (c) 2009 Andreas Nexmann, Tom Martin
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_kb_cats_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_kb_cats',
			'title'		=> 'ACP_KB_MANAGEMENT',
			'version'	=> '0.0.1',
			'modes'		=> array(
				'manage'	=> array('title' => 'ACP_MANAGE_CATS', 'auth' => 'acl_a_board', 'cat' => array('ACP_KB')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>