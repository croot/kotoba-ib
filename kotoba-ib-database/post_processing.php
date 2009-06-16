<?php
/* postprocessing.php: module for post processing */

error_reporting(E_ALL);

/* post_get_board_id: get board id
 * return values: positive integer board id
 * on error return -1
 * arguments:
 * $board_name is board name (UNSAFE!)
 * $kotoba_stat is kotoba stat function name
 * $error_message is reference to variable which would contain error message if any
 */
function post_get_board_id($board_name, $kotoba_stat, &$error_message) {
	$BOARD_NUM = -1;
	// create sql query
	$sql = sprintf("select id from boards where Name = '%s'", $board_name);
	if(($result = mysql_query($sql)) !== false)
	{ // query ok
		if(mysql_num_rows($result) == 0)
		{ // query result contains nothing
			if(KOTOBA_ENABLE_STAT)
				call_user_func_array($kotoba_stat, array(sprintf(ERR_BOARD_NOT_FOUND, $board_name)));

			mysql_free_result($result);
			$error_message = "Ошибка. Доски с именем $board_name не существует";
			return -1;
		}
		else
		{ // query get an id
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$BOARD_NUM = $row['id'];
			mysql_free_result($result);
			return $BOARD_NUM;
		}
	}
	else
	{ // no boards found
		if(KOTOBA_ENABLE_STAT)
			call_user_func_array($kotoba_stat, array(sprintf(ERR_BOARD_EXIST_CHECK, $board_name, mysql_error())));
			
		$error_message = "Ошибка. Не удалось проверить существание доски с именем $board_name. Прична: " .  mysql_error();
		return -1;
	}
}

/*
 * post_check_image_upload_error: check image upload errors
 * return true if no errors
 * false on error
 * arguments:
 * $error is error code from $_FILES[...]['error']
 * $allow_no_uploads is for not to detect NO_FILE error
 * $kotoba_stat is kotoba_stat function name
 * $error_message is reference to variable which would contain error message if any
 */
function post_check_image_upload_error($error, $allow_no_uploads = false, $kotoba_stat, &$error_message) {
	switch($error)
	{
		case UPLOAD_ERR_INI_SIZE:
			if(KOTOBA_ENABLE_STAT)
				call_user_func_array($kotoba_stat, array(ERR_UPLOAD_INI_SIZE));

			$error_message = "Ошибка. Загруженный файл превышает размер, заданный директивой upload_max_filesize в php.ini";
			return false;
		break;

		case UPLOAD_ERR_FORM_SIZE:
			if(KOTOBA_ENABLE_STAT)
				call_user_func_array($kotoba_stat, array(ERR_UPLOAD_FORM_SIZE));

			$error_message = "Ошибка. Загруженный файл превышает размер, заданный директивой MAX_FILE_SIZE, определённой в HTML форме.";
			return false;
		break;
		
		case UPLOAD_ERR_PARTIAL:
			if(KOTOBA_ENABLE_STAT)
				call_user_func_array($kotoba_stat, array(ERR_UPLOAD_PARTIAL));

			$error_message = "Ошибка. Файл был загружен лишь частично.";
			return false;
		break;
		
		case UPLOAD_ERR_NO_FILE:
			if(KOTOBA_ENABLE_STAT)
				if(!$allow_no_uploads)
					call_user_func_array($kotoba_stat, array(ERR_UPLOAD_NO_FILE));

			$error_message = "Ошибка. Файл не был загружен.";
			return $allow_no_uploads;
		break;
		
		case UPLOAD_ERR_NO_TMP_DIR:
			if(KOTOBA_ENABLE_STAT)
				call_user_func_array($kotoba_stat, array(ERR_UPLOAD_NO_TMP_DIR));

			$error_message = "Ошибка. Временная папка не найдена.";
			return false;
		break;
		
		case UPLOAD_ERR_CANT_WRITE:
			if(KOTOBA_ENABLE_STAT)
				call_user_func_array($kotoba_stat, array(ERR_UPLOAD_CANT_WRITE));

			$error_message = "Ошибка. Не удалось записать файл на диск.";
			return false;
		break;
		
		case UPLOAD_ERR_EXTENSION:
			if(KOTOBA_ENABLE_STAT)
				call_user_func_array($kotoba_stat, array(ERR_UPLOAD_EXTENSION));

			$error_message = "Ошибка. Загрузка файла прервана расширением.";
			return false;
		break;
	}

	return true;
}

