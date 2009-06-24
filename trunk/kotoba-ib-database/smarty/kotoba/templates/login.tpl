<html>
<head>
	<title>Kotoba Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/kotoba.css">
</head>
<body>
{if $form == 1}
<form action="{$KOTOBA_DIR_PATH}/login.php" method="post">
<p align="center">
<table width="80%" align="center">
	<div class="pass">
		<center>
			<img src="{$KOTOBA_DIR_PATH}/img/logo.png"><br>
			Keyword: <input name="Keyword" type="text" maxlength="32">
			<input type="submit" value="Login">
			<br><br><br><br><br><br><br><br><br><br>
			<small>- Kotoba 0.00.α -</small>
		</center>
	</div>
</table>
</form>
{/if}
<center>
<p>{$message}</p>
<a href="{$KOTOBA_DIR_PATH}/logout.php">Выйти</a> |
<a href="{$KOTOBA_DIR_PATH}/index.php">На главную</a>
</center>
</body>
</html>
