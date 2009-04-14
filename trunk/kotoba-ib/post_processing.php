<?
/* postprocessing.php: module for post processing */


/* postGetBoardId: get board id
 * return values: positive integer board id
 * on error return -1
 * arguments:
 * $board_name is board name
 * $kotoba_stat is kotoba stat function name
 * $error_message is reference to variable which would contain error message if any
 */
function postGetBoardId($board_name, $kotoba_stat, &$error_message) {
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
 * postCheckImageUploadError: check image upload errors
 * return true if no errors
 * false on error
 * arguments:
 * $error is error code from $_FILES[...]['error']
 * $kotoba_stat is kotoba_stat function name
 * $error_message is reference to variable which would contain error message if any
 */
function postCheckImageUploadError($error, $kotoba_stat, &$error_message) {
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
				call_user_func_array($kotoba_stat, array(ERR_UPLOAD_NO_FILE));

			$error_message = "Ошибка. Файл не был загружен.";
			return false;
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
 * postCheckSizes is check sizes of uploaded data
 * return true if no errors
 * false on error
 * arguments:
 * $uplodedFileSize is size of uploaded image
 * &$message_text is message text field
 * &$message_theme is message theme field
 * &$message_name is message name field
 * $kotoba_stat is kotoba_stat function name
 * $error_message is reference to variable which would contain error message if any
 */
function postCheckSizes($uplodedFileSize, &$message_text, &$message_theme, 
	&$message_name, $kotoba_stat, &$error_message) {

	if($uplodedFileSize < KOTOBA_MIN_IMGSIZE)
	{
		if(KOTOBA_ENABLE_STAT)
			call_user_func_array($kotoba_stat, array(ERR_FILE_TOO_SMALL));
			
		$error_message = "Ошибка. Загружаемый файл имеет слишком маленький размер.";
		return false;
	}

	if(!postCheckMessageSize($message_text, $kotoba_stat, $error_message)) {
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

/* postCheckMessageSize check message text size
 * TODO
 */
function postCheckMessageSize(&$message_text, $kotoba_stat, &$error_message) {
	if(strlen($message_text) > KOTOBA_MAX_MESSAGE_LENGTH)
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
function postMark(&$message_text, &$message_theme, &$message_name, $kotoba_stat, &$error_message) {
require 'mark.php';
KotobaMark($message_text);
	$message_text = preg_replace("/\n/", '<br>', $message_text);
	if(!postCheckMessageSize($message_text, $kotoba_stat, $error_message)) {
		return false;
	}
	// should it be placed before size checking?
	$message_text = preg_replace('/(<br>){3,}/', '<br><br>', $message_text);

	$message_theme = str_replace("\n", '', $message_theme);
	$message_theme = str_replace("\r", '', $message_theme);

	$message_name = str_replace("\n", '', $message_name);
	$message_name = str_replace("\r", '', $message_name);

	return true;
}

/*
 * postGetUploadedExtension gets uploded extesion
 * TODO
 */
function postGetUploadedExtension($filename) {
	$uploaded_parts = pathinfo($filename);
	return $uploaded_parts['extension'];
}

/*
 * postCreateFilenames create filenames for uploaded image and thumbnail
 * TODO
 */
function postCreateFilenames($recived_ext, $original_ext) {
	list($usec, $sec) = explode(' ', microtime());
	$saved_filename = $sec . substr($usec, 2, 5);				// Три знака после запятой.
	$saved_thumbname = $saved_filename . 't.' . $recived_ext;   // Имена всех миниатюр заканчиваются на t.
	$raw_filename = $saved_filename;
	$saved_filename .= ".$original_ext";

	return array($saved_filename, $saved_thumbname, $raw_filename);
}
?>
