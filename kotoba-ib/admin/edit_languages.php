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

// Скрипт редактирования языков.

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
            Logging::$messages['ADMIN_FUNCTIONS_EDIT_LANGUAGES'],
            $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
    Logging::close_log();

    $languages = languages_get_all();
    $reload_languages = false;	// Были ли произведены изменения.

    // Добавление нового языка.
    if (isset($_POST['new_language']) && $_POST['new_language'] !== '') {
        $code = languages_check_code($_POST['new_language']);
        languages_add($code);
        create_language_directories($code);
        $reload_languages = true;
    }

    // Удаление языков.
    foreach ($languages as $language) {
        if (isset($_POST['delete_' . $language['id']])) {
            languages_delete($language['id']);
            $reload_languages = true;
        }
    }

    // Вывод формы редактирования.
    if ($reload_languages) {
        $languages = languages_get_all();
    }
    $smarty->assign('languages', $languages);
    $smarty->display('edit_languages.tpl');

    DataExchange::releaseResources();
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>