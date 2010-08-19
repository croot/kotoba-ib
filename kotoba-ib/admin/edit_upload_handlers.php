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

/**
 * Скрипт редактирования обработчиков загружаемых файлов.
 * @package admscripts
 */

require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';

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

    // Check permission and write message to log file.
    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    Logging::write_msg(Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log',
            Logging::$messages['ADMIN_FUNCTIONS_EDIT_UPLOAD_HANDLERS'],
            $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
    Logging::close_log();

    $upload_handlers = upload_handlers_get_all();
    $reload_upload_handlers = false;    // Были ли произведены изменения.

    // Регистрация обработчика загружаемых файлов.
    if (isset($_POST['new_upload_handler']) && $_POST['new_upload_handler'] !== '') {
        upload_handlers_add(upload_handlers_check_name($_POST['new_upload_handler']));
        $reload_upload_handlers = true;
    }

    // Удаление обработчика загружаемых файлов.
    foreach ($upload_handlers as $handler) {
        if (isset($_POST['delete_' . $handler['id']])) {
            upload_handlers_delete($handler['id']);
            $reload_upload_handlers = true;
        }
    }

    // Вывод формы редактирования.
    if ($reload_upload_handlers) {
        $upload_handlers = upload_handlers_get_all();
    }
    $smarty->assign('upload_handlers', $upload_handlers);
    $smarty->display('edit_upload_handlers.tpl');

    DataExchange::releaseResources();
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
