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
require_once '../config.php';
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

    function __invoke($smarty) {
        $smarty->assign('ib_name', Config::IB_NAME);
        $smarty->assign('text', $this->text);
        $smarty->assign('title', $this->title);
        $smarty->assign('image', $this->image);
        $smarty->display('error.tpl');
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
$ERRORS['THREAD_NOT_FOUND']
    = new Error('Thread number=%d not found.', 'Threads.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $name, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $name));
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
$ERRORS['BOARD_NOT_FOUND']
    = new Error('Board name=%s not found.', 'Boards.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $name, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $name));
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
$ERRORS['MAX_PAGE']
    = new Error('Page number=%d not exist.', 'Pages.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $num, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $num));
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
$ERRORS['THREAD_NOT_ALLOWED']
    = new Error('You id=%d have no permission to do it on thread id=%d.',
                'Threads.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $user_id, $thread_id, $text, $title,
                          $image) {

                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text',
                                    sprintf($text, $user_id, $thread_id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
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
$ERRORS['BOARD_NAME']
    = new Error('Board name wrong format. Board name must be string length at '
                . '1 to 16 symbols. Symbols can be latin letters and digits.',
                'Boards.');
?>
