{include file='header.tpl' page_title=$page_title}
Этот файл уже имеется в следующих постах:
{foreach from=$links item=pagelinks}
{foreach from=$pagelinks item=link}
<a href="{$KOTOBA_DIR_PATH}/{$link.board_name}/{$link.thread_num}/#{$link.post_num}">&gt;&gt;&gt;/{$link.board_name}/{$link.thread_num}/#{$link.post_num}</a>
{/foreach}
{/foreach}
{include file='footer.tpl'}
