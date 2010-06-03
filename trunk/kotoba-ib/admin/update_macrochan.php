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
    $tags_removed = 0;
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
            $tags_removed++;
        }
    }
    echo "Tags removed: $tags_removed<br>\n";

    // Добавим теги, которых нет у нас.
    $tags = macrochan_tags_get_all();
    $tags_added = 0;
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
            $tags_added++;
        }
    }
    echo "Tags added: $tags_added<br>\n";

    // Удалим изображения, которых больше нет.
    $images = macrochan_images_get_all();
    $images_removed = 0;
    foreach ($images as $image) {
        $found = false;
        foreach ($MACROCHAN_IMAGES as $i) {
            if ($image['name'] == $i[1]) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            macrochan_images_delete_by_name($image['name']);
            $images_removed++;
        }
    }
    echo "Images removed: $images_removed<br>\n";

    // Добавим изображения, которых у нас нет.
    $images = macrochan_images_get_all();
    $images_added = 0;
    foreach ($MACROCHAN_IMAGES as $i) {
        $found = false;
        foreach ($images as $image) {
            if ($image['name'] == $i[1]) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            macrochan_images_add($i[1], $i[2], $i[3], $i[4], $i[5], $i[6],
                    $i[6]);
            $images_added++;
        }
    }
    echo "Images added: $images_added<br>\n";

    // Добавим новые связи тегов с изображениями.
    $tags_images = macrochan_tags_images_get_all();
    foreach ($MACROCHAN_TAGS_IMAGES as $ti) {

        // Найдём имя тега.
        foreach ($MACROCHAN_TAGS as $t) {
            if ($ti[0] == $t[0]) {

                // Найдём имя изображения.
                foreach ($MACROCHAN_IMAGES as $i) {
                    if ($ti[1] == $i[0]) {

                        // Проверим, нет ли у нас уже связи этого тега с этим
                        // изображением. Если нет, то добавим связь.
                        if (macrochan_tags_images_get($t[1], $i[1]) === null) {
                            macrochan_tags_images_add($t[1], $i[1]);
                        }
                    }
                }
            }
        }
    }

	DataExchange::releaseResources();
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>