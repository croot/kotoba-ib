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
$ERRORS['BOARD_NOT_FOUND']
    = new Error('Board %s not found.',
                'Board not found.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $board_name, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $board_name));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['BOARD_NOT_FOUND_ID']
    = new Error('Board id %s not found.',
                'Board not found.',
                Config::DIR_PATH . '/img/errors/board_not_found.png',
                function ($smarty, $board_id, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $board_id));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['ACL_NO_RULES']
    = new Error('No one rule in ACL.',
                'No rules in ACL.');
$ERRORS['KOTOBA_INTVAL']
    = new Error('Object cannot be cast to intger. See description to intval() '
                . 'function in PHP documentation.',
                'Cast to integer.');
$ERRORS['MAX_PAGE']
    = new Error('Page number %s not exist.',
                'Page number.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $page, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $page));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
// TODO Это описание ошибки ни черта не понятное да ещё и кривое.
$ERRORS['ACL_RULE_EXCESS']
    = new Error('Board, Thread or Post is unique.',
                'Data excess.');
$ERRORS['ACL_RULE_CONFLICT']
    = new Error('Change permission cannot be set without view. Moderate '
                . 'permission cannot be set without all others.',
                'Permission error.');
$ERRORS['CAPTCHA']
    = new Error('Your code is "%s" but expected code is "%s".',
                'Captcha code incorrect.',
                Config::DIR_PATH . '/img/errors/default_error.png',
                function ($smarty, $ccode, $exp_ccode, $text, $title, $image) {
                    $smarty->assign('show_control', is_admin() || is_mod());
                    $smarty->assign('ib_name', Config::IB_NAME);
                    $smarty->assign('text', sprintf($text, $ccode, $exp_ccode));
                    $smarty->assign('title', $title);
                    $smarty->assign('image', $image);
                    die($smarty->fetch('error.tpl'));
                });
$ERRORS['REMOTE_ADDR']
    = new Error('Remote address is not an IP address.',
                'Invalid remote address.');
?>