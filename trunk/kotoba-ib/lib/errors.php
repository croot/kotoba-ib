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
 * Ensure what requirements to use functions and classes from this script are met.
 */
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . __FILE__ . '</b> but its not.');
}
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'exceptions.php'; })) {
    throw new Exception('File <b>exceptions.php</b> must be included and executed BEFORE '
                        . '<b>' . __FILE__ . '</b> but its not.');
}

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
                    array_merge(array($this->text, $this->title, $this->image),
                            func_get_args()));
        }
    }
}

// Load default errors data array.
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
?>