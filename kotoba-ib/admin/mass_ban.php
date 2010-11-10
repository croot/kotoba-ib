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
 * Скрипт бана по списку.
 * 
 * file - Файл, содержащий диапазоны блокируемых адресов. Каждый новый диапазон
 *        должен начинаться с новой строки (быть разделены символом \n).
 */

/***/
require '../config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Инициализация.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'],
                                    $_SESSION['stylesheet']);

    // Проверка, не заблокирован ли клиент.
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

    // Проверка доступа и запись в лог.
    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    call_user_func(Logging::$f['MASS_BAN']);
    Logging::close_log();

    
    if (isset($_FILES['file'])) {
        check_upload_error($_FILES['file']['error']);
        $list = split("\n", file_get_contents($_FILES['file']['tmp_name']));
        foreach ($list as $range) {
            if ($range) {
                list($range_beg, $range_end) = split(' ', $range);
                echo "Ban from $range_beg to $range_end</br>";
            }
        }
    }

    // Формирование кода страницы и вывод.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->display('mass_ban.tpl');

    // Освобождение ресурсов и очиска.
    DataExchange::releaseResources();

    exit(0);
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
