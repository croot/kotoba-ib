{* Smarty *}
{*
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/
*}
<html>
<head>
	<title>Kotoba Main</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/kotoba.css">
</head>
<body>
{if isset($BOARDS_EXIST)}
Список досок: {section name=name loop=$boardNames}/<a href="{$KOTOBA_DIR_PATH}/{$boardNames[name]}/">{$boardNames[name]}</a>/ {/section}
{else}
<span class="error">Ошибка. Не создано ни одной доски.</span>
{/if}

<p>
{if isset($isLoggedIn)}
	<a href="{$KOTOBA_DIR_PATH}/logout.php">Выйти</a><br>
	<a href="{$KOTOBA_DIR_PATH}/addboard.php">Добавить доску</a><br>
	<a href="{$KOTOBA_DIR_PATH}/remboard.php">Удалить доску</a>
{else}
	<a href="{$KOTOBA_DIR_PATH}/login.php">Войти</a>
{/if}
</p>
</body>
</html>