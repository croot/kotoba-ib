<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Messages in russian.
 * @package api
 */

/**
 * 
 */
require_once '../config.php';

if (!isset($KOTOBA_LOCALE_MESSAGES)) {
    $KOTOBA_LOCALE_MESSAGES = array();
}
$_ = &$KOTOBA_LOCALE_MESSAGES;

$_['Cannot convert image to PNG format.']['rus'] = 'Не удалось преобразовать изображение в формат PNG.';
$_['Copy file.']['rus'] = 'Копирование файла.';
$_['Failed to copy file %s to %s.']['rus'] = 'Не удалось скопировать файл %s в %s.';
$_['Failed to open or create log file %s.']['rus'] = 'Не удалось открыть или создать файл лога %s.';
$_['GD doesn\'t support %s file type.']['rus'] = 'GD не поддерживает тип файла %s.';
$_['GD library.']['rus'] = 'Библиотека GD';
$_['Groups.']['rus'] = 'Группы.';
$_['Id of new group was not received.']['rus'] = 'Id новой группы небыл получен.';
$_['Image convertion.']['rus'] = 'Преобразование изображения.';
$_['Imagemagic doesn\'t support %s file type.']['rus'] = 'Imagemagic не поддерживает тип файла %s.';
$_['Imagemagic library.']['rus'] = 'Библиотека Imagemagic.';
$_['Logging.']['rus'] = 'Логирование.';

unset($_);
?>
