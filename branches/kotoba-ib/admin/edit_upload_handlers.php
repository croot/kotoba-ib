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

require '../kwrapper.php';
require_once Config::ABS_PATH . '/lang/' . Config::LANGUAGE . '/logging.php';

kotoba_setup($link, $smarty);
/*
 * Только для администраторов.
 */
if(! in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
{
	mysqli_close($link);
	kotoba_error(Errmsgs::$messages['NOT_ADMIN'],
		$smarty,
		basename(__FILE__) . ' ' . __LINE__);
}
kotoba_log(sprintf(Logmsgs::$messages['ADMIN_FUNCTIONS'],
		'Редактирование обработчиков загружаемых файлов',
		$_SESSION['user'],
		$_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
$upload_handlers = db_upload_handlers_get($link, $smarty);
$reload_upload_handlers = false;	// Были ли произведены изменения.
/*
 * Добавим обработчик загружаемых файлов.
 */
if(isset($_POST['new_upload_handler']) && $_POST['new_upload_handler'] !== '')
{
	if(($new_upload_handler_name = check_format('upload_handler', $_POST['new_upload_handler'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['UPLOAD_HANDLER_NAME'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	db_upload_handlers_add($new_upload_handler_name, $link, $smarty);
	$reload_upload_handlers = true;
}
/*
 * Удалим обработчики загружаемых файлов.
 */
foreach($upload_handlers as $handler)
	if(isset($_POST['delete_' . $handler['id']]))
	{
		db_upload_handlers_delete($handler['id'], $link, $smarty);
		$reload_upload_handlers = true;
	}
/*
 * Если нужно, получение обновлённого списка обработчиков загружаемых файлов,
 * вывод формы редактирования.
 */
if($reload_upload_handlers)
	 $upload_handlers = db_upload_handlers_get($link, $smarty);
mysqli_close($link);
$smarty->assign('upload_handlers', $upload_handlers);
$smarty->display('edit_upload_handlers.tpl');
?>