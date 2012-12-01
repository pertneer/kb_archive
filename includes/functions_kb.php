<?php
/**
*
* @package phpBB Knowledge Base Mod (KB)
* @version $Id: $
* @copyright (c) 2009 Andreas Nexmann
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

//
// This file holds functions not within the kb class
//

/**
* Fill smiley templates (or just the variables) with smilies, either in a window or inline
* Modified phpBB function
*/
function kb_generate_smilies($mode)
{
	global $auth, $db, $user, $config, $template;
	global $phpEx, $phpbb_root_path;

	if ($mode == 'window')
	{
		$user->setup('posting');

		page_header($user->lang['SMILIES']);

		$template->set_filenames(array(
			'body' => 'posting_smilies.html')
		);
	}

	$display_link = false;
	if ($mode == 'inline')
	{
		$sql = 'SELECT smiley_id
			FROM ' . SMILIES_TABLE . '
			WHERE display_on_posting = 0';
		$result = $db->sql_query_limit($sql, 1, 0, 3600);

		if ($row = $db->sql_fetchrow($result))
		{
			$display_link = true;
		}
		$db->sql_freeresult($result);
	}

	$last_url = '';

	$sql = 'SELECT *
		FROM ' . SMILIES_TABLE .
		(($mode == 'inline') ? ' WHERE display_on_posting = 1 ' : '') . '
		ORDER BY smiley_order';
	$result = $db->sql_query($sql, 3600);

	$smilies = array();
	while ($row = $db->sql_fetchrow($result))
	{
		if (empty($smilies[$row['smiley_url']]))
		{
			$smilies[$row['smiley_url']] = $row;
		}
	}
	$db->sql_freeresult($result);

	if (sizeof($smilies))
	{
		foreach ($smilies as $row)
		{
			$template->assign_block_vars('smiley', array(
				'SMILEY_CODE'	=> $row['code'],
				'A_SMILEY_CODE'	=> addslashes($row['code']),
				'SMILEY_IMG'	=> $phpbb_root_path . $config['smilies_path'] . '/' . $row['smiley_url'],
				'SMILEY_WIDTH'	=> $row['smiley_width'],
				'SMILEY_HEIGHT'	=> $row['smiley_height'],
				'SMILEY_DESC'	=> $row['emotion'])
			);
		}
	}

	if ($mode == 'inline' && $display_link)
	{
		$template->assign_vars(array(
			'S_SHOW_SMILEY_LINK' 	=> true,
			'U_MORE_SMILIES' 		=> append_sid("{$phpbb_root_path}kb.$phpEx", 'i=smilies')
		));
	}

	if ($mode == 'window')
	{
		page_footer();
	}
}

/**
* Show upload popup (progress bar)
*/
function kb_upload_popup()
{
	global $template, $user;

	$user->setup('posting');

	page_header($user->lang['PROGRESS_BAR']);

	$template->set_filenames(array(
		'popup'	=> 'posting_progress_bar.html')
	);

	$template->assign_vars(array(
		'PROGRESS_BAR'	=> $user->img('upload_bar', $user->lang['UPLOAD_IN_PROGRESS']))
	);

	$template->display('popup');

	garbage_collection();
	exit_handler();
}

/**
* Upload Attachment - filedata is generated here
* Uses upload class
* Slightly modified phpBB class
*/
function kb_upload_attachment($form_name, $local = false, $local_storage = '', $local_filedata = false)
{
	global $auth, $user, $config, $db, $cache;
	global $phpbb_root_path, $phpEx;

	$filedata = array(
		'error'	=> array()
	);

	include_once($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
	$upload = new fileupload();

	if ($config['check_attachment_content'])
	{
		$upload->set_disallowed_content(explode('|', $config['mime_triggers']));
	}

	if (!$local)
	{
		$filedata['post_attach'] = ($upload->is_valid($form_name)) ? true : false;
	}
	else
	{
		$filedata['post_attach'] = true;
	}

	if (!$filedata['post_attach'])
	{
		$filedata['error'][] = $user->lang['NO_UPLOAD_FORM_FOUND'];
		return $filedata;
	}

	$extensions = $cache->obtain_attach_extensions('kb');
	$upload->set_allowed_extensions(array_keys($extensions['_allowed_']));

	$file = ($local) ? $upload->local_upload($local_storage, $local_filedata) : $upload->form_upload($form_name);

	if ($file->init_error)
	{
		$filedata['post_attach'] = false;
		return $filedata;
	}

	$ext_cat_id = (isset($extensions[$file->get('extension')]['display_cat'])) ? $extensions[$file->get('extension')]['display_cat'] : ATTACHMENT_CATEGORY_NONE;

	// Make sure the image category only holds valid images...
	if ($ext_cat_id == ATTACHMENT_CATEGORY_IMAGE && !$file->is_image())
	{
		$file->remove();

		// If this error occurs a user tried to exploit an IE Bug by renaming extensions
		// Since the image category is displaying content inline we need to catch this.
		trigger_error($user->lang['ATTACHED_IMAGE_NOT_IMAGE']);
	}

	// Do we have to create a thumbnail?
	$filedata['thumbnail'] = ($ext_cat_id == ATTACHMENT_CATEGORY_IMAGE && $config['img_create_thumbnail']) ? 1 : 0;

	// Check Image Size, if it is an image
	if (!$auth->acl_get('a_') && !$auth->acl_get('m_kb', $cat_id) && $ext_cat_id == ATTACHMENT_CATEGORY_IMAGE)
	{
		$file->upload->set_allowed_dimensions(0, 0, $config['img_max_width'], $config['img_max_height']);
	}

	// Admins and mods are allowed to exceed the allowed filesize
	if (!$auth->acl_get('a_') && !$auth->acl_get('m_kb', $cat_id))
	{
		if (!empty($extensions[$file->get('extension')]['max_filesize']))
		{
			$allowed_filesize = $extensions[$file->get('extension')]['max_filesize'];
		}
		else
		{
			$allowed_filesize = ($is_message) ? $config['max_filesize_pm'] : $config['max_filesize'];
		}

		$file->upload->set_max_filesize($allowed_filesize);
	}

	$file->clean_filename('unique', $user->data['user_id'] . '_');

	// Are we uploading an image *and* this image being within the image category? Only then perform additional image checks.
	$no_image = ($ext_cat_id == ATTACHMENT_CATEGORY_IMAGE) ? false : true;

	$file->move_file($config['upload_path'], false, $no_image);

	if (sizeof($file->error))
	{
		$file->remove();
		$filedata['error'] = array_merge($filedata['error'], $file->error);
		$filedata['post_attach'] = false;

		return $filedata;
	}

	$filedata['filesize'] = $file->get('filesize');
	$filedata['mimetype'] = $file->get('mimetype');
	$filedata['extension'] = $file->get('extension');
	$filedata['physical_filename'] = $file->get('realname');
	$filedata['real_filename'] = $file->get('uploadname');
	$filedata['filetime'] = time();

	// Check our complete quota
	if ($config['attachment_quota'])
	{
		if ($config['upload_dir_size'] + $file->get('filesize') > $config['attachment_quota'])
		{
			$filedata['error'][] = $user->lang['ATTACH_QUOTA_REACHED'];
			$filedata['post_attach'] = false;

			$file->remove();

			return $filedata;
		}
	}

	// Check free disk space
	if ($free_space = @disk_free_space($phpbb_root_path . $config['upload_path']))
	{
		if ($free_space <= $file->get('filesize'))
		{
			$filedata['error'][] = $user->lang['ATTACH_QUOTA_REACHED'];
			$filedata['post_attach'] = false;

			$file->remove();

			return $filedata;
		}
	}

	// Create Thumbnail
	if ($filedata['thumbnail'])
	{
		$source = $file->get('destination_file');
		$destination = $file->get('destination_path') . '/thumb_' . $file->get('realname');

		if (!create_thumbnail($source, $destination, $file->get('mimetype')))
		{
			$filedata['thumbnail'] = 0;
		}
	}

	return $filedata;
}

/**
* Delete Attachments
*
* @param string $mode can be: post|message|topic|attach|user
* @param mixed $ids can be: post_ids, message_ids, topic_ids, attach_ids, user_ids
* @param bool $resync set this to false if you are deleting posts or topics
*/
function kb_delete_attachments($mode, $ids, $resync = true)
{
	global $db, $config, $phpbb_root_path, $phpEx;

	if (is_array($ids) && sizeof($ids))
	{
		$ids = array_unique($ids);
		$ids = array_map('intval', $ids);
	}
	else
	{
		$ids = array((int) $ids);
	}

	if (!sizeof($ids))
	{
		return false;
	}

	switch ($mode)
	{
		case 'add':
		case 'edit':
		case 'delete':
			$sql_id = 'article_id';
		break;

		case 'comment':
			$sql_id = 'comment_id';
		break;

		case 'attach':
		default:
			$sql_id = 'attach_id';
			$mode = 'attach';
		break;
	}

	$article_ids = $comment_ids = $physical = array();
	include_once($phpbb_root_path . 'includes/functions_admin.' . $phpEx);

	// Collect post and topic ids for later use if we need to touch remaining entries (if resync is enabled)
	$sql = 'SELECT article_id, comment_id, physical_filename, thumbnail, filesize, is_orphan
			FROM ' . KB_ATTACHMENTS_TABLE . '
			WHERE ' . $db->sql_in_set($sql_id, $ids);
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		// We only need to store post/message/topic ids if resync is enabled and the file is not orphaned
		if ($resync && !$row['is_orphan'])
		{
			if($mode == 'add' || $mode == 'delete' || $mode == 'edit')
			{
				$article_ids[] = $row['article_id'];
			}
			elseif($mode == 'comment')
			{
				$comment_ids[] = $row['comment_id'];
			}
		}

		$physical[] = array('filename' => $row['physical_filename'], 'thumbnail' => $row['thumbnail'], 'filesize' => $row['filesize'], 'is_orphan' => $row['is_orphan']);
	}
	$db->sql_freeresult($result);

	// Delete attachments
	$sql = 'DELETE FROM ' . KB_ATTACHMENTS_TABLE . '
			WHERE ' . $db->sql_in_set($sql_id, $ids);
	$db->sql_query($sql);
	$num_deleted = $db->sql_affectedrows();

	if (!$num_deleted)
	{
		return 0;
	}

	// Delete attachments from filesystem
	$space_removed = $files_removed = 0;
	foreach ($physical as $file_ary)
	{
		if (phpbb_unlink($file_ary['filename'], 'file', true) && !$file_ary['is_orphan'])
		{
			// Only non-orphaned files count to the file size
			$space_removed += $file_ary['filesize'];
			$files_removed++;
		}

		if ($file_ary['thumbnail'])
		{
			phpbb_unlink($file_ary['filename'], 'thumbnail', true);
		}
	}

	if ($space_removed || $files_removed)
	{
		set_config('upload_dir_size', $config['upload_dir_size'] - $space_removed, true);
		set_config('num_files', $config['num_files'] - $files_removed, true);
	}

	// If we do not resync, we do not need to adjust any message, post, topic or user entries
	if (!$resync)
	{
		return $num_deleted;
	}

	// No more use for the original ids
	unset($ids);

	// Now, we need to resync posts, messages, topics. We go through every one of them
	$article_ids = array_unique($article_ids);
	$comment_ids = array_unique($comment_ids);

	// Update post indicators for posts now no longer having attachments
	if (sizeof($article_ids))
	{
		$sql = 'UPDATE ' . KB_TABLE . '
			SET article_attachment = 0
			WHERE ' . $db->sql_in_set('article_id', $article_ids);
		$db->sql_query($sql);
	}

	// Update message table if messages are affected
	if (sizeof($comment_ids))
	{
		$sql = 'UPDATE ' . KB_COMMENTS_TABLE . '
			SET comment_attachment = 0
			WHERE ' . $db->sql_in_set('comment_id', $comment_ids);
		$db->sql_query($sql);
	}

	return $num_deleted;
}

