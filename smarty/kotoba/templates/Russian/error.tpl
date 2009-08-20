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
    $KOTOBA_DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $msg - текст сообщения об ошибке.
*}
{include file='header.tpl' kotoba_dir=$KOTOBA_DIR_PATH page_title='Ошибка'}
<span class="error">{$msg}</span>
{include file='footer.tpl'}