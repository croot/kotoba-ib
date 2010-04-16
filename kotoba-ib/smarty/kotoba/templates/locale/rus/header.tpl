{* Smarty *}
{*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************
 *********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Код заголовка всех страниц Котобы.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль (см. config.default).
    $page_title - заголовок страницы.
*}
<html>
<head>
    <title>{$page_title}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="{$DIR_PATH}/{$STYLESHEET}">
</head>
<body>
<div class="adminbar">[<a href="{$DIR_PATH}/edit_settings.php">Настройки</a>] [<a href="{$DIR_PATH}/manage.php">Управление</a>]</div>