/**
* Submit Article
*/
function article_submit($mode, &$data, $update_message = true, $old_data = array())
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
	
	if($mode == 'delete')
	{
		// No delete from this function
		return false;
	}
	
	if($mode == 'add')
	{
		$update_message = true;
	}
	
	// Begin sql transaction and build needed sql data
	$db->sql_transaction('begin');
	
	$sql_data = array();
	$data['article_title'] = truncate_string($data['article_title']);
	$data['article_desc'] = truncate_string($data['article_desc'], 300, 500);
	if($mode == 'edit')
	{
		// Build edits table to take care of old data
		$sql_data[KB_EDITS_TABLE]['sql'] = array(
				'article_id'						=> 		$old_data['article_id'],
				'parent_id'							=>		$old_data['article_last_edit_id'], // So silly of me, no need for a function here
				'edit_user_id'						=>		$user->data['user_id'], // Data of the user doing the edit
				'edit_user_name'					=>		$user->data['username'],
				'edit_user_color'					=>		$user->data['user_colour'],
				'edit_time'							=>		$old_data['article_time'],
				'edit_article_title'				=>		$old_data['article_title'],
				'edit_article_desc'					=>		$old_data['article_desc'],
				'edit_article_desc_bitfield'		=>		$old_data['article_desc_bitfield'],
				'edit_article_desc_options'			=>		$old_data['article_desc_options'],
				'edit_article_desc_uid'				=>		$old_data['article_desc_uid'],
				'edit_article_text'					=>		'',
				'edit_enable_bbcode'				=>		$old_data['enable_bbcode'],
				'edit_enable_smilies'				=>		$old_data['enable_smilies'],
				'edit_enable_magic_url'				=>		$old_data['enable_magic_url'],
				'edit_enable_sig'					=>		$old_data['enable_sig'],
				'edit_bbcode_bitfield'				=>		$old_data['bbcode_bitfield'],
				'edit_bbcode_uid'					=>		$old_data['bbcode_uid'],
				'edit_article_status'				=>		$old_data['article_status'],
				'edit_reason'						=>		$old_data['article_edit_reason'],
				'edit_reason_global'				=>		$old_data['article_edit_reason_global'],
				'edit_moderated'					=>		$old_data['edit_moderated'],
		);
		
		if($update_message)
		{
			$sql_data[KB_EDITS_TABLE]['sql'] = array_merge($sql_data[KB_EDITS_TABLE]['sql'], array(
				'edit_article_text'					=>		$old_data['article_text'],
				'edit_article_checksum'				=>		$old_data['article_checksum'],
			));
		}
		
		$sql = 'INSERT INTO ' . KB_EDITS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data[KB_EDITS_TABLE]['sql']);
		$db->sql_query($sql);
		$edit_id = $db->sql_nextid();
	}
	
	// Build sql data for articles table
	$sql_data[KB_TABLE]['sql'] = array(
		'cat_id'						=> 	$data['cat_id'],
		'article_title'					=>	$data['article_title'],
		'article_desc'					=>	$data['article_desc'],
		'article_desc_bitfield'			=>	$data['article_desc_bitfield'],
		'article_desc_options'			=>	$data['article_desc_options'],
		'article_desc_uid'				=>	$data['article_desc_uid'],
		'article_checksum'				=>	$data['message_md5'],
		'article_status'				=>	$data['article_status'],
		'article_attachment'			=>	(isset($data['attachment_data'])) ? 1 : 0, //$data['article_attachment'],
		'article_views'					=>	$data['article_views'],
		'article_user_id'				=>	$data['article_user_id'],
		'article_user_name'				=>	$data['article_user_name'],
		'article_user_color'			=>	$data['article_user_color'],
		'article_time'					=>	$data['current_time'],
		'article_tags'					=>	$data['article_tags'],
		'article_icon'					=>	$data['article_icon'],
		'enable_bbcode'					=>	$data['enable_bbcode'],
		'enable_smilies'				=>	$data['enable_smilies'],
		'enable_magic_url'				=>	$data['enable_urls'],
		'enable_sig'					=>	$data['enable_sig'],
		'bbcode_bitfield'				=>	$data['bbcode_bitfield'],
		'bbcode_uid'					=>	$data['bbcode_uid'],
		'article_last_edit_time'		=>	$data['current_time'],
		'article_last_edit_id'			=>	$data['article_last_edit_id'],
		'article_edit_reason'			=>	$data['article_edit_reason'],
		'article_edit_reason_global'	=>	$data['article_edit_reason_global'],
	);
	
	if($mode == 'edit')
	{
		$sql_data[KB_TABLE]['sql']['article_last_edit_time'] = $data['current_time'];
		$sql_data[KB_TABLE]['sql']['article_last_edit_id'] = $edit_id;
	}
	
	// Only update text if needed
	if($update_message)
	{
		$sql_data[KB_TABLE]['sql']['article_text'] = $data['message'];
	}
	
	// Submit article
	if($mode == 'add')
	{
		$sql = 'INSERT INTO ' . KB_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data[KB_TABLE]['sql']);
		$db->sql_query($sql);
		$data['article_id'] = $db->sql_nextid();
	}
	elseif($mode == 'edit')
	{
		$sql = 'UPDATE ' . KB_TABLE . ' 
				SET ' . $db->sql_build_array('UPDATE', $sql_data[KB_TABLE]['sql']) . '
				WHERE article_id = ' . $data['article_id'];
		$db->sql_query($sql);
		
		set_config('kb_last_updated', time(), true);
	}
	
	// Handle tags
	if($data['create_tags'])
	{
		// Delete old tag entries
		if($mode == 'edit')
		{
			$sql = 'DELETE FROM ' . KB_TAGS_TABLE . ' WHERE article_id = ' . $data['article_id'];
			$db->sql_query($sql);
		}
		
		// Seperate tags by , then build them into the tags table
		$data['article_tags'] = truncate_string($data['article_tags']);
		$tags = explode(',', $data['article_tags']);
		
		foreach($tags as $tag)
		{
			// Strip starting spaces
			$str = $tag;
			if(strpos($str, ' ') === 0)
			{
				$tag = utf8_substr($tag, 1);
			}
			
			$sql_data[KB_TAGS_TABLE]['sql'][] = array(
				'article_id'		=> $data['article_id'],
				'tag_name'			=> $tag,
				'tag_name_lc'		=> utf8_strtolower($tag),
			);
		}
		
		$db->sql_multi_insert(KB_TAGS_TABLE, $sql_data[KB_TAGS_TABLE]['sql']);
	}
	
	// Don't update cats table until the article is shown.
	// Only thing left is attachments
	// Submit Attachments
	if (!empty($data['attachment_data']) && $data['article_id'] && in_array($mode, array('add', 'edit')))
	{
		$space_taken = $files_added = 0;
		$orphan_rows = array();

		foreach ($data['attachment_data'] as $pos => $attach_row)
		{
			$orphan_rows[(int) $attach_row['attach_id']] = array();
		}

		if (sizeof($orphan_rows))
		{
			$sql = 'SELECT attach_id, filesize, physical_filename
				FROM ' . KB_ATTACHMENTS_TABLE . '
				WHERE ' . $db->sql_in_set('attach_id', array_keys($orphan_rows)) . '
				AND is_orphan = 1
				AND poster_id = ' . $user->data['user_id'];
			$result = $db->sql_query($sql);

			$orphan_rows = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$orphan_rows[$row['attach_id']] = $row;
			}
			$db->sql_freeresult($result);
		}

		foreach ($data['attachment_data'] as $pos => $attach_row)
		{
			if ($attach_row['is_orphan'] && !isset($orphan_rows[$attach_row['attach_id']]))
			{
				continue;
			}

			if (!$attach_row['is_orphan'])
			{
				// update entry in db if attachment already stored in db and filespace
				$sql = 'UPDATE ' . KB_ATTACHMENTS_TABLE . "
					SET attach_comment = '" . $db->sql_escape($attach_row['attach_comment']) . "'
					WHERE attach_id = " . (int) $attach_row['attach_id'] . '
						AND is_orphan = 0';
				$db->sql_query($sql);
			}
			else
			{
				// insert attachment into db
				if (!@file_exists($phpbb_root_path . $config['upload_path'] . '/' . basename($orphan_rows[$attach_row['attach_id']]['physical_filename'])))
				{
					continue;
				}

				$space_taken += $orphan_rows[$attach_row['attach_id']]['filesize'];
				$files_added++;

				$attach_sql = array(
					'article_id'		=> $data['article_id'],
					'comment_id'		=> 0,
					'is_orphan'			=> 0,
					'poster_id'			=> $data['article_user_id'],
					'attach_comment'	=> $attach_row['attach_comment'],
				);

				$sql = 'UPDATE ' . KB_ATTACHMENTS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $attach_sql) . '
					WHERE attach_id = ' . $attach_row['attach_id'] . '
						AND is_orphan = 1
						AND poster_id = ' . $user->data['user_id'];
				$db->sql_query($sql);
			}
		}

		if ($space_taken && $files_added)
		{
			set_config('upload_dir_size', $config['upload_dir_size'] + $space_taken, true);
			set_config('num_files', $config['num_files'] + $files_added, true);
		}
	}
	
	return $data['article_id'];
}

