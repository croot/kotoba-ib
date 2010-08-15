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
Code of hard ban page.

Variables:
    $DIR_PATH - path from servers document root to kotoba directory what contans index.php (see config.default).
    $STYLESHEET - stylesheet (see config.default).
*}
{include file='header.tpl' page_title='Banned at firewall' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET}
<br><br><a href="{$DIR_PATH}/">Home</a>
{include file='footer.tpl'}
