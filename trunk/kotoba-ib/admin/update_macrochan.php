<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/
// Скрипт обновления данных с макрочана.

require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    // Возможно завершение работы скрипта.
    bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));

    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }

    Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_UPDATE_MACROCHAN'],
            $_SESSION['user'], $_SERVER['REMOTE_ADDR']),
        Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');

    // download data

    include Config::ABS_PATH . '/res/macrochan_data.php';

    // Удалим теги, которых больше нет.
    $tags = macrochan_tags_get_all();
    foreach ($tags as $tag) {
        $found = false;
        foreach ($MACROCHAN_TAGS as $t) {
            if ($tag['name'] == $t[1]) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            macrochan_tags_delete_by_name($tag['name']);
        }
    }

    // Добавим теги, которых нет у нас.
    $tags = macrochan_tags_get_all();
    foreach ($MACROCHAN_TAGS as $t) {
        $found = false;
        foreach ($tags as $tag) {
            if ($tag['name'] == $t[1]) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            macrochan_tags_add($t[1]);
        }
    }

	DataExchange::releaseResources();
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>