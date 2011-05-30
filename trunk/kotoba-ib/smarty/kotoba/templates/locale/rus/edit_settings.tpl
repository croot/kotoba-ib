{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of user settings page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $categories - categories.
    $boards - boards.
    $sess - Session information.
    $settings - Current settings.
    $languages - Languages.
    $stylesheets - Stylesheets.
    $favorites - Favorite threads.
    $hidden_threads - Hidden threads.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Мои настройки'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH categories=$categories boards=$boards}

<div class="logo">Мои настройки</div>
<br>
{$sess.name}={$sess.id}<br>
Сессия истекает через: {$sess.expire - intval(($sess.curtime - $settings.kotoba_session_start_time) / 60)}/{$sess.expire} минут.<br>
<br/>
<form action="{$DIR_PATH}/edit_settings.php" method="post">
<i>Введите ключевое слово, чтобы загрузить ваши настройки.</i><br/>
    <input type="text" name="keyword_load" size="32">
    <input type="submit" value="Загрузить">
</form>
<form action="{$DIR_PATH}/edit_settings.php" method="post">
<h4>Опции просмотра доски:</h4>
    <table border="0">
        <tr valign="top"><td>Число нитей на странице просмотра доски: </td><td><input type="text" name="threads_per_page" size="10" value="{$settings.threads_per_page}"></td></tr>
        <tr valign="top"><td>Число сообщений в нити на странице просмотра доски: </td><td><input type="text" name="posts_per_thread" size="10" value="{$settings.posts_per_thread}"></td></tr>
        <tr valign="top"><td>Сообщения, в которых число строк превышает это число,<br/>будут урезаны при просмотре доски: </td><td><input type="text" name="lines_per_post" size="10" value="{$settings.lines_per_post}"></td></tr>
        <tr valign="top"><td>Перенаправление: </td><td>
            <select name="goto">
                <option value="t"{if $settings.goto == 't'} selected{/if}>К нити</option>
                <option value="b"{if $settings.goto == 'b'} selected{/if}>К доске</option>
            </select>
        </td></tr>
    </table>
<h4>Другое:</h4>
    <table border="0">
        <tr valign="top"><td>Язык: </td><td><select name="language_id">{section name=j loop=$languages}<option value="{$languages[j].id}"{if $settings.language == $languages[j].code} selected{/if}>{$languages[j].code}</option>{/section}</select></td></tr>
        <tr valign="top"><td>Стиль оформления: </td><td><select name="stylesheet_id">{section name=i loop=$stylesheets}<option value="{$stylesheets[i].id}"{if $STYLESHEET == $stylesheets[i].name} selected{/if}>{$stylesheets[i].name}</option>{/section}</select></td></tr>
    </table>
<i>Введите ключевое слово, чтобы сохранить эти настройки.<br/>
В дальнейшем вы сможете загрузить их, введя ключевое слово.</i><br/>
    <input type="text" name="keyword_save" size="32">
    <input type="submit" value="Сохранить">
</form>
<h4>Избранные нити:</h4>
{if count($favorites) > 0}
{section name=i loop=$favorites}
<a href="{$DIR_PATH}/{$favorites[i].thread.board.name}/{$favorites[i].thread.original_post}/" title="Нажмите, чтобы перейти к нити">/{$favorites[i].thread.board.name}/{$favorites[i].thread.original_post}/</a> <span class="filetitle">{$favorites[i].post.subject}</span> <span class="postername">{$favorites[i].post.name}</span> {if $favorites[i].unread > 0}<span style="color:red;">{$favorites[i].unread} новых сообщений!</span>{else}0 новых сообщений.{/if} <a href="{$DIR_PATH}/favorites.php?action=delete&thread={$favorites[i].thread.id}" title="Удалить из избранного">[X]</a><br/>
{/section}
<br/><a href="{$DIR_PATH}/favorites.php?action=mark_all_readed" title="Отметить все нити прочитанными">Отметить все нити прочитанными</a><br/>
{else}
У вас нет избранных нитей.<br/>
{/if}
<h4>Скрытые нити:</h4>
{if count($hidden_threads) > 0}
{section name=i loop=$hidden_threads}
<a href="{$DIR_PATH}/unhide_thread.php?thread={$hidden_threads[i].thread}" title="Нажмите, чтобы отменить скрытие нити">/{$hidden_threads[i].board_name}/{$hidden_threads[i].thread_number}</a>
{/section}<br/>
{else}
У вас нет скрытых нитей.<br/>
{/if}
{include file='footer.tpl'}