/**
* Submit Comment
*/
function comment_submit($mode, &$data, $update_message = true)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
	
	if($mode == 'delete')
	{
		// No delete from this function
		return false;
	}
	
	if($mode == 'add')
	{
		$update_message = true;
	}
	
	// Begin sql transaction and build needed sql data
	$db->sql_transaction('begin');
	
	$sql_data = array();
	$data['comment_title'] = truncate_string($data['comment_title']);
	
	// Build sql data for articles table
	$sql_data[KB_COMMENTS_TABLE]['sql'] = array(
		'article_id'				=> 		$data['article_id'],
		'comment_title'				=>		$data['comment_title'],
		'comment_checksum'			=>		$data['message_md5'],
		'comment_type'				=>		$data['comment_type'],
		'comment_attachment'		=>		(isset($data['attachment_data'])) ? 1 : 0, //$data['comment_attachment'],
		'comment_user_id'			=>		$data['comment_user_id'],
		'comment_user_name'			=>		$data['comment_user_name'],
		'comment_user_color'		=>		$data['comment_user_color'],
		'comment_time'				=>		$data['current_time'],
		'enable_bbcode'				=>		$data['enable_bbcode'],
		'enable_smilies'			=>		$data['enable_smilies'],
		'enable_magic_url'			=>		$data['enable_urls'],
		'enable_sig'				=>		$data['enable_sig'],
		'bbcode_bitfield'			=>		$data['bbcode_bitfield'],
		'bbcode_uid'				=>		$data['bbcode_uid'],
		'comment_edit_time'			=>		$data['comment_edit_time'],
		'comment_edit_id'			=>		$data['comment_edit_id'],
		'comment_edit_name'			=>		$data['comment_edit_name'],
		'comment_edit_color'		=>		$data['comment_edit_color'],
	);
	
	// Only update text if needed
	if($update_message)
	{
		$sql_data[KB_COMMENTS_TABLE]['sql']['comment_text'] = $data['message'];
	}
	
	// Submit comment
	if($mode == 'add')
	{
		$sql = 'INSERT INTO ' . KB_COMMENTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data[KB_COMMENTS_TABLE]['sql']);
		$db->sql_query($sql);
		$data['comment_id'] = $db->sql_nextid();
		
		set_config('kb_last_updated', time(), true);		
		
		if($data['comment_type'] == COMMENT_GLOBAL)
		{
			// Update article table comment count
			$sql = 'UPDATE ' . KB_TABLE . "
					SET article_comments = article_comments + 1
					WHERE article_id = {$data['article_id']}";
			$db->sql_query($sql);
			
			set_config('kb_total_comments', $config['kb_total_comments'] + 1, true);
		}
	}
	// Else edit it
	elseif($mode == 'edit')
	{
		$sql = 'UPDATE ' . KB_COMMENTS_TABLE . ' 
				SET ' . $db->sql_build_array('UPDATE', $sql_data[KB_COMMENTS_TABLE]['sql']) . '
				WHERE comment_id = ' . $data['comment_id'];
		$db->sql_query($sql);
		
		set_config('kb_last_updated', time(), true);
	}
	
	// Only thing left is attachments
	// Submit Attachments
	if (!empty($data['attachment_data']) && $data['comment_id'] && in_array($mode, array('add', 'edit')))
	{
		$space_taken = $files_added = 0;
		$orphan_rows = array();

		foreach ($data['attachment_data'] as $pos => $attach_row)
		{
			$orphan_rows[(int) $attach_row['attach_id']] = array();
		}

		if (sizeof($orphan_rows))
		{
			$sql = 'SELECT attach_id, filesize, physical_filename
				FROM ' . KB_ATTACHMENTS_TABLE . '
				WHERE ' . $db->sql_in_set('attach_id', array_keys($orphan_rows)) . '
					AND is_orphan = 1
					AND poster_id = ' . $user->data['user_id'];
			$result = $db->sql_query($sql);

			$orphan_rows = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$orphan_rows[$row['attach_id']] = $row;
			}
			$db->sql_freeresult($result);
		}

		foreach ($data['attachment_data'] as $pos => $attach_row)
		{
			if ($attach_row['is_orphan'] && !isset($orphan_rows[$attach_row['attach_id']]))
			{
				continue;
			}

			if (!$attach_row['is_orphan'])
			{
				// update entry in db if attachment already stored in db and filespace
				$sql = 'UPDATE ' . KB_ATTACHMENTS_TABLE . "
					SET attach_comment = '" . $db->sql_escape($attach_row['attach_comment']) . "'
					WHERE attach_id = " . (int) $attach_row['attach_id'] . '
						AND is_orphan = 0';
				$db->sql_query($sql);
			}
			else
			{
				// insert attachment into db
				if (!@file_exists($phpbb_root_path . $config['upload_path'] . '/' . basename($orphan_rows[$attach_row['attach_id']]['physical_filename'])))
				{
					continue;
				}

				$space_taken += $orphan_rows[$attach_row['attach_id']]['filesize'];
				$files_added++;

				$attach_sql = array(
					'article_id'		=> 0,
					'comment_id'		=> $data['comment_id'],
					'is_orphan'			=> 0,
					'poster_id'			=> $data['comment_user_id'],
					'attach_comment'	=> $attach_row['attach_comment'],
				);

				$sql = 'UPDATE ' . KB_ATTACHMENTS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $attach_sql) . '
					WHERE attach_id = ' . $attach_row['attach_id'] . '
						AND is_orphan = 1
						AND poster_id = ' . $user->data['user_id'];
				$db->sql_query($sql);
			}
		}

		if ($space_taken && $files_added)
		{
			set_config('upload_dir_size', $config['upload_dir_size'] + $space_taken, true);
			set_config('num_files', $config['num_files'] + $files_added, true);
		}
	}
	
	return $data['comment_id'];
}

