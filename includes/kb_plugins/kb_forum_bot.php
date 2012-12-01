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
	$acp_options['legend1'] 					= 'ARTICLE_POST_BOT';
	$acp_options['kb_forum_bot_enable'] 		= array('lang' => 'ENABLE_FORUM_BOT',			'validate' => 'bool',	'type' => 'radio:yes_no', 	'explain' 	=> false);
	$acp_options['kb_forum_bot_user'] 			= array('lang' => 'ARTICLE_POST_BOT_USER',		'validate' => 'int',	'type' => 'text:3:5', 		'explain' 	=> false);
	$acp_options['kb_forum_bot_forum_id'] 		= array('lang' => 'FORUM_ID',					'validate' => 'int',	'type' => 'text:3:5', 		'explain' 	=> false);
	$acp_options['kb_forum_bot_subject'] 		= array('lang' => 'ARTICLE_POST_BOT_SUB',		'validate' => 'string',	'type' => 'text:30:50', 	'explain' 	=> false);
	$acp_options['kb_forum_bot_message'] 		= array('lang' => 'ARTICLE_POST_BOT_MSG',		'validate' => 'string',	'type' => 'textarea:5:9', 	'explain' 	=> false);
		
	$details = array(
		'PLUGIN_NAME'			=> 'Knowledge Base Forum Bot',
		'PLUGIN_DESC'			=> 'Posts a new topic in a selected forum, when a article is added',
		'PLUGIN_COPY'			=> '&copy; 2009 Andreas Nexmann, Tom Martin',
		'PLUGIN_VERSION'		=> '1.0.0',
		'PLUGIN_MENU'			=> NO_MENU,
		'PLUGIN_PAGE_PERM'		=> array('add'),
		'PLUGIN_PAGES'			=> array('add'),
	);
}

/**
* Extra details for the mod does it need to call functions on certain areas?
*/
$on_article_post[] = 'post_new_article';
$on_article_approve[] = 'post_new_article';

// Doesn't need to pharse anything but we need it or errors will appear
function forum_bot($cat_id = 0)
{
	global $config;
	
	if (!$config['kb_forum_bot_enable'])
	{
		return;
	}
}

function forum_bot_versions()
{
	global $user;

	$versions = array(
		'0.0.1'	=> array(			
			'config_add'	=> array(
				array('kb_forum_bot_enable', 1),
			),
		),
		
		'0.0.2'	=> array(			
			'config_add'	=> array(
				array('kb_forum_bot_user', $user->data['user_id']),
				array('kb_forum_bot_message', $user->lang['ARTICLE_POST_BOT_MSG_EX']),
			),
		),
		
		'0.0.3'	=> array(			
			'config_add'	=> array(
				array('kb_forum_bot_subject', $user->lang['ARTICLE_POST_BOT_SUB_EX']),
			),
		),
		
		'0.0.4'	=> array(			
			'config_add'	=> array(
				array('kb_forum_bot_forum_id', ''),
			),
		),
		
		//Major release
		'1.0.0'	=> array(	
		),
	);

	return $versions;
}

function append_to_kb_options()
{
	global $user;

	$content = "<table cellspacing=\"1\">
		<caption>" . $user->lang['AVAILABLE'] . ' ' . $user->lang['VARIABLE'] . "</caption>
		<col class=\"col1\" /><col class=\"col2\" /><col class=\"col1\" />
			<thead>
				<tr>
					<th>" . $user->lang['NAME'] . "</th>
					<th>" . $user->lang['VARIABLE'] . "</th>
					<th>" . $user->lang['EXAMPLE'] . "</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td>" . $user->lang['ARTICLE_AUTHOR'] . "</td>
					<td><b>{AUTHOR}</b></td>
					<td><b>" . $user->data['username'] . "</b></td>
				</tr>
				<tr>
					<td>" . $user->lang['ARTICLE_TITLE'] . "</td>
					<td><b>{TITLE}</b></td>
					<td><b>" . $user->lang['ARTICLE_TITLE_EX'] . "</b></td>
				</tr>
				<tr>
					<td>" . $user->lang['ARTICLE_DESC'] . "</td>
					<td><b>{DESC}</b></td>
					<td><b>" . $user->lang['ARTICLE_DESC_EX'] . "</b></td>
				</tr>
				<tr>
					<td>" . $user->lang['ARTICLE_TIME'] . "</td>
					<td><b>{TIME}</b></td>
					<td><b>" . $user->format_date(time()) . "</b></td>
				</tr>
				<tr>
					<td>Article Link</td>
					<td><b>{LINK}</b></td>
					<td><b>" . generate_board_url() . "/kb.php?a=1</b></td>
				</tr>
			</tbody>
		</table>
	";

	return $content;
}

