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
		'Редактирование обработчиков удаления нитей',
		$_SESSION['user'],
		$_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
$popdown_handlers = db_popdown_handlers_get($link, $smarty);
$reload_popdown_handlers = false;	// Были ли произведены изменения.
/*
 * Добавим обработчик удаления нитей.
 */
if(isset($_POST['new_popdown_handler']) && $_POST['new_popdown_handler'] !== '')
{
	if(($new_popdown_handler_name = check_format('popdown_handler', $_POST['new_popdown_handler'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['POPDOWN_HANDLER_NAME'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	db_popdown_handlers_add($new_popdown_handler_name, $link, $smarty);
	$reload_popdown_handlers = true;
}
/*
 * Удалим обработчики удаления нитей.
 */
foreach($popdown_handlers as $handler)
	if(isset($_POST['delete_' . $handler['id']]))
	{
		db_popdown_handlers_delete($handler['id'], $link, $smarty);
		$reload_popdown_handlers = true;
	}
/*
 * Если нужно, получение обновлённого списка обработчиков удаления нитей,
 * вывод формы редактирования.
 */
if($reload_popdown_handlers)
	 $popdown_handlers = db_popdown_handlers_get($link, $smarty);
mysqli_close($link);
$smarty->assign('popdown_handlers', $popdown_handlers);
$smarty->display('edit_popdown_handlers.tpl');
?>