/**
* Generate inline attachment entry
*/
function kb_posting_gen_attachment_entry($attachment_data, &$filename_data, $show_attach_box = true)
{
	global $template, $config, $phpbb_root_path, $phpEx, $user, $auth;

	// Some default template variables
	$template->assign_vars(array(
		'S_SHOW_ATTACH_BOX'	=> $show_attach_box,
		'S_HAS_ATTACHMENTS'	=> sizeof($attachment_data),
		'FILESIZE'			=> $config['max_filesize'],
		'FILE_COMMENT'		=> (isset($filename_data['filecomment'])) ? $filename_data['filecomment'] : '',
	));

	if (sizeof($attachment_data))
	{
		// We display the posted attachments within the desired order.
		($config['display_order']) ? krsort($attachment_data) : ksort($attachment_data);

		foreach ($attachment_data as $count => $attach_row)
		{
			$hidden = '';
			$attach_row['real_filename'] = basename($attach_row['real_filename']);

			foreach ($attach_row as $key => $value)
			{
				$hidden .= '<input type="hidden" name="attachment_data[' . $count . '][' . $key . ']" value="' . $value . '" />';
			}

			$download_link = append_sid("{$phpbb_root_path}download/kb.$phpEx", 'mode=view&amp;id=' . (int) $attach_row['attach_id'], true, ($attach_row['is_orphan']) ? $user->session_id : false);

			$template->assign_block_vars('attach_row', array(
				'FILENAME'			=> basename($attach_row['real_filename']),
				'A_FILENAME'		=> addslashes(basename($attach_row['real_filename'])),
				'FILE_COMMENT'		=> $attach_row['attach_comment'],
				'ATTACH_ID'			=> $attach_row['attach_id'],
				'S_IS_ORPHAN'		=> $attach_row['is_orphan'],
				'ASSOC_INDEX'		=> $count,

				'U_VIEW_ATTACHMENT'	=> $download_link,
				'S_HIDDEN'			=> $hidden)
			);
		}
	}

	return sizeof($attachment_data);
}

/**
* Notify user
*/
function kb_handle_notification($article_id, $article_title, $notify_on)
{
	global $db, $user, $phpEx, $phpbb_root_path, $config;
	
	if(!is_array($notify_on))
	{
		$notify_on = array($notify_on);
	}
	
	$sql_query = array(
		'SELECT'	=> 'n.user_id, n.notify_by, u.user_lang, u.user_email, u.username',
		'FROM'		=> array(
			KB_TRACK_TABLE	=> 'n',
		),
		'LEFT_JOIN'	=> array(
			array(
				  'FROM'	=> array(USERS_TABLE => 'u'),
				  'ON'		=> 'n.user_id = u.user_id'
			),
		),
		'WHERE' => "n.article_id = $article_id AND n.subscribed > 0 AND " . $db->sql_in_set('n.notify_on', $notify_on),
	);
	$sql = $db->sql_build_query('SELECT', $sql_query);
	$result = $db->sql_query($sql);
	$update_ids = array();
	$pm_list = array();
	$lang_ary = array(); // Supporting multiple languages
	include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
	include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
	while($row = $db->sql_fetchrow($result))
	{
		switch($row['notify_by'])
		{
			case NOTIFY_MAIL:
				// Handle mail
				$messenger = new messenger(false);

				$messenger->template('kb_notify_email', $row['user_lang']);

				$messenger->to($row['user_email'], $row['username']);

				$messenger->headers('X-AntiAbuse: Board servername - ' . $config['server_name']);
				$messenger->headers('X-AntiAbuse: User_id - ' . $user->data['user_id']);
				$messenger->headers('X-AntiAbuse: Username - ' . $user->data['username']);
				$messenger->headers('X-AntiAbuse: User IP - ' . $user->ip);

				$messenger->assign_vars(array(
					'ARTICLE_TITLE'	=> htmlspecialchars_decode($article_title),
					'USERNAME'		=> htmlspecialchars_decode($row['username']),
					'U_ARTICLE'		=> htmlspecialchars_decode(generate_board_url() . "kb.$phpEx?a=" . $article_id),
				));

				$messenger->send(NOTIFY_EMAIL);
			break;
				
			case NOTIFY_PM:
				// Handle pm
				$pm_list[$row['user_lang']]['u'][$row['user_id']] = 'to';
				
				// Parse message here to avoid lang probs
				if(!isset($lang_ary[$row['user_lang']]))
				{
					$message_text = file_get_contents($phpbb_root_path . 'language/' . $row['user_lang'] . '/email/kb_notify_pm.txt');
					$message_parser = new parse_message();
					$message_parser->message = $message_text;
					// Parse the url here as it doesn't contain lang vars
					$message_parser->message = str_replace('{U_ARTICLE}', '[url]' . generate_board_url() . "/kb.$phpEx?a=" . $article_id . '[/url]', $message_parser->message);
					unset($message_text);
					
					// Parse message. no imgs allowed, parse urls, smileys and common bbcode, no quote or flash.
					$message_parser->parse(true, true, true, false, false, false, true);
					
					$lang_ary[$row['user_lang']] = array(
						'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
						'bbcode_uid'			=> $message_parser->bbcode_uid,
						'message'				=> $message_parser->message,
					);
				}
			break;
		}
		$update_ids[] = $row['user_id'];
	}
	$db->sql_freeresult($result);
	
	if(sizeof($lang_ary))
	{
		// Do one pm for each language
		foreach($lang_ary as $language => $message)
		{
			if(sizeof($pm_list[$language]))
			{
				// Include language file for this lang
				unset($lang);
				include($phpbb_root_path . 'language/' . $language . '/mods/kb.' . $phpEx);
				
				$message['message'] = str_replace('{ARTICLE_TITLE}', $article_title, $message['message']);
				$pm_data = array(
					'from_user_id'			=> $user->data['user_id'],
					'from_user_ip'			=> $user->ip,
					'from_username'			=> $user->data['username'],
					'reply_from_root_level'	=> 0,
					'reply_from_msg_id'		=> 0,
					'icon_id'				=> 0,
					'enable_sig'			=> false,
					'enable_bbcode'			=> true,
					'enable_smilies'		=> true,
					'enable_urls'			=> true,
					'bbcode_bitfield'		=> $message['bbcode_bitfield'],
					'bbcode_uid'			=> $message['bbcode_uid'],
					'message'				=> $message['message'],
					'attachment_data'		=> array(),
					'filename_data'			=> array(),
					'address_list'			=> $pm_list[$language]
				);
				$msg_id = submit_pm('post', $lang['NOTIFY_PM_SUBJECT'], $pm_data);
			}
		}
	}
	
	// Update tables to tell people there is a new notification
	// This should enable popups as well
	if(!empty($update_ids))
	{
		$sql = 'UPDATE ' . KB_TRACK_TABLE . "
				SET subscribed = 2
				WHERE article_id = $article_id
				AND " . $db->sql_in_set('user_id', $update_ids);
		$db->sql_query($sql);
	}
}

/**
* Generate popups
*/
function kb_generate_popups()
{
	global $user, $db, $template;
	
	if(!$user->data['is_registered'])
	{
		return array('generate' => false);
	}
	
	$sql = 'SELECT article_id
			FROM ' . KB_TRACK_TABLE . '
			WHERE user_id = ' . $user->data['user_id'] . '
			AND subscribed = 2
			AND notify_by = ' . NOTIFY_POPUP;
	$result = $db->sql_query($sql);
	if(!$article_data = $db->sql_fetchrow($result))
	{
		return array('generate' => false);
	}
	
	return array('generate' => true, 'article_id' => $article_data['article_id']);
}

