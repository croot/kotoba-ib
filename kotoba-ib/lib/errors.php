<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Exception extensions.
 * @package api
 */

/**
 *
 */
require_once dirname(dirname(__FILE__)) . '/config.php';
require_once Config::ABS_PATH . '/lib/kgettext.php';

class Error {
    private $image;
    private $text;
    private $title;
    private $handler;

    function  __construct($text, $title, $image = NULL, $handler = NULL) {

        $this->text = $text;
        $this->title = $title;
        if ($image == NULL) {
            $image = Config::DIR_PATH . '/img/errors/default_error.png';
        }
        $this->image = $image;
        if ($handler == NULL) {
            $handler = function($smarty) use ($text, $title, $image) {
                           $smarty->assign('show_control',
                                           is_admin() || is_mod());
                           $smarty->assign('ib_name', Config::IB_NAME);
                           $smarty->assign('text', $text);
                           $smarty->assign('title', $title);
                           $smarty->assign('image', $image);
                           die($smarty->fetch('error.tpl'));
                       };
        }
        $this->handler = $handler;
    }

    function __invoke() {
        if (is_callable($this->handler)) {
            call_user_func_array($this->handler,
                    array_merge(func_get_args(),
                            array(kgettext($this->text), kgettext($this->title),
                                  $this->image)));
        }
    }
}

class KotobaError {
    private $title;
    private $text;
    private $image = NULL;

    function __construct($title, $text, $image = NULL) {
        $this->title = $title;
        $this->text = $text;
        if ($image == NULL) {
            $image = Config::DIR_PATH . '/img/errors/default_error.png';
        }
        $this->image = $image;
    }

    function getTitle() {
        return $this->title;
    }

    function getText() {
        return $this->text;
    }

    function getImage() {
        return $this->image;
    }

    function __invoke($smarty) {
        displayErrorPage($smarty, $this);
    }
}

