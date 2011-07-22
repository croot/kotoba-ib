<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Messages in english.
 * @package api
 */

/**
 * 
 */
require_once '../config.php';

if (!isset($KOTOBA_LOCALE_MESSAGES)) {
    $KOTOBA_LOCALE_MESSAGES = array();
}
$_ = &$KOTOBA_LOCALE_MESSAGES;

$_['Cannot convert image to PNG format.']['eng'] = 'Cannot convert image to PNG format.';
$_['Copy file.']['eng'] = 'Copy file.';
$_['Failed to copy file %s to %s.']['eng'] = 'Failed to copy file %s to %s.';
$_['Failed to open or create log file %s.']['eng'] = 'Failed to open or create log file %s.';
$_['GD doesn\'t support %s file type.']['eng'] = 'GD doesn\'t support %s file type.';
$_['GD library.']['eng'] = 'GD library.';
$_['Groups.']['eng'] = 'Groups.';
$_['Id of new group was not received.']['eng'] = 'Id of new group was not received.';
$_['Image convertion.']['eng'] = 'Image convertion.';
$_['Imagemagic doesn\'t support %s file type.']['eng'] = 'Imagemagic doesn\'t support %s file type.';
$_['Imagemagic library.']['eng'] = 'Imagemagic library.';
$_['Logging.']['eng'] = 'Logging.';

unset($_);
?>
