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

/* database connect module with mysqli interface */

require_once('config.php');
require_once('error_processing.php');


// connect to database
function dbconn() {
	$link = @mysqli_connect(KOTOBA_DB_HOST, KOTOBA_DB_USER, KOTOBA_DB_PASS, KOTOBA_DB_BASENAME);

	if(!$link) {
		kotoba_error(mysqli_connect_error());
	}
	if(!mysqli_set_charset($link, 'utf8')) {
		kotoba_error(mysqli_error($link));
	}
	return $link;
}
// cleanup all results on link. useful when stored procedure.
function cleanup_link($link) {
	do {
		$result = mysqli_use_result($link);
		if($result)
			mysqli_free_result($result);

	} while(mysqli_next_result($link));
}

?>
