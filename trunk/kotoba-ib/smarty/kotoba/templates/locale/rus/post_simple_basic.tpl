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
Код основы обычного сообщения.
*}
<table border="1">
    <tbody>
        <tr>
            {block name=doubledash}{/block}
            <td class="reply">
                {block name=anchor}{/block}
                {block name=remove_post}{/block}
                {block name=extrabtns}{/block}
                {block name=geoip}{/block}
                {block name=subject}{/block}
                {block name=postername}{/block}
                {block name=author_admin}{/block}
                {block name=date_time}{/block}
                {block name=reflink}{/block}
                {block name=postid}{/block}
                {block name=mod_mini_panel}{/block}
                <br/>
                {block name=attachment}{/block}
                {block name=text}{/block}
                {block name=translation}{/block}
            </td>
        </tr>
    </tbody>
</table>
