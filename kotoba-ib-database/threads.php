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

/* show thread */

require 'config.php';
require 'common.php';
require 'events.php';

kotoba_setup();
// firefox caching fix
header("Cache-Control: private");

/* get_thread - get thread posts
 * return array of post numbers
 * arguments:
 * $link - database link
 * $boardid - board id
 * $open_post_num - thread open post number
 */
function get_thread($link, $boardid, $open_post_num) {
	$posts = array();
	$st = mysqli_prepare($link, "call sp_get_thread(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "ii", $boardid, $open_post_num)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $num);
	while(mysqli_stmt_fetch($st)) {
		array_push($posts, $num);
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $posts;
}

if(KOTOBA_ENABLE_STAT)
	if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/preview.stat', 'a')) == false)
		kotoba_error(ERR_STATFILE);

// get boardname
if(isset($_GET['b']))
{
    if(($BOARD_NAME = CheckFormat('board', strval($_GET['b']))) == false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_BOARD_BAD_FORMAT);
		kotoba_error(ERR_BOARD_BAD_FORMAT);
	}
}
else
{
	if(KOTOBA_ENABLE_STAT)
        kotoba_stat(ERR_BOARD_NOT_SPECIFED);
	kotoba_error(ERR_BOARD_NOT_SPECIFED);
}

// get thread open post number
if(isset($_GET['t']))
{
    if(($THREAD_NUM = CheckFormat('thread', intval($_GET['t']))) === false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_THREAD_BAD_FORMAT);

		kotoba_stat(ERR_THREAD_BAD_FORMAT);
	}
}
else
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(ERR_THREAD_NOT_SPECIFED);
	kotoba_stat(ERR_THREAD_NOT_SPECIFED);
}

// Rus: Проверка пароля удаления сообщений.
if(isset($_COOKIE['rempass']))
{
	if(($REPLY_PASS = CheckFormat('pass', strval($_COOKIE['rempass']))) === false)
	{
        if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_PASS_BAD_FORMAT);

		kotoba_error(ERR_PASS_BAD_FORMAT);
	}
}
else
{
    $REPLY_PASS = '';
}

require 'database_connect.php';
require 'database_common.php';
$link = dbconn();
/*
 * user settings will implemented later
 *
if(isset($_SESSION['isLoggedIn']))	// Зарегистрированный пользователь.
{
	if(($result = mysql_query('select `id`, `User Settings` from `users` where SID = \'' . session_id() . '\'')) !== false)
	{
		if(mysql_num_rows($result) > 0)
		{
			$user = mysql_fetch_array($result, MYSQL_ASSOC);
			$User_id = $user['id'];
			$User_Settings = get_settings('user', $user['User Settings']);
			mysql_free_result($result);
		}
	}
	else
	{
		if(KOTOBA_ENABLE_STAT)
				kotoba_stat(sprintf(ERR_USER_DATA, mysql_error()));

		die($HEAD . '<span class="error">Ошибка. Невозможно получить данные пользователя. Причина: ' . mysql_error() . '.</span>' . $FOOTER);
	}
}
 */
// Получение списка досок и проверка существут ли доска с заданным именем.

$BOARD = db_get_board($link, $BOARD_NAME);

$BOARD_NUM = $BOARD['id'];

if($BOARD_NUM == -1)
{ // board not found
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_BOARD_NOT_FOUND, $BOARD_NAME));
	kotoba_error(sprintf(ERR_BOARD_NOT_FOUND, $BOARD_NAME));
}

$types = db_get_board_types($link, $BOARD_NUM);
$THREAD = db_get_thread($link, $BOARD_NUM, $THREAD_NUM);
if(count($THREAD) == 0) { // thread not found
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_THREAD_NOT_FOUND, $THREAD_NUM, $BOARD_NAME));
	kotoba_error(sprintf(ERR_THREAD_NOT_FOUND, $THREAD_NUM, $BOARD_NAME));
}
// var_dump($THREAD);

$POST_COUNT = db_get_post_count($link, $BOARD_NUM);

$smarty = new SmartyKotobaSetup();
$smarty->assign('BOARD_NAME', $BOARD_NAME);
$smarty->assign('BOARD_TYPES', array_keys($types));
$smarty->assign('page_title', "Kotoba - $BOARD_NAME/$THREAD_NUM");
$smarty->assign('THREAD_NUM', $THREAD_NUM);
$boardNames = db_get_boards($link);
$smarty->assign('board_list', $boardNames);
$smarty->assign('POST_COUNT', $THREAD['messages']);
$smarty->assign('THREAD_BUMPLIMIT', $THREAD['bump_limit']);
$smarty->assign('KOTOBA_POST_LIMIT', KOTOBA_POST_LIMIT);
$smarty->display('threads.tpl');

$posts = get_thread($link, $BOARD_NUM, $THREAD_NUM);
// var_dump($posts);
$count = 0;
foreach($posts as $post) {
	$smarty_thread = new SmartyKotobaSetup();
	$smarty_thread->assign('BOARD_NAME', $BOARD_NAME);
	$smarty_thread->assign('reply', 0);
	$whole_post = db_get_post($link, $BOARD_NUM, $post);
	$txt_post = $whole_post[0];
	// post may contain more than one uploads!
	if(count($whole_post[1]) > 0) {
		$upload = $whole_post[1][0];
		$smarty_thread->assign('with_image', 1);
		$smarty_thread->assign('original_file_name', $upload['file']);
		$smarty_thread->assign('original_file_link', $upload['file_name']);
		$smarty_thread->assign('original_file_size', $upload['size']);
		$smarty_thread->assign('original_file_heigth', $upload['file_h']);
		$smarty_thread->assign('original_file_width', $upload['file_w']);
		$smarty_thread->assign('original_file_thumbnail_link', $upload['thumbnail']);
		$smarty_thread->assign('original_file_thumbnail_heigth', $upload['thumbnail_h']);
		$smarty_thread->assign('original_file_thumbnail_width', $upload['thumbnail_w']);

	}
	$smarty_thread->assign('original_theme', $txt_post['subject']);
	$smarty_thread->assign('original_name', $txt_post['name']);
	if(strlen($txt_post['tripcode']) > 0) {
		$smarty_thread->assign('original_hascode', 1);
		$smarty_thread->assign('original_tripcode', $txt_post['tripcode']);
	}
	$smarty_thread->assign('original_time', $txt_post['date_time']);
	$smarty_thread->assign('original_id', $txt_post['post_number']);
	$smarty_thread->assign('original_text', $txt_post['text']);
	if($count > 0) {
		$smarty_thread->assign('original_thread', $THREAD_NUM);
		$smarty_thread->display('post_thread.tpl');
	}
	else {
		$smarty_thread->display('post_original.tpl');
		$smarty_thread->display('post_footer.tpl');
	}
	$count ++;
}

$smarty->display('thread_footer.tpl');

?>
<?php
/*
 * Выводит сообщение $errmsg в файл статистики $stat_file.
 */
function kotoba_stat($errmsg)
{
    global $stat_file;
    fwrite($stat_file, "$errmsg (" . date("Y-m-d H:i:s") . ")\n");
	fclose($stat_file);
}

// vim: set encoding=utf-8:
?>
