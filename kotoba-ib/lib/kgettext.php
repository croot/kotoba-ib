<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Exceptions.
 * @package api
 */

/**
 *
 */
require_once '../config.php';

function kgettext($text) {
    global $KOTOBA_LOCALE_MESSAGES;

    $l = isset($_SESSION['language'])
         ? $_SESSION['language']
         : Config::LANGUAGE;

    // Let warning appeas if no such text or language in locale messages.

    return $KOTOBA_LOCALE_MESSAGES[$text][$l];
}
?>
