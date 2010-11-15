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

// Скрипт редактирования связей загружаемых типов файлов с досками.

require '../config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

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

    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    call_user_func(Logging::$f['EDIT_BOARD_UPLOAD_TYPES_USE']);

    $upload_types = upload_types_get_all();
    $boards = boards_get_all();
    $board_upload_types = board_upload_types_get_all();
    $reload_board_upload_types = false; // Были ли произведены изменения.

    if (isset($_POST['submited'])) {

        // Новая привязка типа загружаемого файла к доске.
        if (isset($_POST['new_bind_board'])
                && isset($_POST['new_bind_upload_type'])
                && $_POST['new_bind_board'] !== ''
                && $_POST['new_bind_upload_type'] !== '') {

            board_upload_types_add(boards_check_id($_POST['new_bind_board']),
            upload_types_check_id($_POST['new_bind_upload_type']));
            $reload_board_upload_types = true;
        }

        // Удаление привязок типов загружаемых файлов к доскам.
        foreach ($board_upload_types as $board_upload_type) {
            if (isset($_POST["delete_{$board_upload_type['board']}_{$board_upload_type['upload_type']}"])) {
                board_upload_types_delete($board_upload_type['board'],
                $board_upload_type['upload_type']);
                $reload_board_upload_types = true;
            }
        }
    }

    // Вывод формы редактирования.
    if ($reload_board_upload_types) {
        $board_upload_types = board_upload_types_get_all();
    }
    $smarty->assign('upload_types', $upload_types);
    $smarty->assign('boards', $boards);
    $smarty->assign('board_upload_types', $board_upload_types);
    $smarty->display('edit_board_upload_types.tpl');

    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>