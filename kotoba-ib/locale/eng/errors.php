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
$K_ERROR['default_error']['image'] = Config::DIR_PATH . '/img/errors/default_error.png';
$K_ERROR['default_error']['text'] = 'Default error occurs.';
$K_ERROR['default_error']['title'] = 'Default error.';

$K_ERROR['board_not_exist']['handler'] = function ($smarty, $board_name) {
    global $K_ERROR;

    $e = &$K_ERROR['board_not_exist'];
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('ib_name', Config::IB_NAME);
    $e['text'] = sprintf($e['text'], $board_name);
    $smarty->assign('error', $e);

    die($smarty->fetch('error.tpl'));
};
$K_ERROR['board_not_exist']['image'] = Config::DIR_PATH . '/img/errors/board_not_exist.png';
$K_ERROR['board_not_exist']['text'] = 'Board %s not exist.';
$K_ERROR['board_not_exist']['title'] = 'Board not exist.';
?>