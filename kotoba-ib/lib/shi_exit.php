<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Shi exit callback.

require_once dirname(dirname(__FILE__)) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH
            . "/locale/{$_SESSION['language']}/messages.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ban = bans_check(get_remote_addr())) !== FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        $smarty->display('banned.tpl');

        session_destroy();
        exit(1);
    }

    // Check parameters.
    if (!isset($_GET['file']) || !ctype_digit($_GET['file'])) {
        throw new Exception('Bad file passed.');
    }
    if (!isset($_GET['time']) || !ctype_digit($_GET['time'])) {
        throw new Exception('Bad time passed.');
    }
    if ($_GET['painter'] !== 'shi_normal' && $_GET['painter'] !== 'shi_pro') {
        throw new Exception('Bad painter passed.');
    }

    $_SESSION['oekaki']['file'] = "{$_GET['file']}.png";
    $_SESSION['oekaki']['thumbnail'] = "{$_GET['file']}t.png";
    $_SESSION['oekaki']['ip'] = $ip;
    $_SESSION['oekaki']['time'] = $_GET['time'];
    $_SESSION['oekaki']['painter'] = $_GET['painter'];

    if (isset($_GET['thread']) && $_GET['thread'] != '' && isset($_GET['board']) && $_GET['board'] != '') {
        $board_name = boards_check_name($_GET['board']);
        if ($board_name === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        $thread_original_post = threads_check_original_post($_GET['thread']);
        header('Location: ' . Config::DIR_PATH . "/$board_name/$thread_original_post/");
    } else if(isset($_GET['board']) && $_GET['board'] != '') {
        $board_name = boards_check_name($_GET['board']);
        if ($board_name === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        header('Location: ' . Config::DIR_PATH . "/$board_name/");
    }

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>