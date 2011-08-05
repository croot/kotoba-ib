{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of imageboard main page.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $ib_name - imageboard name.
*}
<html>
<head>
<title>{$ib_name}</title>
<!-- <link rel="shortcut icon" href="/favicon.ico"> -->
</head>
<frameset cols="15%,*" frameborder="0" border="0">
<frame src="{$DIR_PATH}/menu.php" name="menu" id="menu">
<frame src="{$DIR_PATH}/news.php" name="main" id="main">
<noframes>
Your browser doesn't support frames, which {$ib_name} requires.<br>
Please upgrade to something newer.
</noframes>
</frameset>
</html>