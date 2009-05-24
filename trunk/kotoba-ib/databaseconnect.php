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

require_once 'events.php';
require_once 'config.php';
require_once 'error_processing.php';
if(@mysql_connect(KOTOBA_DB_HOST, KOTOBA_DB_USER, KOTOBA_DB_PASS) == false)
{

	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_DB_CONNECT, mysql_error()));
	kotoba_error(sprintf(ERR_DB_CONNECT, mysql_error()));
}

if(mysql_select_db(KOTOBA_DB_BASENAME) == false)
{

	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_DB_SELECT, mysql_error()));

	kotoba_error(sprintf(ERR_DB_SELECT, mysql_error()));

}
?>
