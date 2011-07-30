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
    function __construct($min, $max) {
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
class ThreadNotFoundError extends KotobaError {
    function __construct($original_post) {
        parent::__construct(
            kgettext('Threads.'),
            sprintf(kgettext('Thread number=%d not found.'), $original_post)
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

$ERRORS['SPAM']
    = new Error('Message detected as spam.', 'Spam.');
$ERRORS['EMPTY_POST']
    = new Error('No attachment and text is empty.', 'Posts.');
$ERRORS['NON_UNICODE']
    = new Error('Invlid unicode characters deteced.', 'Unicode.');
$ERRORS['THREAD_ARCHIVED']
    = new Error('Thread id=%s was archived.', 'Threads.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['THREAD_CLOSED']
    = new Error('Thread id=%s was closed.', 'Threads.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['NO_WORDS']
    = new Error('No words for search.', 'Search.');
$ERRORS['LONG_WORD']
    = new Error('One of search words is more than 60 characters.', 'Search.');
$ERRORS['LANGUAGE_NOT_EXIST']
    = new Error('Language id=%s not exist.', 'Languages.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['POST_NOT_FOUND']
    = new Error('Post id=%d not found or user id=%d have no permission.',
                'Posts.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $post_id, $user_id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $post_id, $user_id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['SEARCH_KEYWORD']
    = new Error('Search keyword not set or too short.', 'Search.');
$ERRORS['STYLESHEET_NOT_EXIST']
    = new Error('Stylesheet id=%d not exist.', 'Stylesheets.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['THREAD_NOT_FOUND_ID']
    = new Error('Thread id=%d not found.', 'Threads.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['THREADS_EDIT']
    = new Error('No threads to edit.', 'Threads.');
$ERRORS['BOARD_NOT_ALLOWED']
    = new Error('You id=%d have no permission to do it on board id=%d.',
                'Boards.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $user_id, $board_id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text',
                                    sprintf($text, $user_id, $board_id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['BOARD_NOT_FOUND_ID']
    = new Error('Board id=%d not found.', 'Boards.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['ACL_RULE_EXCESS']
    = new Error('Board, Thread or Post is unique. Set one of it.', 'ACL.');
$ERRORS['ACL_RULE_CONFLICT']
    = new Error('Change permission cannot be set without view. Moderate '
                . 'permission cannot be set without all others.', 'ACL.');
$ERRORS['CAPTCHA']
    = new Error('You enter wrong verification code %s.', 'Captcha.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $captcha, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $captcha));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['GUEST']
    = new Error('Guests cannot do that.', 'Guest.');
$ERRORS['NOT_ADMIN']
    = new Error('You are not admin.', 'Admin.');
$ERRORS['NOT_MOD']
    = new Error('You are not moderator.', 'Moderator.');
$ERRORS['USER_NOT_EXIST']
    = new Error('User keyword=%s not exists.', 'Users.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $keyword, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $keyword));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['UPLOAD_ERR_INI_SIZE']
    = new Error('Upload limit upload_max_filesize from php.ini exceeded.',
                'Uploads.');
$ERRORS['UPLOAD_ERR_FORM_SIZE']
    = new Error('Upload limit MAX_FILE_SIZE from html form exceeded.',
                'Uploads.');
$ERRORS['UPLOAD_ERR_PARTIAL']
    = new Error('File is loaded partially.', 'Uploads.');
$ERRORS['UPLOAD_ERR_NO_FILE']
    = new Error('No file uploaded.', 'Uploads.');
$ERRORS['UPLOAD_ERR_NO_TMP_DIR']
    = new Error('Temporary directory not found.', 'Uploads.');
$ERRORS['UPLOAD_ERR_CANT_WRITE']
    = new Error('Cant write file to disk.', 'Uploads.');
$ERRORS['UPLOAD_ERR_EXTENSION']
    = new Error('File uploading interrupted by extension.', 'Uploads.');
$ERRORS['UPLOAD_FILETYPE_NOT_SUPPORTED']
    = new Error('File type %s not supported for upload.', 'Uploads.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $ext, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $ext));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['MAX_BOARD_TITLE']
    = new Error('Board title too long.', 'Boards.');
$ERRORS['MAX_NAME_LENGTH']
    = new Error('Name length too long.', 'Posts.');
$ERRORS['MAX_SUBJECT_LENGTH']
    = new Error('Subject too long.', 'Posts.');
$ERRORS['MAX_TEXT_LENGTH']
    = new Error('Text too long.', 'Posts.');
$ERRORS['MAX_TEXT_LENGTH']
    = new Error('Text too long.', 'Posts.');
$ERRORS['MAX_ANNOTATION']
    = new Error('Annotation too long.', 'Boards.');
$ERRORS['MAX_FILE_LINK']
    = new Error('Link too long.', 'Uploads.');
$ERRORS['MAX_SMALL_IMG_SIZE']
    = new Error('So small image cannot have so many data.', 'Uploads.');
$ERRORS['MIN_IMG_DIMENTIONS']
    = new Error('Image dimensions too small.', 'Uploads.');
$ERRORS['MIN_IMG_SIZE']
    = new Error('Image too small.', 'Uploads.');
$ERRORS['WORD_TOO_LONG']
    = new Error('Word too long.', 'Wordfilter.');


function display_error_page($smarty, $error) {
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('text', $error->getText());
    $smarty->assign('title', $error->getTitle());
    $smarty->assign('image', $error->getImage());
    $smarty->display('error.tpl');
}
?>
