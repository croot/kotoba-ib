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
Описание переменных:
    $KOTOBA_DIR_PATH - должна быть объявлена в вызывающем шаблоне.
    $page_title - заголовок страницы. Передаётся из вызывающего шаблона явно.
*}
<html>
<head>
	<title>{$page_title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/kotoba.css">
</head>
<body>

