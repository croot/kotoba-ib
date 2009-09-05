<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

if(!class_exists('Errmsgs')) {
	class Errmsgs { static $messages; }
}

/*********
 * Other *
 *********/

Errmsgs::$messages['UNKNOWN'] = 'Unknown error.';
Errmsgs::$messages['SETLOCALE'] = 'Set locale failed.';
Errmsgs::$messages['SESSION_START'] = 'Can&#39;t start session.';
?>