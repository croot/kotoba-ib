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

// TODO Добавить создание и удаление директорий при добавлении и удалении языка, соответственно.

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
		'Редактирование языков',
		$_SESSION['user'],
		$_SERVER['REMOTE_ADDR']),
	Logmsgs::open_logfile(Config::ABS_PATH . '/log/' .
		basename(__FILE__) . '.log'));
$languages = db_languages_get($link, $smarty);
$reload_languages = false;	// Были ли произведены изменения.
/*
 * Добавим новый язык.
 */
if(isset($_POST['new_language']) && $_POST['new_language'] !== '')
{
	if(($new_language_name = check_format('language', $_POST['new_language'])) == false)
	{
		mysqli_close($link);
		kotoba_error(Errmsgs::$messages['LANGUAGE_NAME'],
			$smarty,
			basename(__FILE__) . ' ' . __LINE__);
	}
	db_languages_add($new_language_name, $link, $smarty);
	$reload_languages = true;
}
/*
 * Удалим языки.
 */
foreach($languages as $language)
	if(isset($_POST['delete_' . $language['id']]))
	{
		db_languages_delete($language['id'], $link, $smarty);
		$reload_languages = true;
	}
/*
 * Если нужно, получение обновлённого списка языков, вывод формы редактирования.
 */
if($reload_languages)
	$languages = db_languages_get($link, $smarty);
mysqli_close($link);
$smarty->assign('languages', $languages);
$smarty->display('edit_languages.tpl');
?>