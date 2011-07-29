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
require_once dirname(dirname(__FILE__)) . '/config.php';

// Messages in default language.
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/messages.php';

function kgettext($text) {
    global $KOTOBA_LOCALE_MESSAGES;

    $l = isset($_SESSION['language'])
         ? $_SESSION['language']
         : Config::LANGUAGE;

    // Let warning appeas if no such text or language in locale messages.

    return $KOTOBA_LOCALE_MESSAGES[$text][$l];
}
?>
