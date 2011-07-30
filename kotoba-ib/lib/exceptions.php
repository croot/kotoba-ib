<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Exceptions.
 * @package api
 */

/**
 * 
 */
require_once dirname(dirname(__FILE__)) . '/config.php';
require_once Config::ABS_PATH . '/lib/kgettext.php';

/**
 * Kotoba extension for default Exception class.
 * @package exceptions
 */
abstract class KotobaException extends Exception {

    /**
     * Message data. All neccessary data to display information about exception.
     */
    protected $message_data;

    /**
     * Returns string representation of common exception.
     * @return string
     */
    public function __toString() {
        $_ = htmlentities(parent::__toString(), ENT_QUOTES,
                          Config::MB_ENCODING);
        return nl2br($_);
    }

    /**
     * Returns message data.
     */
    function getMessageData() {
        return $this->message_data;
    }
}

/**
 * Common exceptions.
 * @package exceptions
 */
abstract class CommonException extends KotobaException {}
/**
 * Search exceptions.
 * @package exceptions
 */
class SearchException extends KotobaException {}
/**
 * No data exception.
 * @package exceptions
 */
class NodataException extends KotobaException {}
/**
 * Data format exception.
 * @package exceptions
 */
class FormatException extends KotobaException {}
/**
 * Registration, authorization, identification and access violation exception.
 * @package exceptions
 */
class PermissionException extends KotobaException {}
/**
 * Data exchange exceptions.
 * @package exceptions
 */
class DataExchangeException extends KotobaException {}
/**
 * Upload exceptions.
 * @package exceptions
 */
class UploadException extends KotobaException {}
/**
 * Limit exceptions.
 * @package exceptions
 */
class LimitException extends KotobaException {}