/*
 * post_check_sizes is check sizes of uploaded data
 * return true if no errors
 * false on error
 * arguments:
 * $uplodedFileSize is size of uploaded image
 * $with_image boolean is was image loaded?
 * &$message_text is message text field
 * &$message_theme is message theme field
 * &$message_name is message name field
 * $kotoba_stat is kotoba_stat function name
 * $error_message is reference to variable which would contain error message if any
 */
function post_check_sizes($uplodedFileSize, $with_image, &$message_text, &$message_theme, 
	&$message_name, $kotoba_stat, &$error_message, $upload = true) {

	if($upload && $uplodedFileSize < KOTOBA_MIN_IMGSIZE && $with_image)
	{
		if(KOTOBA_ENABLE_STAT)
			call_user_func_array($kotoba_stat, array(ERR_FILE_TOO_SMALL));
			
		$error_message = "Ошибка. Загружаемый файл имеет слишком маленький размер.";
		return false;
	}

	if(!post_check_message_size($message_text, $kotoba_stat, $error_message)) {
		return false;
	}
	if(strlen($message_theme) > KOTOBA_MAX_THEME_LENGTH)
	{
		if(KOTOBA_ENABLE_STAT)
			call_user_func_array($kotoba_stat, array(ERR_THEME_TOO_LONG));

		$error_message = "Ошибка. Тема слишком длинная.";
		return false;
	}

	if(strlen($message_name) > KOTOBA_MAX_NAME_LENGTH)
	{
		if(KOTOBA_ENABLE_STAT)
			call_user_func_array($kotoba_stat, array(ERR_NAME_TOO_LONG));

		$error_message = "Ошибка. Имя пользователя слишком длинное.";
		return false;
	}
	$error_message = "success";
	return true;
}

/* post_check_message_size check message text size
 * TODO
 */
function post_check_message_size(&$message_text, $kotoba_stat, &$error_message) {
	if(mb_strlen($message_text) > KOTOBA_MAX_MESSAGE_LENGTH)
	{
		if(KOTOBA_ENABLE_STAT)
			call_user_func_array($kotoba_stat, array(ERR_TEXT_TOO_LONG));
			
		$error_message = "Ошибка. Текст сообщения слишком длинный.";
		return false;
	}
	return true;
}
/*
 * postMark format text
 * TODO
 */
function post_mark($link, &$message_text, &$message_theme, &$message_name, $kotoba_stat, &$error_message) {
require 'mark.php';
KotobaMark($link, $message_text);
	$message_text = preg_replace("/\n/", '<br>', $message_text);
	if(!post_check_message_size($message_text, $kotoba_stat, $error_message)) {
		return false;
	}
	// It not incrase size, so don't matter )
	$message_text = preg_replace('/(<br>){3,}/', '<br><br>', $message_text);

	$message_theme = str_replace("\n", '', $message_theme);
	$message_theme = str_replace("\r", '', $message_theme);

	$message_name = str_replace("\n", '', $message_name);
	$message_name = str_replace("\r", '', $message_name);

	return true;
}

/*
 * post_get_uploaded_extension gets uploded extesion
 * TODO
 */
function post_get_uploaded_extension($filename) {
	$uploaded_parts = pathinfo($filename);
	return $uploaded_parts['extension'];
}

/*
 * post_create_filenames create filenames for uploaded image and thumbnail
 * TODO
 */
