<?php
/**
*
* @package phpBB Knowledge Base Mod (KB)
* @version $Id: kb.php 356 2009-11-08 10:40:10Z tom.martin60@btinternet.com $
* @copyright (c) 2009 Andreas Nexmann, Tom Martin
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

// Common variables
$lang = array_merge($lang, array(
	'ACCEPT_REQUEST'					=> 'Take on request',
	'ACCEPT_REQUEST_CONFIRM'			=> 'Are you sure you want to take on the responsibility of writing an article for this request?',
	'ACL_TYPE_U_KB_'					=> '',
	'ACP_PLUGINS'						=> 'Knowledge Base Plugins',
	'ALL_CATEGORIES'					=> 'All categories',
	'ALL_PAGES'							=> 'All pages',
	'APPROVED_ARTICLES'					=> 'Approved articles',
	'ARTICLE'							=> 'Article',
	'ARTICLES'							=> 'Articles',
	'ARTICLES_VIEW'						=> 'This article has been viewed %1$s time',
	'ARTICLES_VIEWS'					=> 'This article has been viewed %1$s times',
	'ARTICLE_ADD_REQ'					=> 'This article is an answer to this request',
	'ARTICLE_AUTHOR'					=> 'Article author',
	'ARTICLE_AUTHOR_EXPLAIN'			=> 'Change the article author from the original to a new one here.',
	'ARTICLE_CAT'						=> 'Article category',
	'ARTICLE_CONTENT'					=> 'Article content',
	'ARTICLE_CONTRIBUTE'				=> 'Mark this as a contribution to the open article',
	'ARTICLE_DELETED'					=> 'Your article has been deleted',
	'ARTICLE_DESC'						=> 'Article description',
	'ARTICLE_DESC_EX'					=> 'This article will teach you how to use phpmyadmin',
	'ARTICLE_DESC_EXPLAIN'				=> 'If you wish to write a description for your article please do so. The character limit is 300 and BBCodes are enabled.',
	'ARTICLE_HISTORY'					=> 'View article history',
	'ARTICLE_ID'						=> 'Article ID',
	'ARTICLE_OPEN'						=> 'Open this article for others contribution',
	'ARTICLE_POST_BOT'					=> 'Article post bot',
	'ARTICLE_POST_BOT_MSG'				=> 'Post Message',
	'ARTICLE_POST_BOT_MSG_EXPLAIN'		=> 'The message you want for your post, you may use the variables below.',
	'ARTICLE_POST_BOT_MSG_EX'			=> 'Hello Everyone,

{AUTHOR} has written a new article called \'{TITLE}\'

[b]It is about:[/b]
{DESC}

Here is a link to the article - {LINK}',
	'ARTICLE_POST_BOT_SUB'				=> 'Post Subject',
	'ARTICLE_POST_BOT_SUB_EX'			=> 'New Article - {TITLE}',
	'ARTICLE_POST_BOT_SUB_EXPLAIN'		=> 'The subject you want for your message, you may use the variables below.',
	'ARTICLE_POST_BOT_USER'				=> 'User ID',
	'ARTICLE_POST_BOT_USER_EXPLAIN'		=> 'The ID of the user you want to post the message.',
	'ARTICLE_STATUS'					=> 'Article status',
	'ARTICLE_STATUS_PAGE'				=> 'View the article status page',
	'ARTICLE_TIME'						=> 'Article creation time',
	'ARTICLE_TIME_EXPLAIN'				=> 'Here you can modify the article creation time from the original one.',
	'ARTICLE_TITLE'						=> 'Article title',
	'ARTICLE_TITLE_EX'					=> 'How to use phpmyadmin',
	'ARTICLE_TYPE'						=> 'Article type',
	'ARTICLE_UNAPPROVED'				=> 'This article has not been approved by a moderator yet.',
	'AVAILABLE'							=> 'Available',

	'BLOGGER'							=> 'Blogger',
	'BOOKMARK_ARTICLE'					=> 'Bookmark article',
	'BOOKMARK_ARTICLE_REMOVE'			=> 'Remove from bookmarks',
	'BOOKMARK_OPTIONS'					=> 'Bookmark options',

	'CAT_ADMIN'							=> 'Category administration',
	'CAT_CREATED'						=> 'Category created',
	'CAT_DELETE'						=> 'Delete category',
	'CAT_DELETED'						=> 'Your category has been deleted',
	'CAT_DELETE_EXPLAIN'				=> 'The form below will allow you to delete a category. You are able to decide where you want to put all articles (or categories) it contained.',
	'CAT_DESC'							=> 'Category description',
	'CAT_DESC_TOO_LONG'					=> 'Category description too long',
	'CAT_IMAGE'							=> 'Category image',
	'CAT_IMAGE_EXPLAIN'					=> 'Relevent to the phpbb root path',
	'CAT_INDEX'							=> 'Knowledge Base Index',
	'CAT_NAME'							=> 'Category name',
	'CAT_NAME_EMPTY'					=> 'Category name empty',
	'CAT_PARENT'						=> 'Category parent',
	'CAT_SETTINGS'						=> 'Category settings',
	'CAT_UPDATED'						=> 'Category updated',
	'CENTER_MENU'						=> 'Center menu',
	'CHANGE_STATUS'						=> 'Alter status',
	'CLICK_VIEW_ARTICLE'				=> 'Click %shere%s to view the article.',
	'COMMENTS'							=> 'Comments',
	'COMMENT_DELETED'					=> 'Your comment has been deleted.',
	'COMMENT_EMPTY_TITLE'				=> 'You have to specify a title for your comment.',
	'COMMENT_TITLE'						=> 'Comment subject',
	'CONTRIBUTORS'						=> 'Article contributors',
	'COPYRIGHT'							=> 'Copyright',
	'CREATE_CAT'						=> 'Create category',
	'CREATE_TYPE'						=> 'Create article type',
	'CURRENT_RATING_P'					=> 'This article is currently rated at %1$s<br />%2$s votes have been cast.',
	'CURRENT_RATING_S'					=> 'This article is currently rated at %1$s<br />%2$s vote has been cast.',
	'CURRENT_VERSION'					=> 'Current version',

	'DELETE_ALL_ARTICLES'				=> 'Delete all articles',
	'DELETE_ARTICLE'					=> 'Please confirm that you want to delete this article? ',
	'DELETE_ARTICLE_CONFIRM'			=> 'You will delete all old history data, all tracking information, all tags and any comments associated with this article.',
	'DELETE_COMMENT'					=> 'Please confirm that you want to delete this comment? ',
	'DELETE_COMMENT_CONFIRM'			=> 'Warning: There is no way to get this comment back when you have deleted it.',
	'DELETE_REQUEST'					=> 'Delete request',
	'DELETE_REQUEST_CONFIRM'			=> 'Please confirm that you want to delete this request, but remember once deleted, there is no way to get it back.',
	'DELICIOUS'							=> 'Delicious',
	'DESCRIPTION'						=> 'Article Description',
	'DIGG'								=> 'Digg',
	'DISPLAY_ON_PAGE'					=> 'Display on page',
	'DISPLAY_ON_PAGE_EXPLAIN'			=> 'You are able to select more than one page.',

	'EDITED_BY'							=> 'Last edited by %s',
	'EDIT_AUTHOR'						=> 'Edit author',
	'EDIT_CAT'							=> 'Edit category',
	'EDIT_REASON'						=> 'Reason for making these changes',
	'EDIT_REASON_ARTICLE'				=> 'Reason for editing this article',
	'EDIT_TIME'							=> 'Revision time',
	'EDIT_TYPE_CAT'						=> 'The category has been changed.',
	'EDIT_TYPE_CONTENT'					=> 'The main article content has been subject to change.',
	'EDIT_TYPE_DESC'					=> 'The article description has been subject to change.',
	'EDIT_TYPE_STATUS'					=> 'The article type has been changed',
	'EDIT_TYPE_TAGS'					=> 'The tags have been subject to change.',
	'EDIT_TYPE_TITLE'					=> 'The article title has been subject to change.',
	'EDIT_TYPE_TYPE'					=> 'The article type has been changed.',
	'EMAIL_ARTICLE'						=> 'Email Article',
	'ENABLE_CATS'						=> 'Enable categories',
	'ENABLE_FORUM_BOT'					=> 'Enable article post bot',
	'ENABLE_HIGHEST_RATED_ARTICLES'		=> 'Enable highest rated articles',
	'ENABLE_LATEST_ARTICLES'			=> 'Enable latest article',
	'ENABLE_MOST_VIEWED_ARTICLES'		=> 'Enable most viewed articles',
	'ENABLE_RANDOM_ARTICLES'			=> 'Enable random article',
	'ENABLE_REQUEST_ARTICLES'			=> 'Enable request articles',
	'ENABLE_SEARCH'						=> 'Enable search',
	'ENABLE_STATS'						=> 'Enable statistics',
	'EXAMPLE'							=> 'Example',
	'EXPORT_ARTICLE'					=> 'Export Article',

	'FACEBOOK'							=> 'Facebook',
	'FEED'								=> 'feeds',
	'FORUM_ID'							=> 'Forum name',
	'FORUM_ID_EXPLAIN'					=> 'The forum where you want the message to be posted.',
	'FRIEND_FEED'						=> 'Friend Feed',

	'GOOGLE'							=> 'Google',

	'HIDE_CAT'							=> 'Hide categories',
	'HIGHEST_RATED_ARTICLES'			=> 'Highest rated articles',
	'HIGHEST_RATED_ARTICLES_LIMIT'		=> 'How many articles do you want to show on the list',
	'HISTORY'							=> 'History',
	'HOT_ARTICLES_LIMIT'				=> 'Number of articles shown',

	'INSTALLED_PLUGINS'					=> 'Installed Plugins',
	'INSTALL_KB'						=> 'Install Knowledge Base Mod',
	'INSTALL_KB_CONFIRM'				=> 'Please confirm that you want to install the Knowledge Base Mod. Before starting the installation please take a full backup of your forum and please make sure you have downloaded the latest version of this mod. <br />This installation script uses the latest version of UMIL (Unified Mod Install Library), please make sure you have this installed in /phpbb root/umil/umil.php on your server. The script can be found at <a href=\'http://www.phpbb.com/mods/umil/\'>phpBB.com/mods/umil</a>. <br /><br /><b>WARNING: THIS EARLY VERSION OF KB MOD IS NOT INTENDED FOR USE ON LIVE FORUMS!</b>',
	'INSTALL_PLUGIN'					=> 'Install Knowledge Base Plugin',
	'INSTALL_PLUGIN_CONFIRM'			=> 'Are you sure you want to install this plugin?',

	'KB'								=> 'Knowledge Base',
	'KB_ADDED'							=> 'The article has been successfully added and is now awaiting approval by a moderator. In the meantime, you can view your article %shere%s.',
	'KB_ADDED_WA'						=> 'The article has been successfully addded and can now be found in the category you chose. You can view it %shere%s.',
	'KB_ADD_ARTICLE'					=> 'Add article',
	'KB_ADD_COMMENT'					=> 'Add comment',
	'KB_ADD_REQUEST'					=> 'Add article request',
	'KB_ALLOW_ATTACH'					=> 'Allow attachments to be used',
	'KB_ALLOW_BBCODE'					=> 'Allow bbcodes in articles',
	'KB_ALLOW_BOOK'						=> 'Allow users to bookmark articles',
	'KB_ALLOW_FLASH'					=> 'Allow flash in articles',
	'KB_ALLOW_LINKS'					=> 'Allow links in articles',
	'KB_ALLOW_SIG'						=> 'Allow signitures to be used',
	'KB_ALLOW_SMILES'					=> 'Allow smiles in articles',
	'KB_ALLOW_SUB'						=> 'Allow users to subcribe to articles',
	'KB_APPROVED_ARTICLES'				=> 'Approved articles',
	'KB_ARTICLE_REVISIONS'				=> 'Article Revisions',
	'KB_ART_PER_PAGE'					=> 'How many articles per page',
	'KB_BOOKMARKED_ARTICLES'			=> 'Bookmarked articles',
	'KB_CAN'							=> '<strong>can</strong>',
	'KB_CATS'							=> 'Categories',
	'KB_COMMENT_ADDED'					=> 'The comment has been successfully added and is now visible in the article %shere%s.',
	'KB_COMMENT_DELETED'				=> 'The comment has been successfully deleted.',
	'KB_COMMENT_EDITED'					=> 'The comment has been successfully edited and you can now view the updated comment %shere%s.',
	'KB_COMMENT_EDITED_BY'				=> 'This comment was last edited by %1$s on %2$s',
	'KB_COM_PER_PAGE'					=> 'How many comments per page',
	'KB_DELETED'						=> 'The article has been successfully deleted along with all the accompanying article data',
	'KB_DELETE_ARTICLE'					=> 'Delete article',
	'KB_DELETE_BOOKMARK'				=> 'Remove bookmark',
	'KB_DELETE_BOOKMARK_CONFIRM'		=> 'Please confirm that you want to remove the bookmark for this article.',
	'KB_DELETE_COMMENT'					=> 'Delete comment',
	'KB_DELETE_REQUEST'					=> 'Delete article request',
	'KB_DELETE_SUBSCRIBE'				=> 'Delete notification settings',
	'KB_DELETE_SUBSCRIBE_CONFIRM'		=> 'Please confirm that you want to delete the notification settings for this article.',
	'KB_DIFF_FROM'						=> 'Diff from',
	'KB_DIFF_TO'						=> 'Diff to',
	'KB_DISABLE_EXPORT'					=> 'Article export has been disabled by the administrator.',
	'KB_EDITED'							=> 'The article has been successfully edited and you can now view the updated article %shere%s.',
	'KB_EDITED_BY'						=> 'This article was last %1$sedited%2$s by %3$s on %4$s',
	'KB_EDITS'							=> 'Revisions',
	'KB_EDIT_ARTICLE'					=> 'Edit article',
	'KB_EDIT_COMMENT'					=> 'Edit comment',
	'KB_EDIT_REASON'					=> 'Reason for editting the article',
	'KB_EDIT_REASON_GLOBAL'				=> 'Is this reason public',
	'KB_EDIT_REQUEST'					=> 'Edit article request',
	'KB_EMPTY_TITLE'					=> 'You have to specify a title for your article.',
	'KB_ENABLE'							=> 'Enable the Knowledge Base',
	'KB_FEED_CAT'						=> 'Live feed',
	'KB_HAS_RATED'						=> 'You have already rated this article and cannot rate it twice.',
	'KB_ICON'							=> 'Article Icon',
	'KB_INDEX'							=> 'Knowledge Base Index',
	'KB_INSTALLED'						=> 'Knowledge Base Mod version %1$s has been successfully installed, you may now proceed to the ACP to set permissions for it and enable it.',
	'KB_KEYWORDS_EXPLAIN'				=> 'Type in the keywords you want to search for here. Use a space to search for more keywords and decide if you want to search for all terms or just some of them. Special characters like + and - is not supported in this search.',
	'KB_LAST_UPDATE'					=> 'Last updated',
	'KB_LATEST'							=> 'Latest Article',
	'KB_LINK_NAME'						=> 'Knowledge Base link name',
	'KB_LINK_NAME_EXPLAIN'				=> 'The text you want to appear in the overall header link',
	'KB_LOGIN_EXPLAIN_ADD'				=> 'You need to login in order to add an article.',
	'KB_LOGIN_EXPLAIN_COMMENT_ADD'		=> 'You need to login in order to add a comment.',
	'KB_LOGIN_EXPLAIN_COMMENT_DELETE'	=> 'You need to login in order to delete this comment.',
	'KB_LOGIN_EXPLAIN_COMMENT_EDIT'		=> 'You need to login in order to edit this comment.',
	'KB_LOGIN_EXPLAIN_DELETE'			=> 'You need to login in order to delete this article.',
	'KB_LOGIN_EXPLAIN_EDIT'				=> 'You need to login in order to edit this article.',
	'KB_LOGIN_EXPLAIN_HISTORY'			=> 'You have to login to view this articles history.',
	'KB_LOGIN_EXPLAIN_READ'				=> 'You need to login in order to read this article.',
	'KB_LOGIN_EXPLAIN_REQUEST_ADD'		=> 'You need to login in order to add a request.',
	'KB_LOGIN_EXPLAIN_REQUEST_DELETE'	=> 'You need to login in order to delete this request.',
	'KB_LOGIN_EXPLAIN_REQUEST_EDIT'		=> 'You need to login in order to edit this request.',
	'KB_LOGIN_EXPLAIN_VIEW'				=> 'You need to login in order to view this article.',
	'KB_MANAGEMENT'						=> 'Manage Knowledge Base',
	'KB_NOT'							=> '<strong>cannot</strong>',
	'KB_NOTIFY_AUTHOR_COMMENT'			=> 'Notify me when the author comments the article',
	'KB_NOTIFY_BY'						=> 'Notify by',
	'KB_NOTIFY_COMMENT'					=> 'Notify me when someone comments the article',
	'KB_NOTIFY_EDIT_ALL'				=> 'Notify me on all edits',
	'KB_NOTIFY_EDIT_CONTENT'			=> 'Notify me when the article content is edited',
	'KB_NOTIFY_MAIL'					=> 'Notify me via E-mail',
	'KB_NOTIFY_MOD_COMMENT_GLOBAL'		=> 'Notify me when a moderator comments the article',
	'KB_NOTIFY_ON'						=> 'Notify on',
	'KB_NOTIFY_PM'						=> 'Notify me with a private message',
	'KB_NOTIFY_POPUP'					=> 'Notification of article update',
	'KB_NOTIFY_POPUP_EXPLAIN'			=> 'This popup has been generated because an update has been submitted to the article %s.',
	'KB_NOTIFY_UCP'						=> 'Notify me via the User Control Panel',
	'KB_NOT_ADD_REQUEST'				=> 'None',
	'KB_NOT_COMMENT_UNAPPROVED'			=> 'You are only allowed to comment unapproved articles through the user control panel or the moderator control panel.',
	'KB_NOT_ENABLE'						=> 'Knowledge Base isn\'t enabled at the moment please come back later!',
	'KB_NO_APPROVED_ARTICLES'			=> 'There are no approved articles.',
	'KB_NO_ARTICLE'						=> 'There is no article with that id.',
	'KB_NO_BOOKMARKED_ARTICLES'			=> 'You have not bookmarked any articles',
	'KB_NO_CATEGORY'					=> 'There is no such category.',
	'KB_NO_COMMENT'						=> 'There is no such comment.',
	'KB_NO_EDIT_ARTICLE'				=> 'There is no edits for this article.',
	'KB_NO_LATEST'						=> 'No information regarding latest articles.',
	'KB_NO_NOTIFY'						=> 'Don\'t notify me',
	'KB_NO_PERM_HISTORY'				=> 'You don\'t have permission to view the history of an article.',
	'KB_NO_PERM_RATE'					=> 'You do not have permission to rate this article.',
	'KB_NO_QUEUED_ARTICLES'				=> 'There are no articles awaiting approval.',
	'KB_NO_REQS'						=> 'There are no article requests.',
	'KB_NO_REQUEST'						=> 'There is no request with that id.',
	'KB_NO_SUBSCRIBED_ARTICLES'			=> 'You are currently not subscribed to any articles.',
	'KB_NO_TYPE'						=> 'There is no article type with that id.',
	'KB_NO_UNINSTALL'					=> 'This plugin cannot be uninstalled',
	'KB_PERMISSIONS'					=> 'Knowledge Base Permissions',
	'KB_PERM_ADD'						=> 'You %1$s start new articles',
	'KB_PERM_ATTACH'					=> 'You %1$s use attachments in your articles/comments',
	'KB_PERM_COM'						=> 'You %1$s comment on articles',
	'KB_PERM_DEL'						=> 'You %1$s delete your articles/comments',
	'KB_PERM_DOWN'						=> 'You %1$s download attachments',
	'KB_PERM_EDIT'						=> 'You %1$s edit your articles/comments',
	'KB_PERM_HIST'						=> 'You %1$s view article history',
	'KB_PERM_RATE'						=> 'You %1$s rate articles',
	'KB_QUEUED_ARTICLES'				=> 'Articles awaiting approval',
	'KB_RAN_ART'						=> 'Random Article',
	'KB_REQS'							=> 'Article requests',
	'KB_REQUEST_ADDED'					=> 'Your article request has been successfully added, click %shere%s to view it.',
	'KB_REQUEST_EDITED'					=> 'You have successfully edited the article request, click %shere%s to view it.',
	'KB_REQUEST_LIST'					=> 'View article requests',
	'KB_REQUEST_VIEW'					=> 'View request',
	'KB_SEARCH_CATS'					=> 'Search categories',
	'KB_SEARCH_CATS_EXPLAIN'			=> 'Determine which categories you want to search in, leave blank to search in all categories.',
	'KB_SEARCH_DESC'					=> 'Description of article.',
	'KB_SEARCH_TEXT'					=> 'Content of article.',
	'KB_SEARCH_TITLE'					=> 'Title of article.',
	'KB_SEARCH_TITLE_TEXT_DESC'			=> 'Title, description and content of article.',
	'KB_SORT_ASCENDING'					=> 'Ascending',
	'KB_SORT_ATIME'						=> 'Post time',
	'KB_SORT_AUTHOR'					=> 'Author',
	'KB_SORT_BY'						=> 'Sort articles by',
	'KB_SORT_CAT'						=> 'Category',
	'KB_SORT_CAUTHOR'					=> 'Author',
	'KB_SORT_COMMENT_BY'				=> 'Sort comments by',
	'KB_SORT_CTIME'						=> 'Post time',
	'KB_SORT_CTITLE'					=> 'Title',
	'KB_SORT_DESCENDING'				=> 'Descending',
	'KB_SORT_ETIME'						=> 'Last edited',
	'KB_SORT_RATING'					=> 'Rating',
	'KB_SORT_TITLE'						=> 'Title',
	'KB_SORT_VIEWS'						=> 'Views',
	'KB_STATS'							=> 'Knowledge Base Statistics',
	'KB_STATUS_APPROVED'				=> 'Approved',
	'KB_STATUS_DISAPPROVED'				=> 'Not approved',
	'KB_STATUS_ONHOLD'					=> 'On hold',
	'KB_STATUS_UNREVIEW'				=> 'Not viewed',
	'KB_SUBSCRIBED_ARTICLES'			=> 'Subscribed articles',
	'KB_SUBSCRIBE_STATUS_ADD'			=> 'You are adding a notification for the article %s.',
	'KB_SUBSCRIBE_STATUS_EDIT'			=> 'You are editing the notification settings for the article %s.',
	'KB_SUCCESS_BOOKMARK_ADD'			=> 'You have successfully bookmarked the article.',
	'KB_SUCCESS_BOOKMARK_DELETE'		=> 'You have successfully removed the bookmark from the article.',
	'KB_SUCCESS_STATUS'					=> 'You have successfully altered the status of the article.',
	'KB_SUCCESS_SUBSCRIBE_ADD'			=> 'You have successfully marked the article for notification with the specified settings.',
	'KB_SUCCESS_SUBSCRIBE_DELETE'		=> 'You have successfully deleted the notification settings for the specified article.',
	'KB_SUCCESS_SUBSCRIBE_EDIT'			=> 'You have successfully edited the notification settings for the article.',
	'KB_TOO_FEW_CHARS_ARTICLE'			=> 'Your article contains too few characters.',
	'KB_TOO_FEW_CHARS_COMMENT'			=> 'Your comment contains too few characters.',
	'KB_TOO_FEW_CHARS_DESC'				=> 'Your description contains too few characters.',
	'KB_TOO_FEW_CHARS_REQUEST'			=> 'Your request contains too few characters.',
	'KB_TOO_MANY_CHARS_ARTICLE'			=> 'Your article contains %1$d characters. The maximum number of allowed characters is %2$d.',
	'KB_TOO_MANY_CHARS_COMMENT'			=> 'Your comment contains %1$d characters. The maximum number of allowed characters is %2$d.',
	'KB_TOO_MANY_CHARS_DESC'			=> 'Your description contains %1$d characters. The maximum number of allowed characters is %2$d.',
	'KB_TOO_MANY_CHARS_REQUEST'			=> 'Your request contains %1$d characters. The maximum number of allowed characters is %2$d.',
	'KB_UNBOOKMARK_MARKED'				=> 'Remove bookmarks',
	'KB_UNINSTALLED'					=> 'Knowledge Base Mod has been successfully uninstalled',
	'KB_UNSUBSCRIBE_MARKED'				=> 'Unsubscribe marked',
	'KB_UPDATED'						=> 'Your Knowledge Base Mod has been updated to version %1$s, check the ACP for confirmation of this.',
	'KB_UPDATE_UMIL'					=> 'Please download the latest UMIL (Unified MOD Install Library) from: <a href=\'http://www.phpbb.com/mods/umil/\'>phpBB.com/mods/umil</a>',
	'KB_USER_CANNOT_ADD'				=> 'You are not allowed to add an article.',
	'KB_USER_CANNOT_COMMENT_ADD'		=> 'You are not allowed to add a comment.',
	'KB_USER_CANNOT_COMMENT_DELETE'		=> 'You are not allowed to delete this comment.',
	'KB_USER_CANNOT_COMMENT_EDIT'		=> 'You are not allowed to edit this comment.',
	'KB_USER_CANNOT_DELETE'				=> 'You are not allowed to delete this article.',
	'KB_USER_CANNOT_EDIT'				=> 'You are not allowed to edit this article.',
	'KB_USER_CANNOT_READ'				=> 'You don\'t have sufficient permission to read this article.',
	'KB_USER_CANNOT_REQUEST_ADD'		=> 'You are not allowed to request an article.',
	'KB_USER_CANNOT_REQUEST_DELETE'		=> 'You are not allowed to delete this request.',
	'KB_USER_CANNOT_REQUEST_EDIT'		=> 'You are not allowed to edit this request.',
	'KB_USER_CANNOT_VIEW'				=> 'You don\'t have sufficient permission to view this category.',
	'KB_VIEW_DIFF'						=> 'View revision differences',
	'KB_VIEW_HISTORY'					=> 'View article history',

	'LATEST_ARTICLES'					=> 'Latest articles',
	'LATEST_VERSION'					=> 'Latest version',
	'LEFT_MENU'							=> 'Left menu',
	'LINKED_IN'							=> 'Linked In',
	'LINK_TO_THIS'						=> 'External link to this article',
	'LIVE'								=> 'Live',
	'LOOK_UP_CATEGORY'					=> 'Look up category',

	'MATCHS_FOUND'						=> 'matches found',
	'MATCH_FOUND'						=> 'match found',
	'MCP_KB_STATUS_EXPLAIN'				=> 'With this form can you change the status of an article and supply reasons of why you did so.',
	'MIXX'								=> 'Mixx',
	'MOST_VIEWED_ARTICLES'				=> 'Most viewed articles',
	'MOVE_ARTICLES_TO'					=> 'Move articles to',
	'MYSPACE'							=> 'MySpace',

	'NAME'								=> 'Name',
	'NETVIBES'							=> 'Netvibes',
	'NEWEST'							=> 'Newest',
	'NOTIFY_PM_SUBJECT'					=> 'Notification from Knowledge Base Mod',
	'NO_ARTICLES'						=> 'There are no articles in this category.',
	'NO_ARTICLES_CREATED'				=> 'No articles have been created yet!',
	'NO_ARTICLES_REVISIONS'				=> 'No article revisions available yet',
	'NO_CAT'							=> 'No category available',
	'NO_CATS'							=> 'The Knowledge Base has no categories.',
	'NO_CENTER_MENU_PLUGINS'			=> 'No center menu plugins',
	'NO_COMMENTS'						=> 'There are no comments to this article.',
	'NO_INSTALLED_PLUGINS'				=> 'No installed plugins',
	'NO_LEFT_MENU_PLUGINS'				=> 'No left menu plugins',
	'NO_MCP_PERM'						=> 'You don\'t have permission to access the MCP for the Knowledge Base',
	'NO_MENU'							=> 'No Menu',
	'NO_MENU_PLUGINS'					=> 'No Menu plugins installed',
	'NO_PLUGIN_FILE'					=> 'No plugin file found',
	'NO_RELATED_ARTICLES'				=> 'No related articles have been found.',
	'NO_RIGHT_MENU_PLUGINS'				=> 'No right menu plugins',
	'NO_SEARCH_MATCHES'					=> 'No matches was found for your search.',
	'NO_TYPE_IMAGE'						=> 'The image path you supplied is not valid.',
	'NO_TYPE_TITLE'						=> 'You have to specify a title for the article type.',
	'NO_UNINSTALLED_PLUGINS'			=> 'No uninstalled plugins',
	'N_A'								=> 'N/A',

	'ORIGINAL'							=> 'Original',

	'PERMISSION_TYPE'					=> 'Knowledge Base Permissions',
	'PLUGINS_EXPLAIN'					=> 'This is where you can install/uninstall plugins for the knowledge base',
	'PLUGIN_INSTALLED'					=> 'Your plugin has been installed. Now edit the settings to customise your plugin',
	'PLUGIN_NAME'						=> 'Plugin Name',
	'PLUGIN_TEMPLATE_MISSING'			=> 'Your plugin template <strong>%s</strong> is missing',
	'PLUGIN_UNINSTALLED'				=> 'Your plugin has been uninstalled.',
	'PLUGIN_UPDATED'					=> 'Your plugin has been updated.',
	'POPULAR_ART'						=> 'popular article',
	'POSTING'							=> 'Posting article',

	'RATED_THANKS'						=> 'Thank you for rating the article.',
	'RATINGS'							=> 'Rate this article',
	'REDDIT'							=> 'Reddit',
	'RELATED_ARTICLES'					=> 'Related Articles',
	'REQUEST'							=> 'Requests',
	'REQUESTS'							=> 'article requests',
	'REQUEST_ACCEPTED'					=> 'You have accepted the request and can now select it from a dropdown menu when writing the article.',
	'REQUEST_ARTICLES'					=> 'Request Articles',
	'REQUEST_ARTICLES_LIMIT'			=> 'Number of requests to show',
	'REQUEST_DELETED'					=> 'You have deleted the article request and will now be transferred to the request list.',
	'REQUEST_EMPTY_TITLE'				=> 'You have to specify a title for your request.',
	'REQUEST_NOT_ENABLED'				=> 'Request article is not enabled at the moment',
	'REQUEST_TITLE'						=> 'Request title',
	'RETURN_KB'							=> '%sBrowse back to the Knowledge Base Index%s',
	'RETURN_KB_ARTICLE'					=> '%sReturn to the last article visited%s',
	'RETURN_KB_CAT'						=> '%sReturn to the last category visited%s',
	'RETURN_REQ'						=> '%sReturn to the article request%s',
	'REVISION'							=> 'Revision ID',
	'REV_CHANGES'						=> 'Revision changes from last version',
	'RIGHT_MENU'						=> 'Right menu',
	'RSS_AUTHOR'						=> 'Subcribe to this author',
	'RSS_CAT'							=> 'Subcribe to this category',
	'RSS_LATEST'						=> 'Subcribe to latest articles',
	'RULE'								=> 'Rule',
	'RUN_NOW'							=> 'Run Now',

	'SAME_AUTHOR_ARTICLES'				=> 'Articles from the same author',
	'SEARCH'							=> 'Search',
	'SEARCH_ARTICLES'					=> 'Search Articles',
	'SEARCH_KB'							=> 'Knowledge Base Search',
	'SEARCH_NOT_ENABLED'				=> 'Knowledge Base search isn\t enabled',
	'SEARCH_YOUR_ARTICLES'				=> 'Search your articles',
	'SELECT_CAT'						=> 'Select category',
	'SELECT_SUBCAT_EXPLAIN'				=> '',
	'SELECT_TYPE'						=> 'Select article type',
	'SHOW_CAT'							=> 'Show categories',
	'SOCIAL_BOOKMARKS'					=> 'Social bookmarks available',
	'STARS_OUT_OF'						=> 'stars out of',
	'START_WATCHING_ARTICLE'			=> 'Subscribe to article',
	'STATUS_ADDED'						=> 'ADDED',
	'STATUS_ADDED_EXPLAIN'				=> 'This is an old request which has been taken on by %1$s and an article called %2$s has been submitted to the Knowledge Base.',
	'STATUS_EDIT'						=> 'This edit was just a change of status from <strong>%1$s</strong> to <strong>%2$s</strong>.',
	'STATUS_PENDING'					=> 'PENDING',
	'STATUS_PENDING_EXPLAIN'			=> 'This is a new request which has been taken on by %s, who is currently writing an article according to it.',
	'STATUS_REQUEST'					=> 'REQUEST',
	'STATUS_REQUEST_EXPLAIN'			=> 'This is a new request which hasn\'t been taken on by any user. Feel free to do so by clicking %shere%s.',
	'STOP_WATCHING_ARTICLE'				=> 'Remove subscription',
	'STUMBLE_UPON'						=> 'Stumble Upon',
	'SUBCAT'							=> 'Subcategory',
	'SUBCATS'							=> 'Subcategories',
	'SUCCESS'							=> 'Success',

	'TAGS'								=> 'Tags',
	'TAGS_EXPLAIN'						=> 'You can specify which tags you want your article to be found under (if any). Seperate each tag by a comma.',
	'TECHNORATI'						=> 'Technorati',
	'THIS'								=> 'this',
	'TOTAL_ARTICLES'					=> 'Total articles',
	'TOTAL_CATS'						=> 'Total categories',
	'TOTAL_COMMENTS'					=> 'Total comments',
	'TWITTER'							=> 'Twitter',
	'TYPE_ADD'							=> 'Add article type',
	'TYPE_ADMIN'						=> 'Article type administration',
	'TYPE_AFTER'						=> 'Article type suffix',
	'TYPE_BEFORE'						=> 'Article type prefix',
	'TYPE_CREATED'						=> 'Article type successfully created.',
	'TYPE_DELETE'						=> 'Delete article type',
	'TYPE_DELETED'						=> 'You have deleted the article type.',
	'TYPE_DELETE_CONFIRM'				=> 'Are you sure you want to delete this article type?',
	'TYPE_EDIT'							=> 'Edit article type',
	'TYPE_ICON'							=> 'Article type icon',
	'TYPE_ICON_EXPLAIN'					=> 'You can choose an icon which you want to along with your article type. Remember to check the radio box to left of the dropdown box to activate this.',
	'TYPE_IMAGE'						=> 'Custom article type image',
	'TYPE_IMAGE_EXPLAIN'				=> 'You can choose a custom image, the path is relative to the phpbb root. Remember to check the radio box to the left of the image field to activate this.',
	'TYPE_SETTINGS'						=> 'Article type settings',
	'TYPE_TITLE'						=> 'Article type title',
	'TYPE_UPDATED'						=> 'Article type successfully updated.',

	'UNINSTALL'							=> 'Uninstall',
	'UNINSTALLED_PLUGINS'				=> 'Uninstalled Plugins',
	'UNINSTALL_KB'						=> 'Uninstall Knowledge Base Mod',
	'UNINSTALL_KB_CONFIRM'				=> 'Please confirm that you want to uninstall the Knowledge Base Mod.',
	'UNINSTALL_PLUGIN'					=> 'Uninstall This Plugin',
	'UNINSTALL_PLUGIN_CONFIRM'			=> 'are you sure you want to uninstall this plugin',
	'UPDATE_INSTRUCTIONS'				=> '

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
	'UPDATE_KB'							=> 'Update Knowledge Base Mod',
	'UPDATE_KB_CONFIRM'					=> 'Please confirm that you want to update the Knowledge Base Mod to the latest version. Make sure that your phpBB is also updated to the latest version, or this version of KB Mod might not work. Furthermore you should make sure that you have a complete backup of your forum before you begin. Like the installer, this script uses the latest version of UMIL (Unified Mod Install Library), so please make sure you have this installed in /phpbb root/umil/umil.php on your server. The script can be found at <a href=\'http://www.phpbb.com/mods/umil/\'>phpBB.com/mods/umil</a>. <br /><br /><b>WARNING: THIS EARLY VERSION OF KB MOD IS NOT INTENDED FOR USE ON LIVE FORUMS!</b>',
	'USE_CURRENT_TIME'					=> 'Use current time',
	'USE_CUSTOM_AUTHOR'					=> 'Define another user as author',
	'USE_CUSTOM_TIME'					=> 'Define custom time',
	'USE_ORIGINAL_AUTHOR'				=> 'Use original author',
	'USE_ORIGINAL_TIME'					=> 'Use original article time',
	'USE_THIS_AUTHOR'					=> 'Use yourself as author',

	'VARIABLE'							=> 'Variables',
	'VERSION'							=> 'Version',
	'VERSION_CHECK'						=> 'Version check',
	'VERSION_CHECK_EXPLAIN'				=> 'Checks to see if the version of Knowledge Base you are currently running is up to date.',
	'VERSION_NOT_UP_TO_DATE'			=> 'Your version of Knowledge Base is not up to date. Please continue the update process.',
	'VERSION_NOT_UP_TO_DATE_ACP'		=> 'Your version of Knowledge Base is not up to date.<br />Below you will find a link to the release announcement for the latest version as well as instructions on how to perform the update.',
	'VERSION_UP_TO_DATE'				=> 'Your installation is up to date, no updates are available for your version of Knowledge Base. You may want to continue anyway to perform a file validity check.',
	'VERSION_UP_TO_DATE_ACP'			=> 'Your installation is up to date, no updates are available for your version of Knowledge Base. You do not need to update your installation.',
	'VIEWING_ARTICLE_HISTORY'			=> 'Viewing article history of %s',
	'VIEWING_EDIT_DIFF'					=> 'Viewing revision differences',
	'VIEWING_FILE_CONTENTS'				=> 'Viewing file contents',
	'VIEW_ALL'							=> 'View all',
	'VIEW_ARTICLE'						=> 'View article',
	'VIEW_CAT'							=> 'View category',
	'VIEW_REV_INFO'						=> 'View revision information',
	'VIEW_TAG'							=> 'View tag',

	'WAITING_ARTICLES'					=> 'Articles awaiting approval',
	'WAITING_MOD_COMMENTS'				=> 'Unread moderator comments',
	'WHICH_MENU'						=> 'What menu do you want it to appear on',
	'WORD'								=> 'Export to word',
	'WORDPRESS'							=> 'Wordpress',
	'WRITTEN_BY'						=> 'Written by',
	'WRITTEN_ON'						=> 'Written on',
	'WRONG_INFO_FILE_FORMAT'			=> 'Wrong info file format',

	'YOUR_KB_DETAILS'					=> 'Your activity in the knowledge base',
	
));

?>