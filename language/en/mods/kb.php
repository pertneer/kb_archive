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

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Common variables
$lang = array_merge($lang, array(
	'RETURN_KB'				=>	'%sBrowse back to the Knowledge Base Index%s',
	'KB' 					=> 'Knowledge Base',
	'KB_NOT_ENABLE' 		=> 'Knowledge Base isn\'t enabled at the moment please come back later!',
	'ARTICLE_TITLE' 		=> 'Article title',
	'ARTICLE_DESC' 			=> 'Article description',
	'ARTICLE_DESC_EXPLAIN' 	=> 'If you wish to write a description for your article please do so. The character limit is 300 and BBCodes are enabled.',
	'TAGS' 					=> 'Tags',
	'TAGS_EXPLAIN' 			=> 'You can specify which tags you want your article to be found under (if any). Seperate each tag by a comma.',
	'VIEW_ARTICLE' 			=> 'View article',
	'VIEW_CAT' 				=> 'View category',
	'CHANGE_STATUS' 		=> 'Alter status',
	'ARTICLE_HISTORY' 		=> 'View article history',
	'ARTICLE_UNAPPROVED' 	=> 'This article has not been approved by a moderator yet.',
	'ARTICLE_CONTENT' 		=> 'Article content',
	'ARTICLES' 				=> 'Articles',
	'COMMENTS'				=> 'Comments',
	'KB_CATS' 				=> 'Categories',
	'SUBCAT' 				=> 'Subcategory',
	'SUBCATS' 				=> 'Subcategories',
	'KB_INDEX' 				=> 'Knowledge Base Index',
	'NO_ARTICLES' 			=> 'There are no articles in this category.',
	'NO_COMMENTS' 			=> 'There are no comments to this article.',
	'NO_CATS' 				=> 'The Knowledge Base has no categories.',
	'ARTICLE_AUTHOR' 		=> 'Article author',
	'KB_STATS'				=> 'Knowledge Base Statistics',
	'KB_LATEST'				=> 'Latest Article',
	'KB_RAN_ART'			=> 'Random Article',
	
	// Search vars
	'NO_SEARCH_MATCHES'			=> 'No matches was found for your search.',
	'SEARCH_KB'					=> 'Knowledge Base Search',
	'KB_SORT_ATIME'				=> 'Article post time',
	'KB_SORT_ETIME'				=> 'Last edit time',
	'KB_SORT_TITLE'				=> 'Article title',
	'KB_SORT_AUTHOR'			=> 'Article author',
	'KB_SORT_CAT'				=> 'Category',
	'KB_KEYWORDS_EXPLAIN'		=> 'Type in the keywords you want to search for here. Use a space to search for more keywords and decide if you want to search for all terms or just some of them. Special characters like + and - is not supported in this search.',
	'KB_SEARCH_CATS'			=> 'Search categories',
	'KB_SEARCH_CATS_EXPLAIN'	=> 'Determine which categories you want to search in, leave blank to search in all categories.',
	'KB_SEARCH_TITLE_TEXT_DESC' => 'Title, description and content of article.',
	'KB_SEARCH_TITLE'			=> 'Title of article.',
	'KB_SEARCH_TEXT'			=> 'Content of article.',
	'KB_SEARCH_DESC'			=> 'Description of article.',
));

