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
// Скрипт редактирования обработчиков загружаемых файлов.
require '../config.php';
require Config::ABS_PATH . '/modules/errors.php';
require Config::ABS_PATH . '/modules/lang/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/modules/logging.php';
require Config::ABS_PATH . '/modules/lang/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/modules/db.php';
require Config::ABS_PATH . '/modules/cache.php';
require Config::ABS_PATH . '/modules/common.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));	// Возможно завершение работы скрипта.
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PremissionException(PremissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_UPLOAD_HANDLERS'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$upload_handlers = upload_handlers_get_all();
	$reload_upload_handlers = false;	// Были ли произведены изменения.
	// Регистрация обработчика загружаемых файлов.
	if(isset($_POST['new_upload_handler']) && $_POST['new_upload_handler'] !== '')
	{
		upload_handlers_add(upload_handlers_check_name($_POST['new_upload_handler']));
		$reload_upload_handlers = true;
	}
	// Удаление обработчика загружаемых файлов.
	foreach($upload_handlers as $handler)
		if(isset($_POST['delete_' . $handler['id']]))
		{
			upload_handlers_delete($handler['id']);
			$reload_upload_handlers = true;
		}
	// Вывод формы редактирования.
	if($reload_upload_handlers)
		 $upload_handlers = upload_handlers_get_all();
	DataExchange::releaseResources();
	$smarty->assign('upload_handlers', $upload_handlers);
	$smarty->display('edit_upload_handlers.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>