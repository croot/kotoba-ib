{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Admin bar.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $show_control - show link to manage page.
*}
<div class="adminbar">
[<a href="{$DIR_PATH}/edit_settings.php">Настройки</a>]
[Архив]
[<a href="{$DIR_PATH}/search.php">Поиск</a>]
[<a href="{$DIR_PATH}/">Главная</a>]
{if $show_control}[<a href="{$DIR_PATH}/manage.php">Управление</a>]
{/if}
</div>