class DBException extends CommonException {
    public function __construct($err) {
        $_['title'] = kgettext('Database error.');
        $_['text'] = kgettext('Error in database: %s.');
        $_['text'] = sprintf($_['text'], $err);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class IntvalException extends CommonException {
    public function __construct() {
        $_['title'] = 'Intval.';
        $_['text'] = kgettext('Object cannot be cast to intger. See '
                              . 'description to intval() function.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class StrvalException extends CommonException {
    public function __construct() {
        $_['title'] = 'Strval.';
        $_['text'] = kgettext('Arrays and Objects what not implements '
                              . '__toString() method, cannot be cast to '
                              . 'string. See description to strval() '
                              . 'function.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class CopyFileException extends CommonException {
    public function __construct($src, $dest) {
        $_['title'] = kgettext('Copy file.');
        $_['text'] = kgettext('Failed to copy file %s to %s.');
        $_['text'] = sprintf($_['text'], $src, $dest);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class GDFiletypeException extends CommonException {
    public function __construct($ext) {
        $_['title'] = kgettext('GD library.');
        $_['text'] = kgettext('GD doesn\'t support %s file type.');
        $_['text'] = sprintf($_['text'], $ext);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class GroupsAddException extends CommonException {
    public function __construct() {
        $_['title'] = kgettext('Groups.');
        $_['text'] = kgettext('Id of new group was not received.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class ImagemagicFiletypeException extends CommonException {
    public function __construct($ext) {
        $_['title'] = kgettext('Imagemagic library.');
        $_['text'] = kgettext('Imagemagic doesn\'t support %s file type.');
        $_['text'] = sprintf($_['text'], $ext);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class LogFileException extends CommonException {
    public function __construct($path) {
        $_['title'] = kgettext('Logging.');
        $_['text'] = kgettext('Failed to open or create log file %s.');
        $_['text'] = sprintf($_['text'], $path);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class CreateLinkException extends CommonException {
    public function __construct($src, $dest) {
        $_['title'] = kgettext('Link creation.');
        $_['text'] = kgettext('Failed to create hard link %s for file %s.');
        $_['text'] = sprintf($_['text'], $src, $dest);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class NoImageLibraryException extends CommonException {
    public function __construct() {
        $_['title'] = kgettext('Image libraries.');
        $_['text'] = kgettext('Image libraries disabled or doesn\'t work.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class SessionStartException extends CommonException {
    public function __construct() {
        $_['title'] = kgettext('Session.');
        $_['text'] = kgettext('Failed to start session.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class SetLocaleException extends CommonException {
    public function __construct() {
        $_['title'] = kgettext('Locale.');
        $_['text'] = kgettext('Setup locale failed.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class GroupsNotExistsException extends NodataException {
    public function __construct() {
        $_['title'] = kgettext('Groups.');
        $_['text'] = kgettext('No one group exists.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class LanguageNotExistsException extends NodataException {
    public function __construct($id) {
        $_['title'] = kgettext('Languages.');
        $_['text'] = kgettext('Language id=%s not exist.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class LanguagesNotExistsException extends NodataException {
    public function __construct() {
        $_['title'] = kgettext('Languages.');
        $_['text'] = kgettext('No one language exists.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class PostNotFoundException extends NodataException {
    public function __construct($post_id, $user_id) {
        $_['title'] = kgettext('Posts.');
        $_['text'] = kgettext('Post id=%d not found or user id=%d have no '
                              . 'permission.');
        $_['text'] = sprintf($_['text'], $post_id, $user_id);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class RequestMethodException extends NodataException {
    public function __construct() {
        $_['title'] = kgettext('Request method.');
        $_['text'] = kgettext('Request method not defined or unexpected.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class StylesheetNotExistsException extends NodataException {
    public function __construct($id) {
        $_['title'] = kgettext('Stylesheets.');
        $_['text'] = kgettext('Stylesheet id=%d not exist.');
        $_['text'] = sprintf($_['text'], $id);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class StylesheetsNotExistsException extends NodataException {
    public function __construct() {
        $_['title'] = kgettext('Stylesheets.');
        $_['text'] = kgettext('No one stylesheet exists.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class UserWithoutGroupException extends NodataException {
    public function __construct($id) {
        $_['title'] = kgettext('Users.');
        $_['text'] = kgettext('User id=%d has no group.');
        $_['text'] = sprintf($_['text'], $id);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class UsersNotExistsException extends NodataException {
    public function __construct() {
        $_['title'] = kgettext('Users.');
        $_['text'] = kgettext('No one user exists.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class RemoteAddressException extends CommonException {
    public function __construct() {
        $_['title'] = kgettext('Bans.');
        $_['text'] = kgettext('Remote address is not an IP address.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class AclNoRulesException extends NodataException {
    public function __construct() {
        $_['title'] = kgettext('ACL.');
        $_['text'] = kgettext('No one rule in ACL.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class FileHashException extends UploadException {
    public function __construct($path) {
        $_['title'] = kgettext('Uploads.');
        $_['text'] = kgettext('File %s hash calculation failed.');
        $_['text'] = sprintf($_['text'], $path);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class MoveFileException extends UploadException {
    public function __construct($src, $dest) {
        $_['title'] = kgettext('Uploads.');
        $_['text'] = kgettext('Cant move file %s to %s.');
        $_['text'] = sprintf($_['text'], $src, $dest);
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}
class UnknownUploadTypeException extends UploadException {
    public function __construct() {
        $_['title'] = kgettext('Uploads.');
        $_['text'] = kgettext('Unknown upload type.');
        $_['image'] = Config::DIR_PATH . '/img/exceptions/default.png';
        $this->message_data = $_;
        parent::__construct($_['text']);
    }
}

function display_exception_page($smarty, $exception, $show_control) {
    $md = $exception->getMessageData();
    $smarty->assign('show_control', $show_control);
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('title', $md['title']);
    $smarty->assign('image', $md['image']);
    $smarty->assign('text', $md['text']);
    $smarty->assign('debug_info', $exception->__toString());
    $smarty->display('exception.tpl');
}
?>
