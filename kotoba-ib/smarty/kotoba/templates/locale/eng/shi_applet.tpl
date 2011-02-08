{* Smarty *}
{*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************}
{*
Code of shi applet.

Variables:
    $DIR_PATH - path from server document root to index.php directory (see config.default).
    $STYLESHEET - stylesheet (see config.default).
    $image_width - image width.
    $image_height - image height.
    $thumbnail_width - thumbnail wight.
    $thumbnail_height - thumbnail height.
    $tools - toolkit name (pro, normal).
    $ip - IP-address of artist.
    $time - time of begin drawing.
    $file - name of image file.
    $board - board name.
    $thread - thread name.
*}
<html>
<head>
    <title>Shi</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="{$DIR_PATH}/css/global.css">
    <link rel="stylesheet" type="text/css" href="{$DIR_PATH}/css/{$STYLESHEET}">
    <script type="text/javascript" src="{$DIR_PATH}/shi/sp.js"></script>
</head>
<body>
<applet code="c.ShiPainter.class" name="paintbbs" archive="{$DIR_PATH}/shi/spainter_all.jar,{$DIR_PATH}/shi/res/normal.zip" width="100%" height="100%">
    <param name="dir_resource" value="{$DIR_PATH}/shi/res/" />
    <param name="res.zip" value="{$DIR_PATH}/shi/res/res_{$tools}.zip" />
    <param name="tools" value="{$tools}" />
    <param name="tt.zip" value="{$DIR_PATH}/shi/res/tt.zip" />
    <param name="image_width" value="{$image_width}" />
    <param name="image_height" value="{$image_height}" />
    <param name="thumbnail_type" value="animation">
    <param name="thumbnail_type2" value="png">
    <param name="thumbnail_width" value="{$thumbnail_width}">
    <param name="thumbnail_height" value="{$thumbnail_height}">
    <param name="layer_count" value="3" />
    <param name="url_save" value="{$DIR_PATH}/lib/shi_save.php?file={$file}" />
    <param name="url_exit" value="{$DIR_PATH}/lib/shi_exit.php?file={$file}&time={$time}&painter=shi_{$tools}&board={$board}&thread={$thread}" />
    <param name="send_header" value="{$ip}" />
</applet>
{include file='footer.tpl'}