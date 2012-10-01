<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Requires old config included before this script.
 */

require_once "lib/exceptions.php";
require_once "lib/errors.php";
require_once "lib/db.php";

mb_language(Config::MB_LANGUAGE);
mb_internal_encoding(Config::MB_ENCODING);
if (!setlocale(LC_ALL, Config::$LOCALE_NAMES)) {
    throw new SetLocaleException();
}

if (!isset($_SERVER['REMOTE_ADDR'])) {
    throw new RemoteAddressException();
}
if ( ($ban = bans_check(ip2long($_SERVER['REMOTE_ADDR']))) !== FALSE) {
    $page = new BanPage("Ban", $_SERVER['REMOTE_ADDR'], $ban['reason']);
    echo $page->render();

    DataExchange::releaseResources();
    exit(1);
}

?>
