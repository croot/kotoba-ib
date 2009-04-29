<html>
<head>
	<title>Kotoba Register</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/kotoba.css">
</head>
<body>
{if $form == 1}
<form action="register.php" method="post">
	<p>Keyword: 
	<input name="Keyword" type="text" size="80" maxlength="32"> 
	<input type="submit" value="Register">
	</p>
	</form>
<center>
{/if}
<p>{$message}</p>
<a href="{$KOTOBA_DIR_PATH}/login.php">Войти</a> |
<a href="{$KOTOBA_DIR_PATH}/index.php">На главную</a>
</center>
</body>
</html>
