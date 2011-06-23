<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Error messages in english.
 * @package englocale
 */

/**
 * Ensure what requirements to use functions and classes from this script are met.
 */
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . __FILE__ . '</b> but its not.');
}
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'errors.php'; })) {
    throw new Exception('File <b>errors.php</b> must be included and executed BEFORE '
                        . '<b>' . __FILE__ . '</b> but its not.');
}

/***/
$ERRORS['DEFAULT']
    = new Error('Default error occurs.', 'Default error.');
$ERRORS['BOARD_NOT_EXIST']
    = new Error('Board %s not exist.',
                'Board not exist.',
                Config::DIR_PATH . '/img/errors/board_not_exist.png',
                function ($text, $title, $image, $smarty, $board_name) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $board_name));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
?>