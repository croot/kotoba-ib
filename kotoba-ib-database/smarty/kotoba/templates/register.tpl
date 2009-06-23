{include file='header.tpl' page_title='Kotoba Registration'}
<body>
{if $form == 1}
<form action="register.php" method="post">
<table>{if $keyword == 1}
<tr>
	<td>Ключевое слово:</td>
	<td colspan="3"><input name="keyword" type="text" size="80" maxlength="32">
	</td>
</tr>{/if}
<tr>
<td>Последних сообщений в нити</td>
<td><input name="posts" type="text" value="{$posts}"></td>
<td>Первых линий в длинном сообщении</td>
<td><input name="lines" type="text" value="{$lines}"></td>
</tr>
<tr>
<td>Нитей на странице</td>
<td><input name="threads" type="text" value="{$threads}"></td>
<td>Страниц</td>
<td><input name="pages" type="text" value="{$pages}"></td>
</tr>
	<input type="submit" value="Register">
</table>
</form>
{/if}
<center>
<p>{$message}</p>
<a href="{$KOTOBA_DIR_PATH}/login.php">Войти</a> |
<a href="{$KOTOBA_DIR_PATH}/index.php">На главную</a>
</center>
{include file='footer.tpl'}
