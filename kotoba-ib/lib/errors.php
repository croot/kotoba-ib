<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Exception extensions.
 * @package api
 */

/**
 * Ensure what requirements to use functions and classes from this script are met.
 */
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'exceptions.php'; })) {
    throw new Exception('Configuration file <b>exceptions.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}

// Load default error messages.
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/exceptions.php';


?>