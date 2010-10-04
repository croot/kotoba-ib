<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Favorites works script.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    // Check if remote host was banned.
    if (($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if (($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Read parameters.
    $action = null;
    $thread = null;
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } elseif (isset($_POST['action'])) {
        $action = $_POST['action'];
    }
    if (isset($_GET['thread'])) {
        $thread = $_GET['thread'];
    } elseif (isset($_POST['thread'])) {
        $thread = $_POST['thread'];
    }

    //
    if ($action === "mark_all_readed") {
        favorites_mark_readed($_SESSION['user']);
    } else {
        switch ($action) {
            case "add":
                favorites_add($_SESSION['user'], threads_check_id($thread));
                break;
            case "delete":
                favorites_delete($_SESSION['user'], threads_check_id($thread));
                break;
            case "mark_readed":
                favorites_mark_readed($_SESSION['user'], threads_check_id($thread));
                break;
            default:
                break;
        }
    }

    // Clean up.
    DataExchange::releaseResources();

    // Set redirection.
    header('Location: ' . Config::DIR_PATH . "/edit_settings.php");

    exit(0);
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
