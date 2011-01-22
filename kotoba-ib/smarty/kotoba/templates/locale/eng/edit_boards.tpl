{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of edit boards page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $show_control - show link to manage page.
    $boards - Boards.
    $popdown_handlers - Popdown handlers.
    $categories - Categories.
*}
{include file='header.tpl' DIR_PATH=$DIR_PATH STYLESHEET=$STYLESHEET page_title='Edit boards'}

{include file='adminbar.tpl' DIR_PATH=$DIR_PATH show_control=$show_control}

{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

<br>
<form action="{$DIR_PATH}/admin/edit_boards.php" method="post">
<table border="1">
<tr>
    <td colspan="18">To add new board enter all requed parameters (marked as<span style="color:red;">*</span>).<br>
    To edit paramaters of some board edit it in the table.<br>
    To delete board mark it.</td>
</tr>
<tr>
    <td>Name<span style="color:red;">*</span></td>
    <td>Title</td>
    <td>Annotation</td>
    <td>Bump limit<span style="color:red;">*</span></td>
    <td>Hide name flag</td>
    <td>Default name</td>
    <td>Attachments flag</td>
    <td>Macrochan integration flag</td>
    <td>Youtube video posting flag</td>
    <td>Captcha flag</td>
    <td>Translation flag</td>
    <td>GeoIP flag</td>
    <td>Painting flag</td>
    <td>Post identification flag</td>
    <td>Upload policy to same files<span style="color:red;">*</span></td>
    <td>Popdown handler<span style="color:red;">*</span></td>
    <td>Category<span style="color:red;">*</span></td>
    <td>Delete board</td>
</tr>
{section name=i loop=$boards}
<tr>
    <td>{$boards[i].name}</td>
    <td><input type="text" name="title_{$boards[i].id}" value="{$boards[i].title}"></td>
    <td><textarea name="annotation_{$boards[i].id}" rows="4" cols="50">{if $boards[i].annotation !== null}{$boards[i].annotation}{/if}</textarea></td>
    <td><input type="text" name="bump_limit_{$boards[i].id}" value="{$boards[i].bump_limit}"></td>
    <td><input type="checkbox" name="force_anonymous_{$boards[i].id}" value="1"{if $boards[i].force_anonymous} checked{/if}></td>
    <td><input type="text" name="default_name_{$boards[i].id}" value="{$boards[i].default_name}"></td>
    <td><input type="checkbox" name="with_attachments_{$boards[i].id}" value="1"{if $boards[i].with_attachments} checked{/if}></td>
    <td><select name="enable_macro_{$boards[i].id}">
        <option value="0"{if $boards[i].enable_macro == '0'} selected{/if}>down</option>
        <option value="1"{if $boards[i].enable_macro == '1'} selected{/if}>up</option>
        <option value="2"{if $boards[i].enable_macro === null} selected{/if}>inherit</option></select>
    </td>
    <td><select name="enable_youtube_{$boards[i].id}">
        <option value="0"{if $boards[i].enable_youtube == '0'} selected{/if}>down</option>
        <option value="1"{if $boards[i].enable_youtube == '1'} selected{/if}>up</option>
        <option value="2"{if $boards[i].enable_youtube === null} selected{/if}>inherit</option></select>
    </td>
    <td><select name="enable_captcha_{$boards[i].id}">
        <option value="0"{if $boards[i].enable_captcha == '0'} selected{/if}>down</option>
        <option value="1"{if $boards[i].enable_captcha == '1'} selected{/if}>up</option>
        <option value="2"{if $boards[i].enable_captcha === null} selected{/if}>inherit</option></select>
    </td>
    <td><select name="enable_translation_{$boards[i].id}">
        <option value="0"{if $boards[i].enable_translation == '0'} selected{/if}>down</option>
        <option value="1"{if $boards[i].enable_translation == '1'} selected{/if}>up</option>
        <option value="2"{if $boards[i].enable_translation === null} selected{/if}>inherit</option></select>
    </td>
    <td><select name="enable_geoip_{$boards[i].id}">
        <option value="0"{if $boards[i].enable_geoip == '0'} selected{/if}>down</option>
        <option value="1"{if $boards[i].enable_geoip == '1'} selected{/if}>up</option>
        <option value="2"{if $boards[i].enable_geoip === null} selected{/if}>inherit</option></select>
    </td>
    <td><select name="enable_shi_{$boards[i].id}">
        <option value="0"{if $boards[i].enable_shi == '0'} selected{/if}>down</option>
        <option value="1"{if $boards[i].enable_shi == '1'} selected{/if}>up</option>
        <option value="2"{if $boards[i].enable_shi === null} selected{/if}>inherit</option></select>
    </td>
    <td><select name="enable_postid_{$boards[i].id}">
        <option value="2"{if $boards[i].enable_postid === null} selected{/if}>inherit</option>
        <option value="1"{if $boards[i].enable_postid == '1'} selected{/if}>up</option>
        <option value="0"{if $boards[i].enable_postid == '0'} selected{/if}>down</option></select>
    </td>
    <td><input type="text" name="same_upload_{$boards[i].id}" value="{$boards[i].same_upload}"></td>
    <td>
    <select name="popdown_handler_{$boards[i].id}">
    {section name=j loop=$popdown_handlers}
    <option value="{$popdown_handlers[j].id}"{if $popdown_handlers[j].id == $boards[i].popdown_handler} selected{/if}>{$popdown_handlers[j].name}</option>
    {/section}
    </select>
    </td>
    <td>
    <select name="category_{$boards[i].id}">
    {section name=k loop=$categories}
    <option value="{$categories[k].id}"{if $categories[k].id == $boards[i].category} selected{/if}>{$categories[k].name}</option>
    {/section}
    </select>
    </td>
    <td><input type="checkbox" name="delete_{$boards[i].id}" value="1"></td>
</tr>
{/section}
<tr>
<td><input type="text" name="new_name" value=""></td>
<td><input type="text" name="new_title" value=""></td>
<td><textarea name="new_annotation" rows="4" cols="50"></textarea></td>
<td><input type="text" name="new_bump_limit" value=""></td>
<td><input type="checkbox" name="new_force_anonymous" value="1"></td>
<td><input type="text" name="new_default_name" value=""></td>
<td><input type="checkbox" name="new_with_attachments" value="1"></td>
<td><select name="new_enable_macro">
    <option value="0">down</option>
    <option value="1">up</option>
    <option value="2" selected>inherit</option></select>
</td>
<td><select name="new_enable_youtube">
    <option value="0">down</option>
    <option value="1">up</option>
    <option value="2" selected>inherit</option></select>
</td>
<td><select name="new_enable_captcha">
    <option value="0">down</option>
    <option value="1">up</option>
    <option value="2" selected>inherit</option></select>
</td>
<td><select name="new_enable_translation">
    <option value="0">down</option>
    <option value="1">up</option>
    <option value="2" selected>inherit</option></select>
</td>
<td><select name="new_enable_geoip">
    <option value="0">down</option>
    <option value="1">up</option>
    <option value="2" selected>inherit</option></select>
</td>
<td><select name="new_enable_shi">
    <option value="0">down</option>
    <option value="1">up</option>
    <option value="2" selected>inherit</option></select>
</td>
<td><select name="new_enable_postid">
    <option value="0">down</option>
    <option value="1">up</option>
    <option value="2" selected>inherit</option></select>
</td>
<td><input type="text" name="new_same_upload" value=""></td>
<td><select name="new_popdown_handler">
    <option value="" selected></option>{section name=m loop=$popdown_handlers}

    <option value="{$popdown_handlers[m].id}">{$popdown_handlers[m].name}</option>{/section}</select>
</td>
<td colspan="2"><select name="new_category">
    <option value="" selected></option>{section name=n loop=$categories}

    <option value="{$categories[n].id}">{$categories[n].name}</option>{/section}</select>
</td>
</tr>
</table><br>
<input type="hidden" name="submited" value="1">
<input type="reset" value="Reset"> <input type="submit" value="Save">
</form>
<br>
<br>
{include file='navbar.tpl' DIR_PATH=$DIR_PATH boards=$boards}

{include file='footer.tpl'}