class BumpLimitError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Boards.'),
            kgettext('Bump limit must be digit greater than zero.')
        );
    }
}
class SameUploadsError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Boards.'),
            kgettext('Upload policy from same files wrong format. It must be '
                     . 'string at 1 to 32 latin letters.')
        );
    }
}
class RangeBegError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Bans.'),
            kgettext('Begining of IP-address range has wrong format.')
        );
    }
}
class RangeEndError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Bans.'),
            kgettext('End of IP-address range has wrong format.')
        );
    }
}
class BansReasonError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Bans.'),
            kgettext('Ban reason has wrong format.')
        );
    }
}
class CategoryNameError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Categories.'),
            kgettext('Category name wrong format.')
        );
    }
}
class GroupNameError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Groups.'),
            kgettext('Group name wrong format.')
        );
    }
}
class LanguageCodeError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Languages.'),
            kgettext('ISO_639-2 code wrong format.')
        );
    }
}
class MacrochanTagNameError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Macrochan.'),
            kgettext('Macrochan tag name wrong format or not exist.')
        );
    }
}
class PopdownHandlerNameError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Popdown handlers.'),
            kgettext('Popdown handler name wrong format.')
        );
    }
}
class PostPasswordError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Posts.'),
            kgettext('Password wrong format. Password must be at 1 to 12 symbols length. Valid symbold is digits and latin letters.')
        );
    }
}
class SpamfilterPatternError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Spamfilter.'),
            kgettext('Wrong spamfilter pattern.')
        );
    }
}
class StylesheetNameError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Stylesheets.'),
            kgettext('Stylesheet name wrong format.')
        );
    }
}
class UploadHandlerNameError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Upload handlers.'),
            kgettext('Upload handler function name has a wrong format.')
        );
    }
}
class UploadTypeExtensionError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Extension has wrong format.')
        );
    }
}
class UploadTypeStoreExtensionError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Stored extension has wrong format.')
        );
    }
}
class UserGotoError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Users.'),
            kgettext('Redirection wrong format.')
        );
    }
}
class UserKeywordError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Users.'),
            kgettext('Keyword length must be 2 up to 32 symbols. Valid symbols is: latin letters, digits, underscore and dash.')
        );
    }
}
class UploadTypeThumbnailError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Upload types.'),
            kgettext('Thumbnail name for nonimage files has wrong format.')
        );
    }
}
class UserLinesPerPostError extends KotobaError {
    function __construct($min, $max) {
        parent::__construct(
            kgettext('Users.'),
            sprintf(kgettext('Count of lines per post must be in range %d-%d.'),
                    $min, $max)
        );
    }
}
class UserPostsPerThreadError extends KotobaError {
    function __construct($min, $max) {
        parent::__construct(
            kgettext('Users.'),
            sprintf(
                kgettext('Count of posts per thread must be in range %d-%d.'),
                $min,
                $max
            )
        );
    }
}
class UserThreadsPerPageError extends KotobaError {
    function __construct($min, $max) {
        parent::__construct(
            kgettext('Users.'),
            sprintf(
                kgettext('Count of threads per page must be in range %d-%d.'),
                $min,
                $max
            )
        );
    }
}
class BoardNameError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Boards.'),
            kgettext('Board name has wrong format. Board name must be string '
                     . 'length at 1 to 16 symbols. Symbols can be latin '
                     . 'letters and digits.')
        );
    }
}
class BoardNotFoundError extends KotobaError {
    function __construct($name) {
        parent::__construct(
            kgettext('Boards.'),
            sprintf(kgettext('Board name=%s not found.'), $name),
            Config::DIR_PATH . '/img/errors/board_not_found.png'
        );
    }
}
class MaxPageError extends KotobaError {
    function __construct($num) {
        parent::__construct(
            kgettext('Pages.'),
            sprintf(kgettext('Page number=%d not exist.'), $num)
        );
    }
}
class ThreadNotAvailableError extends KotobaError {
    function __construct($user_id, $original_post) {
        parent::__construct(
            kgettext('Threads.'),
            sprintf(kgettext('You id=%d have no permission to do it on thread '
                             . 'number=%d.'), $user_id, $original_post)
        );
    }
}
class ThreadNotAvailableIdError extends KotobaError {
    function __construct($user_id, $thread_id) {
        parent::__construct(
            kgettext('Threads.'),
            sprintf(kgettext('You id=%d have no permission to do it on thread '
                             . 'id=%d.'), $user_id, $thread_id)
        );
    }
}
class ThreadNotFoundError extends KotobaError {
    function __construct($original_post) {
        parent::__construct(
            kgettext('Threads.'),
            sprintf(kgettext('Thread number=%d not found.'), $original_post)
        );
    }
}
class ThreadNotFoundIdError extends KotobaError {
    function __construct($id) {
        parent::__construct(
            kgettext('Threads.'),
            sprintf(kgettext('Thread id=%d not found.'), $id)
        );
    }
}
class UserNotExistsError extends KotobaError {
    function __construct($keyword) {
        parent::__construct(
            kgettext('Users.'),
            sprintf(kgettext('User keyword=%s not exists.'), $keyword)
        );
    }
}
class StylesheetNotExistsError extends KotobaError {
    function __construct($id) {
        parent::__construct(
            kgettext('Stylesheets.'),
            sprintf(kgettext('Stylesheet id=%d not exist.'), $id)
        );
    }
}
class LanguageNotExistsError extends KotobaError {
    function __construct($id) {
        parent::__construct(
            kgettext('Languages.'),
            sprintf(kgettext('Language id=%d not exist.'), $id)
        );
    }
}
class BoardNotAvailableError extends KotobaError {
    function __construct($user_id, $board_id) {
        parent::__construct(
            kgettext('Boards.'),
            sprintf(kgettext('You id=%d have no permission to do it on board '
                             . 'id=%d.'), $user_id, $board_id)
        );
    }
}
class BoardNotFoundIdError extends KotobaError {
    function __construct($id) {
        parent::__construct(
            kgettext('Boards.'),
            sprintf(kgettext('Board id=%d not found.'), $id)
        );
    }
}
class CaptchaError extends KotobaError {
    function __construct($captcha) {
        parent::__construct(
            kgettext('Captcha.'),
            sprintf(kgettext('You enter wrong verification code %s.'), $captcha)
        );
    }
}
class MaxNameLengthError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Posts.'),
            kgettext('Name too long.')
        );
    }
}
class MaxSubjectLengthError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Posts.'),
            kgettext('Subject too long.')
        );
    }
}
class UploadIniSizeError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Upload limit upload_max_filesize from php.ini exceeded.')
        );
    }
}
class UploadFormSizeError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Upload limit MAX_FILE_SIZE from html form exceeded.')
        );
    }
}
class UploadPartialError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('File is loaded partially.')
        );
    }
}
class UploadNoTmpDirError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Temporary directory not found.')
        );
    }
}
class UploadCantWriteError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Cant write file to disk.')
        );
    }
}
class UploadExtensionError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('File uploading interrupted by extension.')
        );
    }
}
class UploadFiletypeNotSupportedError extends KotobaError {
    function __construct($ext) {
        parent::__construct(
            kgettext('Uploads.'),
            sprintf(kgettext('File type %s not supported for upload.'), $ext)
        );
    }
}
class MinImgSizeError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Image too small.')
        );
    }
}
class MaxFileLinkError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Link too long.')
        );
    }
}
class EmptyPostError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Posts.'),
            kgettext('No attachment and text is empty.')
        );
    }
}
class MaxTextLengthError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Posts.'),
            kgettext('Text too long.')
        );
    }
}
class SpamError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Spam.'),
            kgettext('Message detected as spam.')
        );
    }
}
class NonUnicodeError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Unicode.'),
            kgettext('Invlid unicode characters deteced.')
        );
    }
}
class MinImgDimentionsError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('Image dimensions too small.')
        );
    }
}
class MaxSmallImgSizeError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Uploads.'),
            kgettext('So small image cannot have so many data.')
        );
    }
}
class ThreadArchivedError extends KotobaError {
    function __construct($id) {
        parent::__construct(
            kgettext('Threads.'),
            sprintf(kgettext('Thread id=%d was archived.'), $id)
        );
    }
}
class ThreadClosedError extends KotobaError {
    function __construct($id) {
        parent::__construct(
            kgettext('Threads.'),
            sprintf(kgettext('Thread id=%d was closed.'), $id)
        );
    }
}
class PostNotFoundIdError extends KotobaError {
    function __construct($post_id, $user_id) {
        parent::__construct(
            kgettext('Posts.'),
            sprintf(
                kgettext('Post id=%d not found or user id=%d have no '
                         . 'permission.'),
                $post_id,
                $user_id
            )
        );
    }
}
class RequiedParamError extends KotobaError {
    function __construct($param) {
        parent::__construct(
            kgettext('Input params.'),
            sprintf(kgettext('Requied parameter %s not set.'), $param)
        );
    }
}
class GuestError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Guest.'),
            kgettext('Guests cannot do that.')
        );
    }
}
class NotModError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Moderator.'),
            kgettext('You are not moderator.')
        );
    }
}
class NotAdminError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Admin.'),
            kgettext('You are not admin.')
        );
    }
}
class ACLRuleExcessError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('ACL.'),
            kgettext('Board, Thread or Post is unique. Set one of it.')
        );
    }
}
class ACLRuleConflictError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('ACL.'),
            kgettext('Change permission cannot be set without view. Moderate '
                     . 'permission cannot be set without all others.')
        );
    }
}
class BoardTitleTooLongError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Boards.'),
            kgettext('Board title too long.')
        );
    }
}
class BoardAnnotationTooLongError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Boards.'),
            kgettext('Annotation too long.')
        );
    }
}
class NoEditableThreadsError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Threads.'),
            kgettext('No threads to edit.')
        );
    }
}
class WordTooLongError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Wordfilter.'),
            kgettext('Word too long.')
        );
    }
}
class SearchKeywordError extends KotobaError {
    function __construct() {
        parent::__construct(
            kgettext('Search.'),
            kgettext('Search keyword not set or too short.')
        );
    }
}

$KOTOBA_LAST_ERROR = NULL;

function kotoba_last_error() {
    global $KOTOBA_LAST_ERROR;
    return $KOTOBA_LAST_ERROR;
}

function kotoba_set_last_error($error) {
    global $KOTOBA_LAST_ERROR;
    $KOTOBA_LAST_ERROR = $error;
}

$ERRORS['NO_WORDS']
    = new Error('No words for search.', 'Search.');
$ERRORS['LONG_WORD']
    = new Error('One of search words is more than 60 characters.', 'Search.');
$ERRORS['UPLOAD_ERR_NO_FILE']
    = new Error('No file uploaded.', 'Uploads.');

function display_error_page($smarty, $error) {
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('text', $error->getText());
    $smarty->assign('title', $error->getTitle());
    $smarty->assign('image', $error->getImage());
    $smarty->display('error.tpl');
}
?>