function post_create_filenames($recived_ext, $original_ext) {
	list($usec, $sec) = explode(' ', microtime());
	$saved_filename = $sec . substr($usec, 2, 5);				// Три знака после запятой.
	$saved_thumbname = $saved_filename . 't.' . $recived_ext;   // Имена всех миниатюр заканчиваются на t.
	$raw_filename = $saved_filename;
	$saved_filename .= ".$original_ext";

	return array($saved_filename, $saved_thumbname, $raw_filename);
}

/*
 * post_move_uploded_file moves uploded file to kotoba folder
 */
function post_move_uploded_file($source, $target, $kotoba_stat, &$error_message) {
	if (!rename($source, $target))
	{
		if(KOTOBA_ENABLE_STAT)
			call_user_func_array($kotoba_stat, array(ERR_FILE_NOT_SAVED));
			
		$error_message = "Ошибка. Файл не удалось сохранить.";
		return false;
	}
	return true;
}
/*
 * post_get_same_image finds same images if any
 * return true if none found
 * if found return false and $result_array['sameimage'] is true
 * otherwise return false and error_message set
 * result_array fields:
 * 'thread' number of thread where is same message
 * 'post' number of post where is same message
 */
function post_get_same_image($board, $board_name, $hash, $kotoba_stat, &$result_array) {
	$result_array['sameimage'] = false;
	$sql = sprintf("select id, thread from posts where board = %d and locate(\"HASH:%s\", `Post Settings`) <> 0", $board, $hash);
    if($result = mysql_query($sql))
    {
        if(mysql_num_rows($result) == 0)
        {
			mysql_free_result($result);
			return true;
        }
        else
        {
            if(KOTOBA_ENABLE_STAT)
				call_user_func_array($kotoba_stat, array(ERR_FILE_ALREADY_EXIST));

            $row = mysql_fetch_array($result, MYSQL_NUM);
            mysql_free_result($result);
			$result_array['sameimage'] = true;
			$result_array['thread'] = $row[1];
			$result_array['post'] = $row[0];
			return false;
        }
    }
    else
    {
        if(KOTOBA_ENABLE_STAT)
			call_user_func_array($kotoba_stat, array(sprintf(ERR_FILE_EXIST_FAILED, $board_name, mysql_error())));
        
		$result_array['sameimage'] = false;
		$result_array['error_message'] = sprintf("Ошибка. Не удалось проверить существание картинки на доске с именем %s. Прична: %s", $board_name, mysql_error());
		return false;
    }
}

/* post_remove_files: remove uploaded files
 * return nothing
 * $image uploaded image file name
 * $thumbnail genereated thumbnail
 */
function post_remove_files($image, $thumbnail) {
	@unlink($image);
	@unlink($thumbnail);
}

/* upload: create database record about apload
 * return upload identifier on success, -1 on error
 * arguments: 
 * $link - database link
 * $boardid - board id
 * $name - filename
 * $size - file size
 * $hash - upload checksum
 * $image - upload image boolean flag
 * $upload - upload uri
 * $upload_w - upload width
 * $upload_h - upload height
 * $thu - thumbnail uri
 * $thu_w - thumbnail width
 * $thu_h - thumbnail height
 */

function upload($link, $boardid, $name, $size, $hash, $image, $upload, $upload_w, $upload_h, $thu, $thu_w, $thu_h)
{
	$st = mysqli_prepare($link, "call sp_upload(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "isisisiisii", $boardid, $name, $size, $hash, $image, $upload, $upload_w, $upload_h,
		$thu, $thu_w, $thu_h)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $uploadid);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $uploadid;
}

/* post: stores post in database
 * return post identifier on success, -1 on error
 * arguments: 
 * $link - database link
 * $boardid - board id
 * $threadid - thread num (open post number)
 * $postname - name of poster
 * $postemail - email of poster
 * $postsubject - subject of post
 * $postpassword - password for deletion
 * $postersessionid - session id of poster
 * $posterip - poster ip (integer)
 * $posttext - post text
 * $datetime - date time in sql format
 * $sage - post doesn't up thread
 */