function change_auth($user_id, $mode = 'replace', $data = false)
{
	global $user, $auth, $db;
	
	switch($mode)
	{
		case 'replace':
			$data = array(
				'user_backup' => $user,
				'auth_backup' => $auth,
			);

			$sql = 'SELECT *
				FROM ' . USERS_TABLE . "
				WHERE user_id = '" . $db->sql_escape($user_id) . "'";
			$result	= $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$row['is_registered'] = true;
			
			$db->sql_freeresult($result);
			
			$user->data = array_merge($user->data, $row);
			$auth->acl($user->data);
			
			unset($row);
			
			return $data;
		break;
		
		case 'restore':				
			$user = $data['user_backup'];
			$auth = $data['auth_backup'];
			unset($data);
		break;
	}
}

function post_new_article($data)
{
	global $config, $user, $phpbb_root_path, $phpEx;

	if (!function_exists('submit_post'))
	{
		include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
	}
	
	if (($config['kb_forum_bot_forum_id'] == '' || $config['kb_forum_bot_forum_id'] == 0)
		|| ($config['kb_forum_bot_user'] == '' || $config['kb_forum_bot_user'] == 0)
		|| $config['kb_forum_bot_subject'] == '' 
		|| $config['kb_forum_bot_message'] == '')
	{
		return;
	}
	
	$vars = array(
		'{AUTHOR}'		=> $data['article_user_name'],
		'{TITLE}'		=> $data['article_title'],
		'{DESC}'		=> $data['article_desc'],
		'{TIME}'		=> $user->format_date($data['article_time']),
		'{LINK}'		=> '[url]' . generate_board_url() . "/kb.php?a=" . $data['article_id'] . '[/url]',
	);
	
	//Get post bots permissions
	$perms = change_auth($config['kb_forum_bot_user']);

	//Parse the text with the bbcode parser and write into $text
	$subject	= utf8_normalize_nfc($config['kb_forum_bot_subject']);
	$message	= utf8_normalize_nfc($config['kb_forum_bot_message']);
	
	$message = str_replace(array_keys($vars), array_values($vars), $message);
	$subject = str_replace(array_keys($vars), array_values($vars), $subject);
	
	// variables to hold the parameters for submit_post
	$poll = $uid = $bitfield = $options = ''; 
	generate_text_for_storage($subject, $uid, $bitfield, $options, false, false, false);
	generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

	$data = array( 
		'forum_id'			=> $config['kb_forum_bot_forum_id'],
		'icon_id'			=> false,

		'enable_bbcode'		=> true,
		'enable_smilies'	=> true,
		'enable_urls'		=> true,
		'enable_sig'		=> true,

		'message'			=> $message,
		'post_checksum'		=> '',
		'message_md5'		=> '',
				
		'bbcode_bitfield'	=> $bitfield,
		'bbcode_uid'		=> $uid,

		'post_edit_locked'	=> 0,
		'topic_title'		=> $subject,
		'notify_set'		=> false,
		'notify'			=> false,
		'post_time' 		=> 0,
		'forum_name'		=> '',
		'enable_indexing'	=> true,
		
		'topic_approved'	=> true,
		'post_approved'		=> true,
	);

	submit_post('post', $subject, '', POST_NORMAL, $poll, $data);	
	
	//Restore user permissions
	change_auth('', 'restore', $perms);	
}
?>