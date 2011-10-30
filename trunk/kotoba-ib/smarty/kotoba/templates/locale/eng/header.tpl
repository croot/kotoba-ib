{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Header of all Kotoba pages.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $page_title - page title.
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>{$page_title}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="{$DIR_PATH}/css/global.css">
    <link rel="stylesheet" type="text/css" href="{$DIR_PATH}/css/{$STYLESHEET}/{$STYLESHEET}">
    <link rel="icon" type="image/png" href="{$DIR_PATH}/azu/favicon.png" />
</head>
<body>