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

require 'config.php';
require 'common.php';
require 'session_processing.php';
require 'events.php';

kotoba_setup();

header("Cache-Control: private");


/* preview_message - crop long message
 * TODO: limit only lines
 * return cropped (if need) message
 * arguments:
 * $message - message text
 * $preview_lines - how many lines to preview
 * $is_cutted - (pointer) notifies caller if message was cutted
 */
function preview_message(&$message, $preview_lines, &$is_cutted) {
	$lines = explode("<br>", $message);
	if(count($lines) > $preview_lines) {
		$is_cutted = 1;
		return implode("<br>", array_slice($lines, 0, $preview_lines));
	}
	else {
		$is_cutted = 0;
		return $message;
	}
}
/* get_threads_on_page: get threads on page
 * returns array of thread id
 * arguments:
 * $link - database link
 * $boardid - board id
 * $threadsqty - threads quantity on page
 * $page - page number
 */
/* XXX not used
function get_threads_on_page($link, $boardid, $threadsqty, $page) {
	echo "b $boardid, t $threadsqty, p $page<br>\n";
	$threads = array();
	$st = mysqli_prepare($link, "call sp_threads_on_page(?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "iii", $boardid, $threadsqty, $page)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $num);
	while(mysqli_stmt_fetch($st)) {
		array_push($threads, array($num));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $threads;
}
 */

/* get_user_threads_on_page: get threads on page for user (hidden or unhidden settings)
 * returns array:
 * num, hidden, reason
 * arguments:
 * $link - database link
 * $userid - user id
 * $boardid - board id
 * $threadsqty - threads quantity on page
 * $page - page number
 */
