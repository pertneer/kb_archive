<?php
/**
*
* @package acp
* @version $Id: acp_forums.php 8479 2008-03-29 00:22:48Z naderman $
* @copyright (c) 2005 phpBB Group
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