/**
* Delete an article
*/
function article_delete($article_id, $cat_id, $article_data)
{
	global $phpEx, $user, $phpbb_root_path, $auth, $db, $config;
	
	$s_hidden_fields = build_hidden_fields(array(
		'a'		=> $article_id,
		'c'		=> $cat_id,
		'i'		=> 'delete')
	);
	
	if(confirm_box(true))
	{
		// Delete article
		// Delete from rate table
		$sql = 'DELETE FROM ' . KB_RATE_TABLE . "
				WHERE article_id = $article_id";
		$db->sql_query($sql);
		
		// Delete from comments table - no need to store these
		$sql = 'SELECT comment_id
				FROM ' . KB_COMMENTS_TABLE . "
				WHERE article_id = $article_id";
		$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
			comment_delete($row['comment_id'], $article_id, false);
		}
		$db->sql_freeresult($result);
		
		// Delete from edits table
		$sql = 'DELETE FROM ' . KB_EDITS_TABLE . "
				WHERE article_id = $article_id";
		$db->sql_query($sql);
		
		// Delete from attachments table, and delete attachment files
		kb_delete_attachments('delete', array($article_id));
		
		// Delete from tags table
		$sql = 'DELETE FROM ' . KB_TAGS_TABLE . "
				WHERE article_id = $article_id";
		$db->sql_query($sql);
		
		// Delete from tracking table
		$sql = 'DELETE FROM ' . KB_TRACK_TABLE . "
				WHERE article_id = $article_id";
		$db->sql_query($sql);
		
		// Delete from article table
		$sql = 'DELETE FROM ' . KB_TABLE . "
				WHERE article_id = $article_id";
		$db->sql_query($sql);
		
		// Resync cat table if needed
		if($article_data['article_status'] == STATUS_APPROVED)
		{
			$sql = 'UPDATE ' . KB_CATS_TABLE . "
					SET cat_articles = cat_articles - 1
					WHERE cat_id = $cat_id";
			$db->sql_query($sql);
			
			$sql = 'UPDATE ' . USERS_TABLE . " 
					SET user_articles = user_articles - 1
					WHERE user_id = {$article_data['article_user_id']}";
			$db->sql_query($sql);
			
			set_config('kb_total_articles', $config['kb_total_articles'] - 1, true);
		}
		
		$meta_info = append_sid("{$phpbb_root_path}kb.$phpEx", "c=$cat_id");
		$message = $user->lang['ARTICLE_DELETED'] . '<br /><br />' . sprintf($user->lang['RETURN_KB_CAT'], '<a href="' . $meta_info . '">', '</a>');
		meta_refresh(5, $meta_info);
		trigger_error($message);
	}
	else
	{
		confirm_box(false, 'DELETE_ARTICLE', $s_hidden_fields);
	}
	
	redirect(append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_id"));
}

