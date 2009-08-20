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
    $KOTOBA_DIR_PATH - путь от корня документов к директории, где хранится index.php (см. config.default).
    $page_title - заголовок страницы.
    $stylesheet - стиль оформления.
*}
<html>
<head>
	<title>{$page_title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="{$KOTOBA_DIR_PATH}/{$stylesheet}">
</head>
<body>

