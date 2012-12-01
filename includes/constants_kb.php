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
// This file holds extra constants defined to use in the KB mod.
//
define('KB_VERSION', '0.0.7');


// Extra db tables
define('KB_ATTACHMENTS_TABLE', $table_prefix . 'article_attachments');
define('KB_CATS_TABLE', $table_prefix . 'article_cats');
define('KB_COMMENTS_TABLE', $table_prefix . 'article_comments');
define('KB_EDITS_TABLE', $table_prefix . 'article_edits');
define('KB_RATE_TABLE', $table_prefix . 'article_rate');
define('KB_TAGS_TABLE', $table_prefix . 'article_tags');
define('KB_TRACK_TABLE', $table_prefix . 'article_track');
define('KB_TABLE', $table_prefix . 'articles');

// Comment type constants
define('COMMENT_GLOBAL', 0);
define('COMMENT_MOD', 1);

// Article status
define('STATUS_UNREVIEW', 0);
define('STATUS_APPROVED', 1);
define('STATUS_DISAPPROVED', 2);
define('STATUS_ONHOLD', 3); // Put on hold by a moderator.

// Notify by
define('NOTIFY_UCP', 0); // Notify in UCP in some way
define('NOTIFY_MAIL', 1);
define('NOTIFY_PM', 2);
define('NOTIFY_POPUP', 3);

// Notify on
define('NO_NOTIFY', 0);
define('NOTIFY_EDIT_CONTENT', 1); // Only notify on edits to the article content
define('NOTIFY_EDIT_ALL', 2); // Notify on all edits
define('NOTIFY_COMMENT', 3); // Notify on all comments
define('NOTIFY_AUTHOR_COMMENT', 4); // Notify on author comments
define('NOTIFY_MOD_COMMENT_GLOBAL', 5); // Notify on moderator comments
// These 2 are only for set when submitting an article if the author wants it and only for the author
define('NOTIFY_MOD_COMMENT_NOT_GLOBAL', 6);
define('NOTIFY_STATUS_CHANGE', 7);

// Search in
define('SEARCH_TITLE_TEXT_DESC', 0);
define('SEARCH_TITLE', 1);
define('SEARCH_TEXT', 2);
define('SEARCH_DESC', 3);
?>