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
Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления.
	$id - идентификактор пользователя
*}
{include file='header.tpl' page_title='Мой id' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
Ваш id: {$id}
{include file='footer.tpl'}