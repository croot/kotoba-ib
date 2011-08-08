<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Share imageboard data for overchan.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

try {
    // Get data about boards and categories.
    $boards = boards_get_all();
    $categories = categories_get_all();

    // Create code and display.
    $out = '[';
    foreach ($categories as $category) {
        foreach ($boards as $board) {
            if ($category['id'] == $board['category']) {
                $out .= "<a href=\"/{$board['name']}/\">"
                        . "{$board['name']}</a> /\n";
            }
        }
        $out = mb_substr(
            $out,
            0,
            mb_strlen($out, Config::MB_ENCODING) - 3, Config::MB_ENCODING
        );
        $out .= " |\n";
    }
    $out = mb_substr(
        $out,
        0,
        mb_strlen($out, Config::MB_ENCODING) - 3, Config::MB_ENCODING
    );
    $out .= ']';
    echo $out;

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>