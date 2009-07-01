<span class="omittedposts">Скрытые нити:
{foreach from=$hidden_threads item=thread}
<a title="{$thread.reason}" href="{$KOTOBA_DIR_PATH}/{$BOARD_NAME}/{$thread.thread}">&gt;&gt;{$thread.thread}</a><a href="{$KOTOBA_DIR_PATH}/un-hide.php?action=unhide&b={$BOARD_NAME}&t={$thread.thread}">[+]</a>&nbsp;
{/foreach}</span>
<br>
{foreach from=$PAGES item=page}
({if $page.selected == 1}{$page.page}{else}<a href="{$KOTOBA_DIR_PATH}/{$BOARD_NAME}/p{$page.page}">{$page.page}</a>{/if})
{/foreach}
{include file='footer.tpl'}
