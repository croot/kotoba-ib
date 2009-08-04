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
    $KOTOBA_DIR_PATH - см. объявление одноимённой константы в config.default.
    $stylesheet - стиль оформления.
*}
{include file='header.tpl' page_title='Мои настройки' stylesheet=$stylesheet}
<form action="{$KOTOBA_DIR_PATH}/edit_settings.php" method="post">
<i>Введите ключевое слово, чтобы загрузить ваши настройки.</i><br>
    <input type="text" name="keyword_load" size="32">
    <input type="submit" value="Загрузить">
</form>
<br>
<form action="{$KOTOBA_DIR_PATH}/edit_settings.php" method="post">
<b>Опции предпросмотра доски:</b>
    <table border="0">
        <tr valign="top"><td>Количество нитей на странице: </td><td><input type="text" name="threads_per_page" size="10" value="{$threads_per_page}"></td></tr>
        <tr valign="top"><td>Количество сообщений в нити: </td><td><input type="text" name="posts_per_thread" size="10" value="{$posts_per_thread}"></td></tr>
        <tr valign="top"><td>Количество строк в сообщении: </td><td><input type="text" name="lines_per_post" size="10" value="{$lines_per_post}"></td></tr>
    </table>
<b>Другое:</b>
    <table border="0">
        <tr valign="top"><td>Язык: </td><td><select name="language">{section name=j loop=$languages}<option value="{$languages[j]}"{if $language == $languages[j]} selected{/if}>{$languages[j]}</option>{/section}</select></td></tr>
        <tr valign="top"><td>Стиль оформления: </td><td><select name="stylesheet">{section name=i loop=$stylesheets}<option value="{$stylesheets[i]}"{if $stylesheet == $stylesheets[i]} selected{/if}>{$stylesheets[i]}</option>{/section}</select></td></tr>
    </table>
<i>Введите ключевое слово, чтобы сохранить эти настройки.<br>
В дальнейшем вы можете загрузить их, введя ключевое слово.</i><br>
    <input type="text" name="keyword_save" size="32">
    <input type="submit" value="Сохранить">
</form>
{include file='footer.tpl'}