/*
* Delete a comment
*/
function comment_delete($comment_id, $article_id, $show_confirm_box, $type = COMMENT_GLOBAL)
{
	global $phpEx, $user, $phpbb_root_path, $auth, $db;
	
	$s_hidden_fields = build_hidden_fields(array(
		'comment_id' => $comment_id,
		'action' => 'delete',
		'i'		=> 'comment')
	);
	
	$do_delete = true;
	if($show_confirm_box)
	{
		if(confirm_box(true))
		{
			$do_delete = true;
		}
		else
		{
			confirm_box(false, 'DELETE_COMMENT', $s_hidden_fields);
		}
	}
	
	if($do_delete)
	{
		// Delete article
		// Delete from rate table
		$sql = 'DELETE FROM ' . KB_COMMENTS_TABLE . "
				WHERE comment_id = $comment_id";
		$db->sql_query($sql);
		
		// Delete from attachments table, and delete attachment files
		kb_delete_attachments('comment', array($comment_id));
		
		if($type == COMMENT_GLOBAL)
		{
			// Update article table comment count
			$sql = 'UPDATE ' . KB_TABLE . " 
					SET article_comments = article_comments - 1
					WHERE article_id = $article_id";
			$db->sql_query($sql);
			
			set_config('kb_total_comments', $config['kb_total_comments'] - 1, true);
		}
		
		if(!$show_confirm_box)
		{
			return;
		}
		
		$meta_info = append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_id");
		$message = $user->lang['COMMENT_DELETED'] . '<br /><br />' . sprintf($user->lang['RETURN_KB_ARTICLE'], '<a href="' . $meta_info . '">', '</a>');
		meta_refresh(5, $meta_info);
		trigger_error($message);
	}
	
	redirect(append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_id"));
}
	
/*
* Generate KB Navigation Bar
*/
function generate_kb_nav($page_title = '', $data = array())
{
	global $phpEx, $user, $phpbb_root_path, $template;
	
	// Knowledge Base link
	$template->assign_block_vars('navlinks', array(
		'S_IS_CAT'		=> true,
		'FORUM_NAME'	=> $user->lang['KB'],
		'FORUM_ID'		=> 0,
		'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}kb.$phpEx"))
	);
	
	// Parent Categories
	$cat_parents = (!empty($data)) ? kb_get_cat_parents($data) : array();
	if(!empty($cat_parents))
	{
		foreach ($cat_parents as $parent_cat_id => $parent_name)
		{
			$template->assign_block_vars('navlinks', array(
				'S_IS_CAT'		=> true,
				'FORUM_NAME'	=> $parent_name,
				'FORUM_ID'		=> $parent_cat_id,
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}kb.$phpEx", 'c=' . $parent_cat_id))
			);
		}
	}
	
	// Current Category
	if(!empty($data))
	{
		$template->assign_block_vars('navlinks', array(
			'S_IS_CAT'		=> true,
			'FORUM_NAME'	=> $data['cat_name'],
			'FORUM_ID'		=> $data['cat_id'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}kb.$phpEx", 'c=' . $data['cat_id']))
		);
	}
	
	if($page_title == '')
	{
		return;
	}
	
	$template->assign_block_vars('navlinks', array(
		'S_IS_CAT'		=> true,
		'FORUM_NAME'	=> $page_title,
		'FORUM_ID'		=> 0,
		'U_VIEW_FORUM'	=> '', // This is for the last page, it will link to the page itself
	));
	
	return;
}

/**
* Returns cat parents as an array.
*/
function kb_get_cat_parents(&$cat_data)
{
	global $db;

	$cat_parents = array();

	if ($cat_data['parent_id'] > 0)
	{
		$sql = 'SELECT cat_id, cat_name
			FROM ' . KB_CATS_TABLE . '
			WHERE left_id < ' . $cat_data['left_id'] . '
				AND right_id > ' . $cat_data['right_id'] . '
			ORDER BY left_id ASC';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$cat_parents[$row['cat_id']] = $row['cat_name'];
		}
		$db->sql_freeresult($result);
	}

	return $cat_parents;
}

/**
* General attachment parsing
*
* @param mixed $forum_id The forum id the attachments are displayed in (false if in private message)
* @param string &$message The post/private message
* @param array &$attachments The attachments to parse for (inline) display. The attachments array will hold templated data after parsing.
* @param array &$update_count The attachment counts to be updated - will be filled
* @param bool $preview If set to true the attachments are parsed for preview. Within preview mode the comments are fetched from the given $attachments array and not fetched from the database.
*/
function kb_parse_attachments(&$message, &$attachments, &$update_count, $preview = false)
{
	if (!sizeof($attachments))
	{
		return;
	}

	global $template, $cache, $user;
	global $extensions, $config, $phpbb_root_path, $phpEx;

	//
	$compiled_attachments = array();

	if (!isset($template->filename['attachment_tpl']))
	{
		$template->set_filenames(array(
			'attachment_tpl'	=> 'attachment.html')
		);
	}

	if (empty($extensions) || !is_array($extensions))
	{
		$extensions = $cache->obtain_attach_extensions('kb');
	}

	// Look for missing attachment information...
	$attach_ids = array();
	foreach ($attachments as $pos => $attachment)
	{
		// If is_orphan is set, we need to retrieve the attachments again...
		if (!isset($attachment['extension']) && !isset($attachment['physical_filename']))
		{
			$attach_ids[(int) $attachment['attach_id']] = $pos;
		}
	}

	// Grab attachments (security precaution)
	if (sizeof($attach_ids))
	{
		global $db;

		$new_attachment_data = array();

		$sql = 'SELECT *
			FROM ' . KB_ATTACHMENTS_TABLE . '
			WHERE ' . $db->sql_in_set('attach_id', array_keys($attach_ids));
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			if (!isset($attach_ids[$row['attach_id']]))
			{
				continue;
			}

			// If we preview attachments we will set some retrieved values here
			if ($preview)
			{
				$row['attach_comment'] = $attachments[$attach_ids[$row['attach_id']]]['attach_comment'];
			}

			$new_attachment_data[$attach_ids[$row['attach_id']]] = $row;
		}
		$db->sql_freeresult($result);

		$attachments = $new_attachment_data;
		unset($new_attachment_data);
	}

	// Sort correctly
	if ($config['display_order'])
	{
		// Ascending sort
		krsort($attachments);
	}
	else
	{
		// Descending sort
		ksort($attachments);
	}

	foreach ($attachments as $attachment)
	{
		if (!sizeof($attachment))
		{
			continue;
		}

		// We need to reset/empty the _file block var, because this function might be called more than once
		$template->destroy_block_vars('_file');

		$block_array = array();

		// Some basics...
		$attachment['extension'] = strtolower(trim($attachment['extension']));
		$filename = $phpbb_root_path . $config['upload_path'] . '/' . basename($attachment['physical_filename']);
		$thumbnail_filename = $phpbb_root_path . $config['upload_path'] . '/thumb_' . basename($attachment['physical_filename']);

		$upload_icon = '';

		if (isset($extensions[$attachment['extension']]))
		{
			if ($user->img('icon_topic_attach', '') && !$extensions[$attachment['extension']]['upload_icon'])
			{
				$upload_icon = $user->img('icon_topic_attach', '');
			}
			else if ($extensions[$attachment['extension']]['upload_icon'])
			{
				$upload_icon = '<img src="' . $phpbb_root_path . $config['upload_icons_path'] . '/' . trim($extensions[$attachment['extension']]['upload_icon']) . '" alt="" />';
			}
		}

		$filesize = $attachment['filesize'];
		$size_lang = ($filesize >= 1048576) ? $user->lang['MIB'] : (($filesize >= 1024) ? $user->lang['KIB'] : $user->lang['BYTES']);
		$filesize = get_formatted_filesize($filesize, false);

		$comment = bbcode_nl2br(censor_text($attachment['attach_comment']));

		$block_array += array(
			'UPLOAD_ICON'		=> $upload_icon,
			'FILESIZE'			=> $filesize,
			'SIZE_LANG'			=> $size_lang,
			'DOWNLOAD_NAME'		=> basename($attachment['real_filename']),
			'COMMENT'			=> $comment,
		);

		$denied = false;

		if (!extension_allowed(false, $attachment['extension'], $extensions))
		{
			$denied = true;

			$block_array += array(
				'S_DENIED'			=> true,
				'DENIED_MESSAGE'	=> sprintf($user->lang['EXTENSION_DISABLED_AFTER_POSTING'], $attachment['extension'])
			);
		}

		if (!$denied)
		{
			$l_downloaded_viewed = $download_link = '';
			$display_cat = $extensions[$attachment['extension']]['display_cat'];

			if ($display_cat == ATTACHMENT_CATEGORY_IMAGE)
			{
				if ($attachment['thumbnail'])
				{
					$display_cat = ATTACHMENT_CATEGORY_THUMB;
				}
				else
				{
					if ($config['img_display_inlined'])
					{
						if ($config['img_link_width'] || $config['img_link_height'])
						{
							$dimension = @getimagesize($filename);

							// If the dimensions could not be determined or the image being 0x0 we display it as a link for safety purposes
							if ($dimension === false || empty($dimension[0]) || empty($dimension[1]))
							{
								$display_cat = ATTACHMENT_CATEGORY_NONE;
							}
							else
							{
								$display_cat = ($dimension[0] <= $config['img_link_width'] && $dimension[1] <= $config['img_link_height']) ? ATTACHMENT_CATEGORY_IMAGE : ATTACHMENT_CATEGORY_NONE;
							}
						}
					}
					else
					{
						$display_cat = ATTACHMENT_CATEGORY_NONE;
					}
				}
			}

			// Make some descisions based on user options being set.
			if (($display_cat == ATTACHMENT_CATEGORY_IMAGE || $display_cat == ATTACHMENT_CATEGORY_THUMB) && !$user->optionget('viewimg'))
			{
				$display_cat = ATTACHMENT_CATEGORY_NONE;
			}

			if ($display_cat == ATTACHMENT_CATEGORY_FLASH && !$user->optionget('viewflash'))
			{
				$display_cat = ATTACHMENT_CATEGORY_NONE;
			}

			$download_link = append_sid("{$phpbb_root_path}download/kb.$phpEx", 'id=' . $attachment['attach_id']);

			switch ($display_cat)
			{
				// Images
				case ATTACHMENT_CATEGORY_IMAGE:
					$l_downloaded_viewed = 'VIEWED_COUNT';
					$inline_link = append_sid("{$phpbb_root_path}download/kb.$phpEx", 'id=' . $attachment['attach_id']);
					$download_link .= '&amp;mode=view';

					$block_array += array(
						'S_IMAGE'		=> true,
						'U_INLINE_LINK'		=> $inline_link,
					);

					$update_count[] = $attachment['attach_id'];
				break;

				// Images, but display Thumbnail
				case ATTACHMENT_CATEGORY_THUMB:
					$l_downloaded_viewed = 'VIEWED_COUNT';
					$thumbnail_link = append_sid("{$phpbb_root_path}download/kb.$phpEx", 'id=' . $attachment['attach_id'] . '&amp;t=1');
					$download_link .= '&amp;mode=view';

					$block_array += array(
						'S_THUMBNAIL'		=> true,
						'THUMB_IMAGE'		=> $thumbnail_link,
					);
				break;

				// Windows Media Streams
				case ATTACHMENT_CATEGORY_WM:
					$l_downloaded_viewed = 'VIEWED_COUNT';

					// Giving the filename directly because within the wm object all variables are in local context making it impossible
					// to validate against a valid session (all params can differ)
					// $download_link = $filename;

					$block_array += array(
						'U_FORUM'		=> generate_board_url(),
						'ATTACH_ID'		=> $attachment['attach_id'],
						'S_WM_FILE'		=> true,
					);

					// Viewed/Heared File ... update the download count
					$update_count[] = $attachment['attach_id'];
				break;

				// Real Media Streams
				case ATTACHMENT_CATEGORY_RM:
				case ATTACHMENT_CATEGORY_QUICKTIME:
					$l_downloaded_viewed = 'VIEWED_COUNT';

					$block_array += array(
						'S_RM_FILE'			=> ($display_cat == ATTACHMENT_CATEGORY_RM) ? true : false,
						'S_QUICKTIME_FILE'	=> ($display_cat == ATTACHMENT_CATEGORY_QUICKTIME) ? true : false,
						'U_FORUM'			=> generate_board_url(),
						'ATTACH_ID'			=> $attachment['attach_id'],
					);

					// Viewed/Heared File ... update the download count
					$update_count[] = $attachment['attach_id'];
				break;

				// Macromedia Flash Files
				case ATTACHMENT_CATEGORY_FLASH:
					list($width, $height) = @getimagesize($filename);

					$l_downloaded_viewed = 'VIEWED_COUNT';

					$block_array += array(
						'S_FLASH_FILE'	=> true,
						'WIDTH'			=> $width,
						'HEIGHT'		=> $height,
					);

					// Viewed/Heared File ... update the download count
					$update_count[] = $attachment['attach_id'];
				break;

				default:
					$l_downloaded_viewed = 'DOWNLOAD_COUNT';

					$block_array += array(
						'S_FILE'		=> true,
					);
				break;
			}

			$l_download_count = (!isset($attachment['download_count']) || $attachment['download_count'] == 0) ? $user->lang[$l_downloaded_viewed . '_NONE'] : (($attachment['download_count'] == 1) ? sprintf($user->lang[$l_downloaded_viewed], $attachment['download_count']) : sprintf($user->lang[$l_downloaded_viewed . 'S'], $attachment['download_count']));

			$block_array += array(
				'U_DOWNLOAD_LINK'		=> $download_link,
				'L_DOWNLOAD_COUNT'		=> $l_download_count
			);
		}

		$template->assign_block_vars('_file', $block_array);

		$compiled_attachments[] = $template->assign_display('attachment_tpl');
	}

	$attachments = $compiled_attachments;
	unset($compiled_attachments);

	$tpl_size = sizeof($attachments);

	$unset_tpl = array();

	preg_match_all('#<!\-\- ia([0-9]+) \-\->(.*?)<!\-\- ia\1 \-\->#', $message, $matches, PREG_PATTERN_ORDER);

	$replace = array();
	foreach ($matches[0] as $num => $capture)
	{
		// Flip index if we are displaying the reverse way
		$index = ($config['display_order']) ? ($tpl_size-($matches[1][$num] + 1)) : $matches[1][$num];

		$replace['from'][] = $matches[0][$num];
		$replace['to'][] = (isset($attachments[$index])) ? $attachments[$index] : sprintf($user->lang['MISSING_INLINE_ATTACHMENT'], $matches[2][array_search($index, $matches[1])]);

		$unset_tpl[] = $index;
	}

	if (isset($replace['from']))
	{
		$message = str_replace($replace['from'], $replace['to'], $message);
	}

	$unset_tpl = array_unique($unset_tpl);

	// Needed to let not display the inlined attachments at the end of the post again
	foreach ($unset_tpl as $index)
	{
		unset($attachments[$index]);
	}
}

/**
* Get related articles 
* Maybe includes tags in function as should really show in article page
* Functioned instead so can use anywhere
*/
function handle_related_articles($article_id, $show_num = 5)
{
	global $phpbb_root_path, $phpEx, $db, $template, $user;
	
	$ra = request_var('ra', 0);
	
	// Get the tags first
	$sql = $db->sql_build_query('SELECT', array(
		'SELECT'	=> 't.tag_name_lc',
		'FROM'		=> array(
			KB_TAGS_TABLE => 't'),

		'WHERE'		=> "t.article_id = '" . $db->sql_escape($article_id) . "'",
	));
	$result = $db->sql_query($sql);
	
	$tags = array();	
	while($row = $db->sql_fetchrow($result))
	{
		$tags[] = "'" . $db->sql_escape($row['tag_name_lc']) . "'";
	}
	$db->sql_freeresult($result);
	
	// Check what prefix we need
	if (!empty($tags))
	{
		$num_tags = sizeof($tags);
		if ($num_tags == 1)
		{
			$tag = $tags[0];
		}
		else
		{
			$tag = implode(' OR ', $tags);
		}
		
		// Get the titles
		$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> 't.article_id, a.article_title',
			'FROM'		=> array(
				KB_TAGS_TABLE => 't'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM' => array(KB_TABLE => 'a'),
					'ON' => 't.article_id = a.article_id',
				),
			),
			'WHERE'		=> 't.article_id <> ' . $article_id . '
				AND a.article_status = ' . STATUS_APPROVED . '
				AND t.tag_name_lc = ' . $tag,	
		));
		$result = $db->sql_query_limit($sql, $show_num, $ra);
		
		$related_articles = array();	
		while($row = $db->sql_fetchrow($result))
		{
			$related_articles[$row['article_id']] = $row['article_title'];
		}
		$db->sql_freeresult($result);
		
		$articles = array_unique($related_articles);
		
		// Need to do it without limit to get the count :S
		$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> 't.article_id, a.article_title',
			'FROM'		=> array(
				KB_TAGS_TABLE => 't'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM' => array(KB_TABLE => 'a'),
					'ON' => 't.article_id = a.article_id',
				),
			),
			'WHERE'		=> 't.article_id <> ' . $article_id . '
				AND a.article_status = ' . STATUS_APPROVED . '
				AND t.tag_name_lc = ' . $tag,			
		));
		$result = $db->sql_query($sql);
		
		$articles_count = array();	
		while($count = $db->sql_fetchrow($result))
		{
			$articles_count[] = $count['article_id'];
		}
		$db->sql_freeresult($result);		
		$articles_count = array_unique($articles_count);		
		$articles_found = sizeof($articles_count);
		
		foreach ($articles as $article_id_ra => $article_title_ra)
		{
			$template->assign_block_vars('related_articles', array(
				'U_VIEW_ARTICLE'	=> append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_id_ra"),
				'TITLE'				=> $article_title_ra,
			));
		}
		
		// Generate Pagination
		$template->assign_vars(array(
			'KB_PAGINATION'	=> kb_generate_pagination(append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_id"), $articles_found, $show_num, $ra, 'ra', true),
			'KB_PAGE_NUMBER'	=> on_page($articles_found, $show_num, $ra),
			'KB_TOTAL_RA' 		=> $articles_found,
			'KB_S_TOTAL_RA'	=> ($articles_found == 1) ? $user->lang['MATCH_FOUND'] : $user->lang['MATCHS_FOUND'],
		));
		
	}
}