function post($link, $boardid,$threadid,$postname,$postemail,$postsubject,$postpassword,
	$postersessionid,$posterip,$posttext,$datetime,$sage)
{
	$st = mysqli_prepare($link, "call sp_post(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "iisssssissi", $boardid,$threadid,$postname,$postemail,
		$postsubject,$postpassword,
		$postersessionid,$posterip,$posttext,$datetime,$sage)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_bind_result($st, $postid);
	if(! mysqli_stmt_fetch($st)) {
		mysqli_stmt_close($st);
		cleanup_link($link);
		return -1;
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $postid;
}

/* link_post_upload: link post and upload
 * returns nothing
 * arguments:
 * $link - database link
 * $boardid - board id
 * $uploadid - upload identifier (see upload function)
 * $postid - post identifier (see post function)
 */

function link_post_upload($link, $boardid, $uploadid, $postid)
{
	$st = mysqli_prepare($link, "call sp_post_upload(?, ?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "iii", $boardid, $postid, $uploadid)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
}

// TODO: non-effective search
/* post_check_supported_type: check if extension of uploaded file is allowed to post
 * returns boolean
 * arguments:
 * $extension - extension of uploaded file
 * $types - array where keys is allowed to post extensions
 */
function post_check_supported_type($extension, &$types) {
	return array_key_exists($extension, $types);
}

/* post_find_same_uploads: find same uploads on board
 * return array of identifiers of uploads (empty if there is wasn't same uploads)
 * arguments:
 * $link - database link
 * $boardid - board id
 * $img_hash - hash of upload
 */
function post_find_same_uploads($link, $boardid, $img_hash) {
	$uploads = array();
	$st = mysqli_prepare($link, "call sp_same_uploads(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "is", $boardid, $img_hash)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_bind_result($st, $id)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	while(mysqli_stmt_fetch($st)) {
		array_push($uploads, array('id' => $id));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $uploads;
}

/* post_show_uploads_links: show information about same uploads
 * XXX: warning! terminates script!
 * returns nothing
 * arguments:
 * $link - database link
 * $boardid - board id
 * $same_uploads - array of identifiers of uploads (see post_find_same_uploads function)
 */
function post_show_uploads_links($link, $boardid, &$same_uploads) {
	$links = array();
	foreach($same_uploads as $uploadid) {
		array_push($links, post_get_uploadlink($link, $boardid, $uploadid['id']));
	}

	$smarty = new SmartyKotobaSetup();
	$smarty->assign('links', $links);
	$smarty->display('post_same_uploads.tpl');
	exit;
}

/* post_get_uploadlink: get post information linked with upload id
 * returns array with fields:
 * board_name, thread_num, post_num
 * arguments:
 * $link - database link
 * $boardid - board id
 * $uploadid - upload identifier
 */

function post_get_uploadlink($link, $boardid, $uploadid) {
	$posts = array();
	$st = mysqli_prepare($link, "call sp_upload_post(?, ?)");
	if(! $st) {
		kotoba_error(mysqli_error($link));
	}
	if(! mysqli_stmt_bind_param($st, "ii", $boardid, $uploadid)) {
		kotoba_error(mysqli_stmt_error($st));
	}

	if(! mysqli_stmt_execute($st)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	if(! mysqli_stmt_bind_result($st, $board_name, $thread_num, $post_num)) {
		kotoba_error(mysqli_stmt_error($st));
	}
	while(mysqli_stmt_fetch($st)) {
		array_push($posts, array('board_name' => $board_name, 'thread_num' => $thread_num, 
			'post_num' => $post_num));
	}
	mysqli_stmt_close($st);
	cleanup_link($link);
	return $posts;
}

?>
