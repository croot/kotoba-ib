{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of thread view page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $thread - thread.
    $enable_translation - translation flag (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $banner - banner.
    $ib_name - imageboard name  (see config.default).
    $MAX_FILE_SIZE - maximum size of uploaded file in bytes (see config.default).
    $board - board.
    $name - name.
    $quote (optional) - post number what will be added to reply form.
    $oekaki - oekaki data.
    $enable_macro - lacrochan integration flag (see config.default).
    $macrochan_tags - macrochan tags.
    $enable_youtube - loutube video posting flag (see config.default).
    $enable_captcha - laptcha flag (see config.default).
    $password - password.
    $goto - redirection.
    $upload_types - upload types on this board.
    $enable_shi - painting flag (see config.default).
    $is_moderatable - is current thread is moderatable.
    $threads - thread in array.
    $threads_html - html code of threads.
    $hidden_threads - hidden threads.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="Просмотр нити `$thread.original_post`"}

{if $enable_translation}<script type="text/javascript" src="http://www.google.com/jsapi"></script>{else}<!-- Translation disabled -->{/if}

<script type="text/javascript">var DIR_PATH = '{$DIR_PATH}';</script>
<script src="{$DIR_PATH}/kotoba.js"></script>
<script src="{$DIR_PATH}/protoaculous-compressed.js"></script>
{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

{if isset($banner)}
<div class="logo"><img src="{$DIR_PATH}/misc/img/{$banner.name}" alt="{$banner.name}" width="{$banner.widht}" height="{$banner.height}"></div>
{/if}
<div class="logo">{$ib_name} — /{$board.name}/{$thread.original_post}</div>
<hr>

<div class="postarea">
<form name="postform" id="postform" action="{$DIR_PATH}/reply.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE}">
<table align="center" border="0">
<tbody>
{if !$board.force_anonymous}
    <tr valign="top"><td class="postblock">Имя: </td><td><input type="text" name="name" size="30" value="{$name}"></td></tr>
{/if}
    <tr valign="top"><td class="postblock">Тема: </td><td><input type="text" name="subject" size="56"> <input type="submit" value="Ответить"></td></tr>
    <tr valign="top"><td class="postblock">Сообщение: </td><td><textarea name="text" rows="7" cols="50">{if isset($quote)}>>{$quote}{/if}</textarea><img id="resizer" src="{$DIR_PATH}/flower.png"></td></tr>
{if $thread.with_attachments || ($thread.with_attachments === null && $board.with_attachments)}
    <tr valign="top"><td class="postblock">Файл: </td><td><input type="file" name="file" size="54"> Спойлер: <input type="checkbox" name="spoiler" value="1" /></td></tr>
    {if isset($oekaki)}<tr valign="top"><td class="postblock">Мой рисунок: </td><td><a href="{$DIR_PATH}/shi/{$oekaki.file}"><img border="0" src="{$DIR_PATH}/shi/{$oekaki.thumbnail}" align="middle" /></a> Использовать вместо файла: <input type="checkbox" name="use_oekaki" value="1"></td></tr>{else}<!-- Oekaki disabled -->{/if}

{if $enable_macro}
    <tr valign="top"><td class="postblock">Макрос: </td>
    <td>
        <select name="macrochan_tag">
        <option value="" selected></option>
        {section name=i loop=$macrochan_tags}
            <option value="{$macrochan_tags[i].name}">{$macrochan_tags[i].name}</option>
        {/section}
        </select>
    </td>
    </tr>
{/if}
    {if $enable_youtube}<tr valign="top"><td class="postblock">Youtube: </td><td><input type="text" name="youtube_video_code" size="30"></td></tr>{else}<!-- Youtube video posting disabled -->{/if}

{/if}
    {if $enable_captcha}<tr valign="top"><td class="postblock">Капча: </td><td><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/captcha/image.php?' + Math.random(); return false"><img border="0" id="captcha" src="{$DIR_PATH}/captcha/image.php" alt="Kotoba capcha v0.4" align="middle" /></a> <input type="text" name="captcha_code" size="10" maxlength="6" /></tr>{else}<!-- Captcha disabled -->{/if}

    <tr valign="top"><td class="postblock">Пароль: </td><td><input type="password" name="password" size="30" value="{$password}"></td></tr>
    <tr valign="top"><td class="postblock">Перейти: </td><td>(нить: <input type="radio" name="goto" value="t"{if $goto == 't'} checked{/if}>) (доска: <input type="radio" name="goto" value="b"{if $goto == 'b'} checked{/if}>)</td></tr>
    <tr valign="top"><td class="postblock">Sage: </td><td><input type="checkbox" name="sage" value="sage"></td></tr>
    <tr valign="top"><td colspan = "2" class="rules">
        <ul class="infolist">
        <li>Типы файлов, доступных для загрузки: {section name=i loop=$upload_types} {$upload_types[i].extension}{/section}</li>
        <li>Бамплимит доски: {$board.bump_limit}</li>
        <li>Бамплимит нити: {$thread.bump_limit}</li>
        <li>Число сообщений: {$thread.posts_count}</li>
        <li><a href="{$DIR_PATH}/catalog.php?board={$board.name}">Каталог нитей</a></li>
        </ul>
        {$board.annotation}
    </td></tr>
</tbody>
</table>
<input type="hidden" name="t" value="{$thread.id}">
</form>
{if $thread.with_attachments || ($thread.with_attachments === null && $board.with_attachments) && $enable_shi}
<form action="{$DIR_PATH}/lib/shi_applet.php" method="post">
    <input type="hidden" name="board" value="{$board.name}">
    <input type="hidden" name="thread" value="{$thread.original_post}">
    Наоекакать: <select name="painter">
        <option value="shi_normal" selected="selected">Shi Normal</option>
        <option value="shi_pro">Shi Pro</option>
    </select>
    Ширина: <input type="text" name="x" size="3" value="640" />
    Высота: <input type="text" name="y" size="3" value="480" />
    <input type="submit" value="Рисовать" />
</form>{else}<!-- Oekaki disabled -->{/if}

</div>
{literal}<script type="text/javascript">
<!--
var mytextarea = document.forms.postform.text;
mytextarea.style.width = mytextarea.clientWidth + 'px';
mytextarea.style.height = mytextarea.clientHeight + 'px';
if(navigator.userAgent.indexOf("WebKit") < 0) {
    resizeMaster.setResizer(document.getElementById("resizer"));
}
else {
    // Reset alignment of postform to kusaba default for chrome users,
    // because chrome have native textarea resizing support.
    for(var stylesheetKey in document.styleSheets) {
        if(document.styleSheets[stylesheetKey].href.indexOf("img_global.css") >= 0) {
            for(var ruleKey in document.styleSheets[stylesheetKey].cssRules) {
                if(document.styleSheets[stylesheetKey].cssRules[ruleKey].selectorText.indexOf("postarea table") >= 0)
                    document.styleSheets[stylesheetKey].cssRules[ruleKey].style.margin = "0px auto"
            }
        }
    }
}
//-->
</script>{/literal}
{if $is_moderatable}{include file='threads_settings_list.tpl' boards=$boards threads=$threads DIR_PATH=$DIR_PATH}{/if}
<hr>{$threads_html}
{if count($hidden_threads) > 0}
Скрытые вами нити:
{section name=i loop=$hidden_threads}
<a href="{$DIR_PATH}/unhide_thread.php?thread={$hidden_threads[i].thread}" title="Нажмите, чтобы отменить скрытие нити.">{$hidden_threads[i].thread_number}</a>
{/section}
{/if}<br>
{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<div class="footer" style="clear: both;">- Kotoba 1.1 -</div>
{include file='footer.tpl'}