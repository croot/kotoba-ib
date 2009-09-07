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
		'Редактирование стилей оформления',
		$_SESSION['user'],
		$_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
$stylesheets = db_stylesheets_get($link, $smarty);
$reload_stylesheets = false;	// Были ли произведены изменения.
/*
 * Добавим стиль оформления.
 */
if(isset($_POST['new_stylesheet']) && $_POST['new_stylesheet'] !== '')
{
	if(($new_stylesheet_name = check_format('stylesheet', $_POST['new_stylesheet'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['STYLESHEET_NAME'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	db_stylesheets_add($new_stylesheet_name, $link, $smarty);
	$reload_stylesheets = true;
}
/*
 * Удалим стили.
 */
foreach($stylesheets as $stylesheet)
	if(isset($_POST['delete_' . $stylesheet['id']]))
	{
		db_stylesheets_delete($stylesheet['id'], $link, $smarty);
		$reload_stylesheets = true;
	}
/*
 * Если нужно, получение обновлённого списка стилей оформления, вывод формы
 * редактирования.
 */
if($reload_stylesheets)
	$stylesheets = db_stylesheets_get($link, $smarty);
mysqli_close($link);
$smarty->assign('stylesheets', $stylesheets);
$smarty->display('edit_stylesheets.tpl');
?>