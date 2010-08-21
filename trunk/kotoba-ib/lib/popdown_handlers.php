<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Обработчики автоматического удаления и архивирования нитей.
 * @package api
 */

/**
 * Стандартный обработчик автоматического удаления нитей оставляет не
 * помеченными на архивирование последние (новые) нити заданной доски, суммарное
 * количество сообщений в которых не более чем 10 * специфический бамплимит
 * доски.
 * @param string|int $board_id Идентификатор доски.
 */
function popdown_default_handler($board_id) {
	$link = DataExchange::getDBLink();
	if (!mysqli_query($link, "call sp_threads_edit_archived_postlimit($board_id, 10)")) {
		throw new CommonException(mysqli_error($link));
    }

	db_cleanup_link($link);
}
?>