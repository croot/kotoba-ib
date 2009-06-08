<br>
{foreach from=$PAGES item=page}
({if $page.selected == 1}{$page.page}{else}<a href="{$KOTOBA_DIR_PATH}/{$BOARD_NAME}/p{$page.page}">{$page.page}</a>{/if})
{/foreach}
{include file='footer.tpl'}
