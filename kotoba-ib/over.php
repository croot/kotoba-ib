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

// Скрипт, предоставляющий данные для оверчана.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';

try {
    // Получение данных о досках и категориях.
    $boards = boards_get_all();
    $categories = categories_get_all();

    // Формирование кода страницы и вывод.
    $out = '[';
    foreach ($categories as $category) {
        foreach ($boards as $board) {
            if ($category['id'] == $board['category']) {
                $out .= "<a href=\"/{$board['name']}/\">{$board['name']}</a> /\n";
            }
        }
        $out = mb_substr($out, 0, mb_strlen($out, Config::MB_ENCODING) - 3, Config::MB_ENCODING);
        $out .= " |\n";
    }
    $out = mb_substr($out, 0, mb_strlen($out, Config::MB_ENCODING) - 3, Config::MB_ENCODING);
    $out .= ']';
    echo $out;

    // Освобождение ресурсов и очистка.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>