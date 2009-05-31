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
<html>
<head>
	<title>Kotoba Service</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/kotoba.css">
</head>
{if isset($error)}
<span class="error">{$error}.</span>
{else}
Обработано {$postsCount} постов. Исправлено {$affectedCount} постов.
{/if}
</body>
</html>