/**
* Get/Add/Delete latest articles of cat
* Using this instead of doing multiple sql queries which will increase server load, as it could be running more than 25 extra queries depending how many cats they had perpage
* Should actually work quickly but more likely to fail if a manual delete happenes from database as doesn't check topic is there
*/
function handle_latest_articles($mode, $cat_id, $data = false)
{
	global $phpbb_root_path, $phpEx, $db;

	//Need random word/combination that is very unlikely going to appear in a message for explode
	$combination = 'kbdev69htygfhdslo908'; //That should do me this can't be changed unless still in alpha as will stop furture versions working
	
	switch ($mode)
	{
		case 'get':
			$article_ids 	= explode($combination, $data['LATEST_IDS']);
			$article_titles = explode($combination, $data['LATEST_TITLES']);
			
			// Goes article_id => $article_title
			$article_links = array();
			
			// Got to limit to 4 think that should be enough
			if (!empty($article_ids))
			{				
				if (isset($article_ids[1]))
				{
					$article_links[] = '<a href="' . append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_ids[1]") . '">' . $article_titles[1] . '</a>';
				}
				
				if (isset($article_ids[2]))
				{
					$article_links[] = '<a href="' . append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_ids[2]") . '">' . $article_titles[2] . '</a>';
				}
				
				if (isset($article_ids[3]))
				{
					$article_links[] = '<a href="' . append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_ids[3]") . '">' . $article_titles[3] . '</a>';
				}
				
				if (isset($article_ids[4]))
				{
					$article_links[] = '<a href="' . append_sid("{$phpbb_root_path}kb.$phpEx", "a=$article_ids[4]") . '">' . $article_titles[4] . '</a>';
				}
			}	
			
			return implode(', ', $article_links);
		break;
		
		case 'add':
			$sql = $db->sql_build_query('SELECT', array(
				'SELECT'	=> 'c.latest_ids, c.latest_titles',
				'FROM'		=> array(
					KB_CATS_TABLE => 'c'),

				'WHERE'		=> 'cat_id = ' . $cat_id,
			));
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			$article_ids 	= explode($combination, $row['latest_ids']);
			$article_titles = explode($combination, $row['latest_titles']);
			
			// only start checking if empty if not start a fresh
			if (isset($article_ids[1]))
			{				
				if (isset($article_ids[1]))
				{
					$use = 1;
				}
				
				if (isset($article_ids[2]))
				{
					$use = 2;
				}
				
				if (isset($article_ids[3]))
				{
					$use = 3;
				}
				
				if (isset($article_ids[4]))
				{
					$use = 4;
				}
				
				if ($use == 4)
				{
					$latest_ids 	= str_replace($combination . $article_ids[4], '', $row['latest_ids']);
					$latest_titles 	= str_replace($combination . $article_titles[4], '', $row['latest_titles']);
					
					$sql_ary = array(
						'latest_ids'		=> $combination . $data['ARTICLE_ID'] . $latest_ids,
						'latest_titles'		=> $combination . $data['ARTICLE_TITLE'] . $latest_titles,
					);
				}
				else
				{
					$sql_ary = array(
						'latest_ids'		=> $combination . $data['ARTICLE_ID'] . $row['latest_ids'],
						'latest_titles'		=> $combination . $data['ARTICLE_TITLE'] . $row['latest_titles'],
					);
				}
				
				$sql = 'UPDATE ' . KB_CATS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
					WHERE cat_id = '" . $db->sql_escape($cat_id) . "'";
				$db->sql_query($sql);
			}
			else
			{
				$sql_ary = array(
					'latest_ids'		=> $combination . $data['ARTICLE_ID'],
					'latest_titles'		=> $combination . $data['ARTICLE_TITLE'],
				);
				
				$sql = 'UPDATE ' . KB_CATS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
					WHERE cat_id = '" . $db->sql_escape($cat_id) . "'";
				$db->sql_query($sql);
			}	
		break;
		
		case 'delete':
			// Call delete here only used it article id thats getting deleted is is recent articles for that cat
		break;
	}
}

/**
* Pagination routine, generates page number sequence
* tpl_prefix is for using different pagination blocks at one page
* for kb, change start to what you want so can be used multiple times on a page :)
*/
function kb_generate_pagination($base_url, $num_items, $per_page, $start_item, $pass, $add_prevnext_text = false, $tpl_prefix = 'KB_')
{
	global $template, $user;

	// Make sure $per_page is a valid value
	$per_page = ($per_page <= 0) ? 1 : $per_page;

	$seperator = '<span class="page-sep">' . $user->lang['COMMA_SEPARATOR'] . '</span>';
	$total_pages = ceil($num_items / $per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}

	$on_page = floor($start_item / $per_page) + 1;
	$url_delim = (strpos($base_url, '?') === false) ? '?' : '&amp;';

	$page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . '">1</a>';

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - 4), $total_pages - 5);
		$end_cnt = max(min($total_pages, $on_page + 4), 6);

		$page_string .= ($start_cnt > 1) ? ' ... ' : $seperator;

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}$pass=" . (($i - 1) * $per_page) . '">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}

		$page_string .= ($end_cnt < $total_pages) ? ' ... ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;

		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}$pass=" . (($i - 1) * $per_page) . '">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}

	$page_string .= ($on_page == $total_pages) ? '<strong>' . $total_pages . '</strong>' : '<a href="' . $base_url . "{$url_delim}$pass=" . (($total_pages - 1) * $per_page) . '">' . $total_pages . '</a>';

	if ($add_prevnext_text)
	{
		if ($on_page != 1)
		{
			$page_string = '<a href="' . $base_url . "{$url_delim}$pass=" . (($on_page - 2) * $per_page) . '">' . $user->lang['PREVIOUS'] . '</a>&nbsp;&nbsp;' . $page_string;
		}

		if ($on_page != $total_pages)
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . "{$url_delim}$pass=" . ($on_page * $per_page) . '">' . $user->lang['NEXT'] . '</a>';
		}
	}

	$template->assign_vars(array(
		$tpl_prefix . 'BASE_URL'		=> $base_url,
		'A_' . $tpl_prefix . 'BASE_URL'	=> addslashes($base_url),
		$tpl_prefix . 'PER_PAGE'		=> $per_page,

		$tpl_prefix . 'PREVIOUS_PAGE'	=> ($on_page == 1) ? '' : $base_url . "{$url_delim}$pass=" . (($on_page - 2) * $per_page),
		$tpl_prefix . 'NEXT_PAGE'		=> ($on_page == $total_pages) ? '' : $base_url . "{$url_delim}$pass=" . ($on_page * $per_page),
		$tpl_prefix . 'TOTAL_PAGES'		=> $total_pages,
	));

	return $page_string;
}