function get_user_threads_on_page($link, $userid, $boardid, $threadsqty, $page) {
	echo "u $userid, b $boardid, t $threadsqty, p $page<br>\n";
	$threads = array();
	$st = mysqli_prepare($link, "call sp_threads_on_page_for_user(?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "iiii", $userid, $boardid, $threadsqty, $page)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $num);
	while(mysqli_stmt_fetch($st)) {
		array_push($threads, $num);
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $threads;
}
function get_hidden_threads($link, $userid, $boardid) {
	$threads = array();
	$st = mysqli_prepare($link, "call sp_user_hidden_threads(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "ii", $userid, $boardid)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $num, $reason);
	while(mysqli_stmt_fetch($st)) {
		array_push($threads, array('thread' => $num, 'reason' => $reason));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $threads;
}
/* get_thread_preview: get thread posts and skipped posts and skipped uploads
 * returns array with fields:
 * post_number, skipped, uploads
 * arguments:
 * $link - database link
 * $boardid - board id
 * $open_post - thread open post number
 * $last - show N last posts
 */

function get_thread_preview($link, $boardid, $open_post, $last) {
	$posts = array();
	$st = mysqli_prepare($link, "call sp_thread_preview(?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "iii", $boardid, $open_post, $last)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $num, $skipped, $uploads);
	while(mysqli_stmt_fetch($st)) {
		array_push($posts, array('post_number' => $num, 'skipped' => $skipped,
			'uploads' => $uploads));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $posts;
}

// TODO: constant
if(KOTOBA_ENABLE_STAT)
    if(($stat_file = @fopen($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/preview.stat', 'a')) == false)
        kotoba_error("Ошибка. Неудалось открыть или создать файл статистики");


if(isset($_GET['b']))
{
    if(($BOARD_NAME = CheckFormat('board', $_GET['b'])) == false)
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

if(isset($_GET['p']))
{
	if(($PAGE = CheckFormat('page', $_GET['p'])) == false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_PAGE_BAD_FORMAT);
		kotoba_error(ERR_PAGE_BAD_FORMAT);
	}
}
else
{
	$PAGE = 1;
}

if(isset($_COOKIE['rempass']))
{
	if(($OPPOST_PASS = CheckFormat('pass', $_COOKIE['rempass'])) == false)
	{
		if(KOTOBA_ENABLE_STAT)
			kotoba_stat(ERR_PASS_BAD_FORMAT);
		kotoba_error(ERR_PASS_BAD_FORMAT);
	}
}
else
{
	$OPPOST_PASS = '';
}

require 'database_connect.php';
require 'database_common.php';
$link = dbconn();
// Получение списка досок и проверка существут ли доска с заданным именем.
$BOARD = db_get_board($link, $BOARD_NAME);

$BOARD_NUM = $BOARD['id'];

if($BOARD_NUM == -1)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_BOARD_NOT_FOUND, $BOARD_NAME));
	kotoba_error(sprintf(ERR_BOARD_NOT_FOUND, $BOARD_NAME));
}

$POST_COUNT = db_get_post_count($link, $BOARD_NUM);
$BUMP_LIMIT = $BOARD['bump_limit'];
if($BUMP_LIMIT == -1)
{
	if(KOTOBA_ENABLE_STAT)
		kotoba_stat(sprintf(ERR_BOARD_NOT_FOUND, $BOARD_NAME));
	kotoba_error(sprintf(ERR_BOARD_NOT_FOUND, $BOARD_NAME));
}


$db_page = $PAGE - 1;

// user logged in
if(isset($_SESSION['isLoggedIn']) and $_SESSION['isLoggedIn'] > 0) {
	$usersettings = sess_get_user_settings();
	$userid = $usersettings['userid'];
	$previewposts = $usersettings['preview_posts'];
	$previewlines = $usersettings['preview_lines'];
	$previewthreads = $usersettings['preview_threads'];
}
else {
	$userid = 0;
	$previewposts = KOTOBA_POSTS_PREVIEW;
	$previewlines = KOTOBA_LONGPOST_LINES;
	$previewthreads = KOTOBA_THREADS_ONPAGE;
}

$threads = get_user_threads_on_page($link, $userid, $BOARD_NUM, $previewthreads, $db_page);
$pages_count = db_get_pages($link, $userid, $BOARD_NUM, $previewthreads);

$pages = array();
for($page = 1; $page <= $pages_count; $page ++) {
	$settings = array('page' => sprintf("%02d", $page));
	if($PAGE == $page) {
		$settings["selected"] = 1;
	}
	array_push($pages, $settings);
}

$types = db_get_board_types($link, $BOARD_NUM);

$smarty = new SmartyKotobaSetup();
$smarty->assign('BOARD_NAME', $BOARD_NAME);
$smarty->assign('BOARD_TYPES', array_keys($types));
$smarty->assign('page_title', "Kotoba - $BOARD_NAME");
$boardNames = db_get_boards($link);
$smarty->assign('board_list', $boardNames);
$smarty->assign('POST_COUNT', $POST_COUNT);
$smarty->assign('BOARD_BUMPLIMIT', $BUMP_LIMIT);
$smarty->assign('KOTOBA_POST_LIMIT', KOTOBA_POST_LIMIT);
$smarty->display('board_preview.tpl');

$hidden = get_hidden_threads($link, $userid, $BOARD_NUM);

foreach($threads as $open_post) {
	$posts = get_thread_preview($link, $BOARD_NUM, $open_post, $previewposts);
	$count = 0;
	foreach($posts as $post) {
		$smarty_thread = new SmartyKotobaSetup();
		$smarty_thread->assign('BOARD_NAME', $BOARD_NAME);
		$smarty_thread->assign('reply', 1);
		$whole_post = db_get_post($link, $BOARD_NUM, $post['post_number']);
		$txt_post = $whole_post[0];
		$long = 0;
		$preview_lines = preview_message($txt_post['text'], $previewlines, $long);
		// post may contain more than one upoads!
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

		$smarty_thread->assign('original_text_cutted', $long);
		$smarty_thread->assign('original_text', $preview_lines);
		if($count > 0) {
			$smarty_thread->assign('original_thread', $open_post);
			$smarty_thread->display('post_thread.tpl');
		}
		else {
			$smarty_thread->assign('original_thread', $open_post);
			$smarty_thread->assign('skipped', $post['skipped']);
			$smarty_thread->assign('skipped_uploads', $post['uploads']);
			$smarty_thread->display('post_original.tpl');
			$smarty_thread->display('post_footer.tpl');
		}
		$count ++;
	}
	$smarty->display('preview_thread_footer.tpl');
}

$smarty->assign('hidden_threads', $hidden);
$smarty->assign('PAGES', $pages);
$smarty->display('board_preview_footer.tpl');


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
