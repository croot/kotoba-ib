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
// Скрипт редактирования обработчиков удаления нитей.
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
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_POPDOWN_HANDLERS'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$popdown_handlers = popdown_handlers_get_all();
	$reload_popdown_handlers = false;	// Были ли произведены изменения.
	// Добавление обработчика удаления нитей.
	if(isset($_POST['new_popdown_handler']) && $_POST['new_popdown_handler'] !== '')
	{
		popdown_handlers_add(popdown_handlers_check_name($_POST['new_popdown_handler']));
		$reload_popdown_handlers = true;
	}
	// Удаление обработчиков удаления нитей.
	foreach($popdown_handlers as $handler)
		if(isset($_POST['delete_' . $handler['id']]))
		{
			popdown_handlers_delete($handler['id']);
			$reload_popdown_handlers = true;
		}
	// Вывод формы редактирования.
	if($reload_popdown_handlers)
		 $popdown_handlers = popdown_handlers_get_all();
	DataExchange::releaseResources();
	$smarty->assign('popdown_handlers', $popdown_handlers);
	$smarty->display('edit_popdown_handlers.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>