/**
* Display Categories
*/
function kb_display_cats($root_data = '')
{
	global $db, $auth, $user, $template;
	global $phpbb_root_path, $phpEx, $config;

	$cats = $cat = $subcats = $cat_ids = $visible_subcats = array();
	$parent_id = 0;
	$sql_where = '';
	
	if($root_data == '')
	{
		$root_data = array('cat_id' => 0);
	}
	else
	{
		$sql_where = 'c.left_id > ' . $root_data['left_id'] . ' AND c.left_id < ' . $root_data['right_id'];
	}	
	
	$sql = $db->sql_build_query('SELECT', array(
		'SELECT'	=> 'c.*',
		'FROM'		=> array(
			KB_CATS_TABLE => 'c'),

		'WHERE'		=> $sql_where,

		'ORDER_BY'	=> 'c.left_id',
	));

	$result = $db->sql_query($sql);
	
	while($row = $db->sql_fetchrow($result))
	{
		$cat_id = $row['cat_id'];
		$cat_ids[] = $cat_id;
		
		if ($row['parent_id'] == $root_data['cat_id'])
		{
			// Direct child of current branch
			$parent_id = $cat_id;
			$cat[] = $row;
			$cats[$cat_id] = $row;
		}
		else
		{
			$cats[$parent_id]['cat_articles'] += $row['cat_articles'];
			$subcats[$parent_id][$cat_id]['name'] = $row['cat_name'];
			$subcats[$parent_id][$cat_id]['children'] = array();

			if (isset($subforums[$parent_id][$row['parent_id']]))
			{
				$subcats[$parent_id][$row['parent_id']]['children'][] = $cat_id;
			}
		}

	}
	$db->sql_freeresult($result);
	
	for ($i = 0; $i < count($cat_ids); $i += 3)
	{
		$template->assign_block_vars('cat_row', array());

		for ($j = $i; $j < ($i + 3); $j++)
		{
				
			if ($j >= count($cats))
			{
				$template->assign_block_vars('cat_row.no_cat', array());
				continue;
			}
			
			$cat_id = $cat[$j]['cat_id'];
			$subcats_list = $s_subcats_list = array();
			$visible_subcats[$cat_id] = 0;
			
			if(isset($subcats[$cat_id]))
			{
				foreach($subcats[$cat_id] as $subcat_id => $subcat)
				{
					$visible_subcats[$cat_id]++;
					$subcats_list[] = array(
						'name' => $subcat['name'],
						'link' => append_sid("{$phpbb_root_path}kb.$phpEx", "c=$subcat_id"),
					);
					$s_subcats_list[] = '<a href="' . append_sid("{$phpbb_root_path}kb.$phpEx", "c=$subcat_id") . '">' . $subcat['name'] . '</a>';
				}
				$s_subcats_list = implode(', ', $s_subcats_list);
			}
			
			$folder_img = (sizeof($subcats_list)) ? 'forum_read_subforum' : 'forum_read';
			
			$late_article = array(
				'LATEST_IDS'		=> $cats[$cat_id]['latest_ids'],
				'LATEST_TITLES'		=> $cats[$cat_id]['latest_titles'],
			);
			$latest_articles = handle_latest_articles('get', $cat_id, $late_article);
			
			$template->assign_block_vars('cat_row.cats', array(
				'S_SUBFORUMS'			=> (sizeof($subcats_list)) ? true : false,
			
				'CAT_ID'				=> $cats[$cat_id]['cat_id'],
				'CAT_NAME'				=> $cats[$cat_id]['cat_name'],
				'CAT_DESC'				=> generate_text_for_display($cats[$cat_id]['cat_desc'], $cats[$cat_id]['cat_desc_uid'], $cats[$cat_id]['cat_desc_bitfield'], $cats[$cat_id]['cat_desc_options']),
				'ARTICLES'				=> $cats[$cat_id]['cat_articles'],
				'CAT_FOLDER_IMG'		=> $user->img($folder_img, $cats[$cat_id]['cat_name']),
				'CAT_FOLDER_IMG_SRC'	=> $user->img($folder_img, $cats[$cat_id]['cat_name'], false, '', 'src'),
				'CAT_FOLDER_IMG_ALT'	=> $cats[$cat_id]['cat_name'],
				'CAT_IMAGE'				=> ($cats[$cat_id]['cat_image']) ? '<img src="' . $phpbb_root_path . $cats[$cat_id]['cat_image'] . '" alt="' . $user->lang['KB_CATS'] . '" />' : '',
				'CAT_IMAGE_SRC'			=> ($cats[$cat_id]['cat_image']) ? $phpbb_root_path . $cats[$cat_id]['cat_image'] : '',
				'SUBCATS'				=> $s_subcats_list,
				'LATEST_ARTICLE'		=> $latest_articles,

				'L_SUBCAT'				=> ($visible_subcats[$cat_id] == 1) ? $user->lang['SUBCAT'] : $user->lang['SUBCATS'],
				'U_VIEWCAT'				=> append_sid("{$phpbb_root_path}kb.$phpEx", "c=$cat_id")
			));		
		}
	}	
	return;
}

// Generate edit string, used so many places i found it better to put it here
function gen_kb_edit_string($article_id, $last_edit_id, $article_time, $edit_time)
{
	global $db, $user, $phpbb_root_path, $phpEx;
	
	if($last_edit_id && $edit_time != $article_time)
	{
		$sql = "SELECT e.edit_id, u.user_id, u.username, u.user_colour
				FROM " . KB_EDITS_TABLE . " e, " . USERS_TABLE . " u
				WHERE e.edit_id = $article_id
				AND u.user_id = $last_edit_id";
		$result = $db->sql_query($sql);
		$edit_data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$l_edit = sprintf($user->lang['KB_EDITED_BY'], '<a href="' . append_sid("{$phpbb_root_path}kb.$phpEx", "e=$article_id") . '">', '</a>', get_username_string('full', $edit_data['user_id'], $edit_data['username'], $edit_data['user_colour']), $user->format_date($edit_time, false, true));
		return $l_edit;
	}
	
	return '';
}

// Show what user can do, $cat_id not used at the moment as nothing is local!
function gen_kb_auth_level($cat_id = false)
{
	global $template, $auth, $user, $config;

	$rules = array(
		($auth->acl_get('u_kb_add')) ? sprintf($user->lang['KB_PERM_ADD'], $user->lang['KB_CAN']) : sprintf($user->lang['KB_PERM_ADD'], $user->lang['KB_NOT']),
		($auth->acl_get('u_kb_comment')) ? sprintf($user->lang['KB_PERM_COM'], $user->lang['KB_CAN']) : sprintf($user->lang['KB_PERM_COM'], $user->lang['KB_NOT']),
		($user->data['is_registered'] && $auth->acl_gets('u_kb_edit', 'm_kb')) ? sprintf($user->lang['KB_PERM_EDIT'], $user->lang['KB_CAN']) : sprintf($user->lang['KB_PERM_EDIT'], $user->lang['KB_NOT']),
		($user->data['is_registered'] && $auth->acl_gets('u_kb_delete', 'm_kb')) ? sprintf($user->lang['KB_PERM_DEL'], $user->lang['KB_CAN']) : sprintf($user->lang['KB_PERM_DEL'], $user->lang['KB_NOT']),
		($auth->acl_get('u_kb_rate')) ? sprintf($user->lang['KB_PERM_RATE'], $user->lang['KB_CAN']) : sprintf($user->lang['KB_PERM_RATE'], $user->lang['KB_NOT']),
	);

	if ($config['kb_allow_attachments'])
	{
		$rules[] = ($auth->acl_get('u_kb_attach')) ? sprintf($user->lang['KB_PERM_ATTACH'], $user->lang['KB_CAN']) : sprintf($user->lang['KB_PERM_ATTACH'], $user->lang['KB_NOT']);
	}
	
	$rules[] = ($auth->acl_get('u_kb_download')) ? sprintf($user->lang['KB_PERM_DOWN'], $user->lang['KB_CAN']) : sprintf($user->lang['KB_PERM_DOWN'], $user->lang['KB_NOT']);

	foreach ($rules as $rule)
	{
		$template->assign_block_vars('rules', array('RULE' => $rule));
	}

	return;
}

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
	);

	return $versions;
}

?>