<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Messages in english.
 * @package api
 */

/**
 * 
 */
require_once '../config.php';

if (!isset($KOTOBA_LOCALE_MESSAGES)) {
    $KOTOBA_LOCALE_MESSAGES = array();
}
$_ = &$KOTOBA_LOCALE_MESSAGES;

$_['ACL.']['eng'] = 'ACL.';
$_['Bans.']['eng'] = 'Bans.';
$_['Board id=%d not found.']['eng'] = 'Board id=%d not found.';
$_['Board name=%s not found.']['eng'] = 'Board name=%s not found.';
$_['Board, Thread or Post is unique. Set one of it.']['eng'] = 'Board, Thread or Post is unique. Set one of it.';
$_['Boards.']['eng'] = 'Boards.';
$_['Cannot convert image to PNG format.']['eng'] = 'Cannot convert image to PNG format.';
$_['Captcha.']['eng'] = 'Captcha.';
$_['Change permission cannot be set without view. Moderate permission cannot be set without all others.']['eng'] = 'Change permission cannot be set without view. Moderate permission cannot be set without all others.';
$_['Copy file.']['eng'] = 'Copy file.';
$_['Failed to copy file %s to %s.']['eng'] = 'Failed to copy file %s to %s.';
$_['Failed to create hard link %s for file %s.']['eng'] = 'Failed to create hard link %s for file %s.';
$_['Failed to open or create log file %s.']['eng'] = 'Failed to open or create log file %s.';
$_['Failed to start session.']['eng'] = 'Failed to start session.';
$_['GD doesn\'t support %s file type.']['eng'] = 'GD doesn\'t support %s file type.';
$_['GD library.']['eng'] = 'GD library.';
$_['Groups.']['eng'] = 'Groups.';
$_['Id of new group was not received.']['eng'] = 'Id of new group was not received.';
$_['Image convertion.']['eng'] = 'Image convertion.';
$_['Image libraries.']['eng'] = 'Image libraries.';
$_['Image libraries disabled or doesn\'t work.']['eng'] = 'Image libraries disabled or doesn\'t work.';
$_['Imagemagic doesn\'t support %s file type.']['eng'] = 'Imagemagic doesn\'t support %s file type.';
$_['Imagemagic library.']['eng'] = 'Imagemagic library.';
$_['Invlid unicode characters deteced.']['eng'] = 'Invlid unicode characters deteced.';
$_['Language id=%d not exist.']['eng'] = 'Language id=%d not exist.';
$_['Languages.']['eng'] = 'Languages.';
$_['Link creation.']['eng'] = 'Link creation.';
$_['Locale.']['eng'] = 'Locale.';
$_['Logging.']['eng'] = 'Logging.';
$_['Message detected as spam.']['eng'] = 'Message detected as spam.';
$_['No attachment and text is empty.']['eng'] = 'No attachment and text is empty.';
$_['No one group exists.']['eng'] = 'No one group exists.';
$_['No one language exists.']['eng'] = 'No one language exists.';
$_['No one rule in ACL.']['eng'] = 'No one rule in ACL.';
$_['No one stylesheet exists.']['eng'] = 'No one stylesheet exists.';
$_['No one user exists.']['eng'] = 'No one user exists.';
$_['No threads to edit.']['eng'] = 'No threads to edit.';
$_['No words for search.']['eng'] = 'No words for search.';
$_['One of search words is more than 60 characters.']['eng'] = 'One of search words is more than 60 characters.';
$_['Page number=%d not exist.']['eng'] = 'Page number=%d not exist.';
$_['Pages.']['eng'] = 'Pages.';
$_['Post id=%d not found or user id=%d have no permission.']['eng'] = 'Post id=%d not found or user id=%d have no permission.';
$_['Posts.']['eng'] = 'Posts.';
$_['Request method.']['eng'] = 'Request method.';
$_['Request method not defined or unexpected.']['eng'] = 'Request method not defined or unexpected.';
$_['Remote address is not an IP address.']['eng'] = 'Remote address is not an IP address.';
$_['Search.']['eng'] = 'Search.';
$_['Search keyword not set or too short.']['eng'] = 'Search keyword not set or too short.';
$_['Session.']['eng'] = 'Session.';
$_['Setup locale failed.']['eng'] = 'Setup locale failed.';
$_['Spam.']['eng'] = 'Spam.';
$_['Stylesheet id=%d not exist.']['eng'] = 'Stylesheet id=%d not exist.';
$_['Stylesheets.']['eng'] = 'Stylesheets.';
$_['Thread id=%d not found.']['eng'] = 'Thread id=%d not found.';
$_['Thread number=%d not found.']['eng'] = 'Thread number=%d not found.';
$_['Thread id=%d was archived.']['eng'] = 'Thread id=%d was archived.';
$_['Thread id=%d was closed.']['eng'] = 'Thread id=%d was closed.';
$_['Threads.']['eng'] = 'Threads.';
$_['Unicode.']['eng'] = 'Unicode.';
$_['User id=%d has no group.']['eng'] = 'User id=%d has no group.';
$_['Users.']['eng'] = 'Users.';
$_['You enter wrong verification code %s.']['eng'] = 'You enter wrong verification code %s.';
$_['You id=%d have no permission to do it on board id=%d.']['eng'] = 'You id=%d have no permission to do it on board id=%d.';

unset($_);
?>