// Article Posting
$lang = array_merge($lang, array(
	'KB_NO_ARTICLE'						=> 'There is no article with that id.',
	'KB_NO_EDIT_ARTICLE'				=> 'There is no edits for this article.',
	'KB_ADD_ARTICLE' 					=> 'Add article',
	'KB_EDIT_ARTICLE' 					=> 'Edit article',
	'KB_DELETE_ARTICLE' 				=> 'Delete article',
	'KB_ADD_COMMENT' 					=> 'Add comment',
	'KB_EDIT_COMMENT' 					=> 'Edit comment',
	'KB_DELETE_COMMENT' 				=> 'Delete comment',
	'DELETE_ARTICLE' 					=> 'Please confirm that you want to delete this article? ',
	'DELETE_ARTICLE_CONFIRM' 			=> 'You will delete all old history data, all tracking information, all tags and any comments associated with this article.',
	'DELETE_COMMENT' 					=> 'Please confirm that you want to delete this comment? ',
	'DELETE_COMMENT_CONFIRM' 			=> 'Warning: There is no way to get this comment back when you have deleted it.',
	'KB_USER_CANNOT_READ'				=>	'You don\'t have sufficient permission to read this article.',
	'KB_LOGIN_EXPLAIN_ADD'				=>	'You need to login in order to add an article.',
	'KB_LOGIN_EXPLAIN_EDIT'				=>	'You need to login in order to edit this article.',
	'KB_LOGIN_EXPLAIN_DELETE'			=>	'You need to login in order to delete this article.',
	'KB_LOGIN_EXPLAIN_READ'				=>	'You need to login in order to read this article.',
	'KB_LOGIN_EXPLAIN_COMMENT_ADD'		=>	'You need to login in order to add a comment.',
	'KB_LOGIN_EXPLAIN_COMMENT_EDIT'		=>	'You need to login in order to edit this comment.',
	'KB_LOGIN_EXPLAIN_COMMENT_DELETE'	=>	'You need to login in order to delete this comment.',
	'KB_ADDED'							=>	'The article has been successfully added and is now awaiting approval by a moderator. You can follow the status of your article %shere%s.',
	'KB_ADDED_WA'						=> 'The article has been successfully addded and can now be found in the category you chose. You can view it %shere%s.',
	'KB_EDITED'							=>	'The article has been successfully edited and you can now view the updated article %shere%s.',
	'KB_DELETED' 						=> 'The article has been successfully deleted along with all the accompanying article data',
	'KB_COMMENT_ADDED'					=>	'The comment has been successfully added and is now visible in the article %shere%s.',
	'KB_COMMENT_EDITED'					=>	'The comment has been successfully edited and you can now view the updated comment %shere%s.',
	'KB_COMMENT_DELETED' 				=> 'The comment has been successfully deleted.',
	'RETURN_KB_CAT'						=>	'%sReturn to the last category visited%s',
	'RETURN_KB_ARTICLE'					=>	'%sReturn to the last article visited%s',
	'KB_ICON'							=>	'Article Icon',
	'KB_NO_CATEGORY' 					=> 'There is no such category.',
	'KB_NO_COMMENT' 					=> 'There is no such comment.',
	'KB_EMPTY_TITLE' 					=> 'You have to specify a title for your article.',
	'COMMENT_EMPTY_TITLE' 				=> 'You have to specify a title for your comment.',
	'COMMENT_TITLE' 					=> 'Comment subject',
	
	'KB_USER_CANNOT_EDIT' 				=> 'You are not allowed to edit this article.',
	'KB_USER_CANNOT_ADD' 				=> 'You are not allowed to add an article.',
	'KB_USER_CANNOT_DELETE' 			=> 'You are not allowed to delete this article.',
	'KB_USER_CANNOT_COMMENT_EDIT' 		=> 'You are not allowed to edit this comment.',
	'KB_USER_CANNOT_COMMENT_ADD' 		=> 'You are not allowed to add a comment.',
	'KB_USER_CANNOT_COMMENT_DELETE' 	=> 'You are not allowed to delete this comment.',
	
	// Article viewing
	'KB_EDITED_BY' 			=> 'This article was last %1$sedited%2$s by %3$s on %4$s',
	'KB_COMMENT_EDITED_BY' 	=> 'This comment was last edited by %1$s on %2$s',
	'KB_NO_PERM_RATE' 		=> 'You do not have permission to rate this article.',
	'KB_HAS_RATED' 			=> 'You have already rated this article and cannot rate it twice.',
	'RATED_THANKS' 			=> 'Thank you for rating the article.',
	'STARS_OUT_OF' 			=> 'stars out of',
	'CURRENT_RATING_S' 		=> 'The article is currently rated with %1$s stars and %2$s vote has been cast.',
	'CURRENT_RATING_P' 		=> 'The article is currently rated with %1$s stars and %2$s votes have been cast.',
	
	'SEARCH_ARTICLES' 		=> 'Search Articles',
	'RATINGS' 				=> 'Rate this article',
	'DESCRIPTION'			=> 'Description',
	
	// ACP
	'KB_ENABLE'				=> 'Enable the Knowledge Base',
	'KB_ALLOW_ATTACH'		=> 'Allow attachments to be used',
	'KB_ALLOW_SUB'			=> 'Allow users to subcribe to articles',
	'KB_ALLOW_BOOK'			=> 'Allow users to bookmark articles',
	'KB_ART_PER_PAGE'		=> 'How many articles per page',
	'KB_COM_PER_PAGE'		=> 'How many comments per page',
	
	'ACP_KB_POST_SETTINGS'	=> 'Knowledge Base Post Settings',
	'KB_ALLOW_SIG'			=> 'Allow signitures to be used',
	'KB_ALLOW_BBCODE'		=> 'Allow bbcodes in articles',
	'KB_ALLOW_SMILES'		=> 'Allow smiles in articles',
	'KB_ALLOW_FLASH'		=> 'Allow flash in articles',
	'KB_ALLOW_LINKS'		=> 'Allow links in articles',	
	
	'KB_MANAGEMENT'				=> 'Manage Knowledge Base',
	'UNINSTALL'					=> 'Uninstall KB',
	'RUN_NOW'					=> 'Run Now',
	
	'VERSION_CHECK'				=> 'Version check',
	'VERSION_CHECK_EXPLAIN'		=> 'Checks to see if the version of Knowledge Base you are currently running is up to date.',
	'VERSION_NOT_UP_TO_DATE'	=> 'Your version of Knowledge Base is not up to date. Please continue the update process.',
	'VERSION_NOT_UP_TO_DATE_ACP'=> 'Your version of Knowledge Base is not up to date.<br />Below you will find a link to the release announcement for the latest version as well as instructions on how to perform the update.',
	'VERSION_UP_TO_DATE'		=> 'Your installation is up to date, no updates are available for your version of Knowledge Base. You may want to continue anyway to perform a file validity check.',
	'VERSION_UP_TO_DATE_ACP'	=> 'Your installation is up to date, no updates are available for your version of Knowledge Base. You do not need to update your installation.',
	'VIEWING_FILE_CONTENTS'		=> 'Viewing file contents',
	'VIEWING_FILE_DIFF'			=> 'Viewing file differences',

	'WRONG_INFO_FILE_FORMAT'	=> 'Wrong info file format',
	
	'UPDATE_INSTRUCTIONS'			=> '

		<h1>Release announcement</h1>

		<p>Please read <a href="%1$s" title="%1$s"><strong>the release announcement for the latest version</strong></a> before you continue your update process, it may contain useful information. It also contains a change log.</p>

		<br />

		<h1>How to update your installation</h1>

		<p>The recommended way of updating your installation are listed below:</p>

		<ul style="margin-left: 20px; font-size: 1.1em;">
			<li>Download the latest package - <a href="%2$s" title="%2$s">direct link here</a><br /><br /></li>
			<li>Unpack the archive.<br /><br /></li>
			<li>Upload all the files to your phpbb root folder overwriting the existing files.<br /><br /></li>
		</ul>

		<p>Once uploaded the Knowledge Base database will need updating, if your version is out of date the Knowledge Base will disable itself to normal users until you run the installation<br /><br />
		<strong><a href="%3$s" title="%3$s">Now start the update process by pointing your browser to the Knowledge Base file</a>.</strong><br />
		<br />
		You will then be guided through the update process. You will be notified once the update is complete.
		</p>
	',
	'CURRENT_VERSION'		=> 'Current version',
	'LATEST_VERSION'		=> 'Latest version',
	
	'CAT_INDEX'				=> 'Knowledge Base Index',
	'CREATE_CAT'			=> 'Create category',
	'CAT_ADMIN'				=> 'Category administration',
	'SELECT_CAT'			=> 'Select category',
	'CAT_SETTINGS'			=> 'Category settings',
	'CAT_PARENT'			=> 'Category parent',
	'CAT_NAME'				=> 'Category name',
	'CAT_DESC'				=> 'Category description',
	'CAT_IMAGE'				=> 'Category image',
	'CAT_IMAGE_EXPLAIN'		=> 'Relevent to the phpbb root path',
	
	'CAT_CREATED'			=> 'Category created',
	'CAT_UPDATED'			=> 'Category updated',
	'NO_CAT'				=> 'No category available',
	'EDIT_CAT'				=> 'Edit category',
	'CAT_NAME_EMPTY'		=> 'Category name empty',
	'CAT_DESC_TOO_LONG'		=> 'Category description too long',
	
	// Install stuff
	'KB_UPDATE_UMIL'		=> 'Please download the latest UMIL (Unified MOD Install Library) from: <a href=\'http://www.phpbb.com/mods/umil/\'>phpBB.com/mods/umil</a>',
	'KB_INSTALLED'			=> 'Knowledge Base Mod has been successfully installed, you may now proceed to the ACP to set permissions for it and enable it.',
	'KB_UNINSTALLED'		=> 'Knowledge Base Mod has been successfully uninstalled',
	'KB_UPDATED'			=> 'Your Knowledge Base Mod has been updated to the latest version, check the ACP for confirmation of this.',
	'INSTALL_KB'			=> 'Install Knowledge Base Mod',
	'UNINSTALL_KB'			=> 'Uninstall Knowledge Base Mod',
	'INSTALL_KB_CONFIRM'	=> 'Please confirm that you want to install the Knowledge Base Mod. Before starting the installation please take a full backup of your forum and please make sure you have downloaded the latest version of this mod. <br />This installation script uses the latest version of UMIL (Unified Mod Install Library), please make sure you have this installed in /phpbb root/umil/umil.php on your server. The script can be found at <a href=\'http://www.phpbb.com/mods/umil/\'>phpBB.com/mods/umil</a>. <br /><br /><b>WARNING: THIS EARLY VERSION OF KB MOD IS NOT INTENDED FOR USE ON LIVE FORUMS!</b>',
	'UNINSTALL_KB_CONFIRM'	=> 'Please confirm that you want to uninstall the Knowledge Base Mod.',
	'UPDATE_KB'				=> 'Update Knowledge Base Mod',
	'UPDATE_KB_CONFIRM'		=> 'Please confirm that you want to update the Knowledge Base Mod to the latest version. Make sure that your phpBB is also updated to the latest version, or this version of KB Mod might not work. Furthermore you should make sure that you have a complete backup of your forum before you begin. Like the installer, this script uses the latest version of UMIL (Unified Mod Install Library), so please make sure you have this installed in /phpbb root/umil/umil.php on your server. The script can be found at <a href=\'http://www.phpbb.com/mods/umil/\'>phpBB.com/mods/umil</a>. <br /><br /><b>WARNING: THIS EARLY VERSION OF KB MOD IS NOT INTENDED FOR USE ON LIVE FORUMS!</b>',

	// UCP stuff
	'KB_SUCCESS_SUBSCRIBE_ADD'		=> 'You have successfully marked the article for notification with the specified settings.',
	'KB_SUCCESS_SUBSCRIBE_EDIT'		=> 'You have successfully edited the notification settings for the article.',
	'KB_SUCCESS_SUBSCRIBE_DELETE'	=> 'You have successfully deleted the notification settings for the specified article.',
	'KB_SUCCESS_BOOKMARK_ADD'		=> 'You have successfully bookmarked the article.',
	'KB_SUCCESS_BOOKMARK_DELETE'	=> 'You have successfully removed the bookmark from the article.',
	'KB_NOTIFY_ON'					=> 'Notify on',
	'KB_NOTIFY_BY'					=> 'Notify by',
	'KB_NO_NOTIFY'					=> 'Don\'t notify me',
	'KB_NOTIFY_EDIT_CONTENT'		=> 'Notify me when the article content is edited',
	'KB_NOTIFY_EDIT_ALL'			=> 'Notify me on all edits',
	'KB_NOTIFY_COMMENT'				=> 'Notify me when someone comments the article',
	'KB_NOTIFY_AUTHOR_COMMENT'		=> 'Notify me when the author comments the article',
	'KB_NOTIFY_MOD_COMMENT_GLOBAL'	=> 'Notify me when a moderator comments the article',
	'KB_NOTIFY_UCP'					=> 'Notify me via the User Control Panel',
	'KB_NOTIFY_MAIL'				=> 'Notify me via E-mail',
	'KB_NOTIFY_PM'					=> 'Notify me with a private message',
	'KB_NOTIFY_POPUP'				=> 'Notify me with a popup',
	'KB_SUBSCRIBE_STATUS_ADD'		=> 'You are adding a notification for the article %s.',
	'KB_SUBSCRIBE_STATUS_EDIT'		=> 'You are editing the notification settings for the article %s.',
	'KB_DELETE_SUBSCRIBE'			=> 'Delete notification settings',
	'KB_DELETE_SUBSCRIBE_CONFIRM'	=> 'Please confirm that you want to delete the notification settings for this article.',
	'KB_DELETE_BOOKMARK'			=> 'Remove bookmark',
	'KB_DELETE_BOOKMARK_CONFIRM'	=> 'Please confirm that you want to remove the bookmark for this article.',
	'KB_SUBSCRIBED_ARTICLES'		=> 'Subscribed articles',
	'KB_NO_SUBSCRIBED_ARTICLES'		=> 'You are currently not subscribed to any articles.',
	'KB_UNSUBSCRIBE_MARKED'			=> 'Unsubscribe marked',
	'KB_BOOKMARKED_ARTICLES'		=> 'Bookmarked articles',
	'KB_NO_BOOKMARKED_ARTICLES'		=> 'You have not bookmarked any articles',
	'KB_UNBOOKMARK_MARKED'			=> 'Remove bookmarks',
	'APPROVED_ARTICLES'				=> 'Approved articles',
	'WAITING_ARTICLES'				=> 'Articles awaiting approval',
	'WAITING_MOD_COMMENTS'			=> 'Unread moderator comments',
	'SEARCH_YOUR_ARTICLES'			=> 'Search your articles',
	'ARTICLE_STATUS_PAGE'			=> 'View the article status page',
	'YOUR_KB_DETAILS'				=> 'Your activity in the knowledge base',
	'RULE'							=> 'Rule',
	'RELATED_ARTICLES'				=> 'Related Articles',
	'EMAIL_ARTICLE'					=> 'Email Article',
	'SOCIAL_BOOKMARK'				=> 'Social Bookmark',
	'EXPORT_ARTICLE'				=> 'Export Article',
	'COMMENT_DELETED'				=> 'Your comment has been deleted.',
	'ARTICLES_VIEW'					=> 'This article has been viewed %1$s time',
	'ARTICLES_VIEWS'				=> 'This article has been viewed %1$s times',
	'LATEST_ARTICLES'				=> 'Latest articles',
	'NO_RELATED_ARTICLES'			=> 'No related articles have been found.',
	'MATCH_FOUND'					=> 'match found',
	'MATCHS_FOUND'					=> 'matches found',
	
	// Social bookmarking
	'THIS'							=> 'this',
	'BLOGGER'						=> 'Blogger',
	'DELICIOUS'						=> 'Delicious',
	'DIGG'							=> 'Digg',
	'FACEBOOK'						=> 'Facebook',
	'FRIEND_FEED'					=> 'Friend Feed',
	'GOOGLE'						=> 'Google',
	'LINKED_IN'						=> 'Linked In',
	'LIVE'							=> 'Live',
	'MIXX'							=> 'Mixx',
	'MYSPACE'						=> 'MySpace',
	'NETVIBES'						=> 'Netvibes',
	'REDDIT'						=> 'Reddit',
	'STUMBLE_UPON'					=> 'Stumble Upon',
	'TECHNORATI'					=> 'Technorati',
	'TWITTER'						=> 'Twitter',
	'WORDPRESS'						=> 'Wordpress',
	
	// Permission stuff
	'KB_NOT'			=> '<strong>cannot</strong>',
	'KB_CAN'			=> '<strong>can</strong>',
	'KB_PERM_ADD'		=> 'You %1$s start new articles',
	'KB_PERM_COM'		=> 'You %1$s comment on articles',
	'KB_PERM_EDIT'		=> 'You %1$s edit your articles/comments',
	'KB_PERM_DEL'		=> 'You %1$s delete your articles/comments',
	'KB_PERM_RATE'		=> 'You %1$s rate articles',
	'KB_PERM_ATTACH'	=> 'You %1$s use attachments in your articles/comments',
	'KB_PERM_DOWN'		=> 'You %1$s download attachments',
	
	'KB_PERMISSIONS'	=> 'Knowledge Base Permissions',
	
	// Notify stuff
	'KB_NOTIFY_POPUP'			=> 'Notification of article update',
	'KB_NOTIFY_POPUP_EXPLAIN'	=> 'This popup has been generated because an update has been submitted to the article %s.',
	'CLICK_VIEW_ARTICLE'		=> 'Click %shere%s to view the article.',
	'NOTIFY_PM_SUBJECT'			=> 'Notification from Knowledge Base Mod',
	
	'NO_ARTICLES_CREATED'		=> 'No articles have been created yet!',
	
	// Stats
	'TOTAL_CATS'		=> 'Total categories',
	'TOTAL_ARTICLES'	=> 'Total articles',
	'TOTAL_COMMENTS'	=> 'Total comments',
	'KB_LAST_UPDATE'	=> 'Last updated',
	
	// MCP Stuff
	'NO_MCP_PERM'				=> 'You don\'t have permission to access the MCP for the Knowledge Base',
	'KB_QUEUED_ARTICLES'		=> 'Articles awaiting approval',
	'KB_NO_QUEUED_ARTICLES'		=> 'There are no articles awaiting approval.',
	'ARTICLE_STATUS'			=> 'Article status',
	'KB_STATUS_APPROVED'		=> 'Approved',
	'KB_STATUS_UNREVIEW'		=> 'Not viewed',
	'KB_STATUS_DISAPPROVED'		=> 'Not approved',
	'KB_STATUS_ONHOLD'			=> 'On hold',
	'KB_APPROVED_ARTICLES'		=> 'Approved articles',
	'KB_NO_APPROVED_ARTICLES'	=> 'There are no approved articles.',
	'KB_SUCCESS_STATUS'			=> 'You have successfully altered the status of the article.',
	'MCP_KB_STATUS_EXPLAIN'		=> 'With this form can you change the status of an article and supply reasons of why you did so.',
	
	// History stuff
	'KB_NO_PERM_HISTORY'		=> 'You don\'t have permission to view the history of an article.',
	'KB_ARTICLE_REVISIONS'		=> 'Article Revisions',
	'NO_ARTICLES_REVISIONS'		=> 'No article revisions available yet',
	'REVISION'					=> 'Revision ID',
	'EDITED_BY'					=> 'Last edited by %s',
	'STATUS_EDIT'				=> 'This edit was just a change of status from <strong>%1$s</strong> to <strong>%2$s</strong>.',
	'KB_VIEW_HISTORY'			=> 'View article history',
	'KB_EDITS'					=> 'Revisions',
	'KB_DIFF_FROM'				=> 'Diff from',
	'KB_DIFF_TO'				=> 'Diff to',
	'EDIT_TIME'					=> 'Revision time',
	'KB_VIEW_DIFF'				=> 'View revision differences',
	'KB_EDIT_REASON'			=> 'Reason for editting the article',
	'KB_EDIT_REASON_GLOBAL'		=> 'Is this reason public',
));
					
?>