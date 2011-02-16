{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of board view page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $ib_name - imageboard name  (see config.default).
    $board - board.
    $page - page number.
    $enable_translation - translation flag (see config.default).
    $show_control - show link to manage page.
    $boards - boards.
    $banner - banner.
    $pages - pages numbers.
    $MAX_FILE_SIZE - maximum size of uploaded file in bytes (see config.default).
    $name - name.
    $oekaki - oekaki data.
    $enable_macro - lacrochan integration flag (see config.default).
    $macrochan_tags - macrochan tags.
    $enable_youtube - loutube video posting flag (see config.default).
    $enable_captcha - laptcha flag (see config.default).
    $password - password.
    $goto - redirection.
    $upload_types - upload types on this board.
    $enable_shi - painting flag (see config.default).
    $threads_html - html code of threads.
    $hidden_threads - hidden threads.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="`$ib_name` — /`$board.name`/ `$board.title`. Board view, page $page"}


{if $enable_translation}
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
{literal}<script type="text/javascript">
<!--
google.load("language", "1");
//-->
</script>{/literal}
{else}
<!-- Translation disabled -->
{/if}
<script type="text/javascript">var DIR_PATH = '{$DIR_PATH}';</script>
<script src="{$DIR_PATH}/protoaculous-compressed.js"></script>
<script src="{$DIR_PATH}/kotoba.js"></script>
{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}


{if isset($banner)}
<div class="logo"><img src="{$DIR_PATH}/misc/img/{$banner.name}" alt="{$banner.name}" width="{$banner.widht}" height="{$banner.height}"></div>
{/if}
<div class="logo">{$ib_name} — /{$board.name}/ {$board.title}</div>
{include file='pages_list.tpl' board_name=$board.name pages=$pages page=$page}


<hr>
<div class="postarea">
<form name="postform" id="postform" action="{$DIR_PATH}/create_thread.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE}">
<table class="postform">
<tbody>
{if !$board.force_anonymous}
    <tr valign="top"><td class="postblock">Name: </td><td><input type="text" name="name" size="28" maxlength="64" accesskey="n" value="{$name}"></td></tr>
{/if}
    {if $enable_captcha}<tr valign="top"><td class="postblock"><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/captcha/image.php?' + Math.random(); return false"><img border="0" id="captcha" src="{$DIR_PATH}/captcha/image.php" alt="Kotoba capcha v0.4" align="middle" /></a></td><td><input type="text" name="captcha_code" size="28" maxlength="64" accesskey="f"></td></tr>{else}<!-- Captcha disabled -->{/if}

    <tr valign="top"><td class="postblock">Subject: </td><td><input type="text" name="subject" size="35" maxlength="75" accesskey="s"> <input type="submit" value="Create thread"></td></tr>
    <tr valign="top"><td class="postblock">Message: </td><td><textarea name="text" cols="48" rows="4" accesskey="m"></textarea><img id="resizer" src="{$DIR_PATH}/flower.png" title="Drag to resize text area."></td></tr>
{if $board.with_attachments}
    <tr valign="top"><td class="postblock">File: </td><td><input type="file" name="file" size="35" accesskey="f"> Spoiler: <input type="checkbox" name="spoiler" value="1" /></td></tr>
    {if isset($oekaki)}<tr valign="top"><td class="postblock">My art: </td><td><a href="{$DIR_PATH}/shi/{$oekaki.file}"><img border="0" src="{$DIR_PATH}/shi/{$oekaki.thumbnail}" align="middle" /></a> Use instead: <input type="checkbox" name="use_oekaki" value="1"></td></tr>{else}<!-- Oekaki disabled -->{/if}

    {if $enable_macro}<tr valign="top"><td class="postblock">Macro: </td>
    <td>
        <select name="macrochan_tag">
        <option value="" selected></option>
        {section name=i loop=$macrochan_tags}
            <option value="{$macrochan_tags[i].name}">{$macrochan_tags[i].name}</option>
        {/section}
        </select>
    </td>
    </tr>{else}<!-- Macrochan integration disabled -->{/if}

    {if $enable_youtube}<tr valign="top"><td class="postblock">Youtube: </td><td><input type="text" name="youtube_video_code" size="30"></td></tr>{else}<!-- YouTube video posting disabled -->{/if}
{else}
    <!-- Attachments disabled -->
{/if}

    <tr valign="top"><td class="postblock">Password: </td><td><input type="password" name="password" size="8" accesskey="p" value="{$password}"></td></tr>
    <tr valign="top"><td class="postblock">Redirection: </td><td>(thread: <input type="radio" name="goto" value="t"{if $goto == 't'} checked{/if}>) (board: <input type="radio" name="goto" value="b"{if $goto == 'b'} checked{/if}>)</td></tr>
    <tr valign="top"><td colspan = "2" class="rules">
        <ul class="infolist">
            <li>File types: {section name=i loop=$upload_types} {$upload_types[i].extension}{/section}</li>
            <li>Board bumplimit: {$board.bump_limit}</li>
            <li><a href="{$DIR_PATH}/catalog.php?board={$board.name}">Catalog</a></li>
        </ul>
        {$board.annotation}
    </td></tr>
</tbody>
</table>
<input type="hidden" name="board" value="{$board.id}">
</form>
{if $board.with_attachments && $enable_shi}<form action="{$DIR_PATH}/lib/shi_applet.php" method="post">
    <input type="hidden" name="board" value="{$board.name}">
    Oekaki: <select name="painter">
        <option value="shi_normal" selected="selected">Shi Normal</option>
        <option value="shi_pro">Shi Pro</option>
    </select>
    Width: <input type="text" name="x" size="3" value="640" />
    Height: <input type="text" name="y" size="3" value="480" />
    <input type="submit" value="Draw" />
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
<hr>{$threads_html}
{if count($hidden_threads) > 0}
Hidden threads:
{section name=i loop=$hidden_threads}
<a href="{$DIR_PATH}/unhide_thread.php?thread={$hidden_threads[i].thread}" title="Refer to unhide thread.">{$hidden_threads[i].thread_number}</a>
{/section}
{/if}
{include file='pages_list.tpl' board_name=$board.name pages=$pages page=$page}
<br>
{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}
<br>
<div class="footer" style="clear: both;">- <a href="http://code.google.com/p/kotoba-ib/" target="_top">Kotoba 1.2</a> -</div>
{include file='footer.tpl'}