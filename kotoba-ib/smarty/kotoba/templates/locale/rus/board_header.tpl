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
Код начала страницы просмотра доски.

Описание переменных:
    $DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $STYLESHEET - стиль оформления (см. config.default).
    $ib_name - название имейджборды  (см. config.default).
    $board - просматриваемая доска.
    $page - номер просматриваемой страницы.
    $show_control - показывать ссылку на страницу административных фукнций и фукнций модераторов в панели администратора.
    $boards - доски.
    $enable_macro - Включение интеграции с макрочаном (см. config.default).
    $enable_youtube - Включение постинга видео с ютуба (см. config.default).
    $enable_search - Включение поиска по тексту сообщений (см. config.default).
    $enable_captcha - Включение каптчи (см. config.default).
    $enable_translation - Включение перевода текста сообщения (см. config.default).
    $enable_geoip - Включение отображения страны автора сообщения (см. config.default).
    $enable_shi - Включение рисования (см. config.default).
    $is_admin - флаг администратора.
    $password - пароль для удаления сообщений.
    $upload_types - типы файлов, доступных для загрузки на просматриваемой доске.
    $pages - номера страниц.
    $goto - переход к нити или доске.
    $macrochan_tags - теги макросов.
    $name - имя
    $banner - баннер.
    $oekaki - данные о рисунке.

Специальные переменные (не входит в котобу):
    $event_daynight_active - запущен ли эвент времени суток.
    $event_daynight_code - код, добавляемый к html коду страницы, эвентом.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title="`$ib_name` — /`$board.name`/ `$board.title`. Просмотр, страница $page"}

{if $enable_translation}<script type="text/javascript" src="http://www.google.com/jsapi"></script>{/if}

<script src="{$DIR_PATH}/kotoba.js"></script>
<script src="{$DIR_PATH}/protoaculous-compressed.js"></script>

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
<input type="hidden" name="MAX_FILE_SIZE" value="1560576">
<table align="center" border="0">
<tbody>
{if !$board.force_anonymous}
<tr valign="top"><td class="postblock">Имя: </td><td><input type="text" name="name" size="30" value="{$name}"></td></tr>
{/if}
<tr valign="top"><td class="postblock">Тема: </td><td><input type="text" name="subject" size="48"> <input type="submit" value="Создать нить"></td></tr>
<tr valign="top"><td class="postblock">Сообщение: </td><td><textarea name="text" rows="7" cols="50"></textarea><img id="resizer" src="{$DIR_PATH}/flower.png"></td></tr>
{if $board.with_attachments}
	<tr valign="top"><td class="postblock">Файл: </td><td><input type="file" name="file" size="54"> Спойлер: <input type="checkbox" name="spoiler" value="1" /></td></tr>
	{if $oekaki}
        <tr valign="top"><td class="postblock">Мой рисунок: </td><td><a href="{$DIR_PATH}/shi/{$oekaki.file}"><img border="0" src="{$DIR_PATH}/shi/{$oekaki.thumbnail}" align="middle" /></a> Использовать вместо файла: <input type="checkbox" name="use_oekaki" value="1"></td></tr>
        {/if}
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
	{if $enable_youtube}<tr valign="top"><td class="postblock">Youtube: </td><td><input type="text" name="youtube_video_code" size="30"></td></tr>{/if}
{/if}
{if $enable_captcha}<tr valign="top"><td class="postblock">Капча: </td><td><a href="#" onclick="document.getElementById('captcha').src = '{$DIR_PATH}/captcha/image.php?' + Math.random(); return false"><img border="0" id="captcha" src="{$DIR_PATH}/captcha/image.php" alt="Kotoba capcha v0.4" align="middle" /></a> <input type="text" name="captcha_code" size="10" maxlength="6" /></tr>{/if}
<tr valign="top"><td class="postblock">Пароль: </td><td><input type="password" name="password" size="30" value="{$password}"></td></tr>
<tr valign="top"><td class="postblock">Перейти: </td><td>(нить: <input type="radio" name="goto" value="t"{if $goto == 't'} checked{/if}>) (доска: <input type="radio" name="goto" value="b"{if $goto == 'b'} checked{/if}>)</td></tr>

<tr valign="top"><td colspan = "2" class="rules">
<ul style="margin-left: 10pt; margin-top: 0pt; margin-bottom: 0pt; padding-left: 0pt;">
<li>Типы файлов, доступных для загрузки: {section name=i loop=$upload_types} {$upload_types[i].extension}{/section}</li>
<li>Бамплимит доски: {$board.bump_limit}</li>
<li><a href="{$DIR_PATH}/catalog.php?board={$board.name}">Каталог нитей</a></li>
</ul>
{$board.annotation}
</td></tr>
</tbody>
</table>
<input type="hidden" name="board" value="{$board.id}">
</form>
{if $board.with_attachments && $enable_shi}
<form action="{$DIR_PATH}/lib/shi_applet.php" method="post">
    <input type="hidden" name="board" value="{$board.name}">
    Наоекакать: <select name="painter">
        <option value="shi_normal" selected="selected">Shi Normal</option>
        <option value="shi_pro">Shi Pro</option>
    </select>
    Ширина: <input type="text" name="x" size="3" value="640" />
    Высота: <input type="text" name="y" size="3" value="480" />
    <input type="submit" value="Рисовать" />
</form>
{/if}

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
<hr>
