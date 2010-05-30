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
// Скрипт редактирования досок.
require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';
try
{
	kotoba_session_start();
	locale_setup();
	$smarty = new SmartyKotobaSetup($_SESSION['language'],
		$_SESSION['stylesheet']);
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_EDIT_WORDFILTER'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$words = words_get_all();
	$reload_words = false;	// Были ли произведены изменения.
	if(isset($_POST['submited']))
	{
// Добавление нового слова.
		if(isset($_POST['new_word'])
			&& isset($_POST['new_replace'])
			&& $_POST['new_word'] != ''
			&& $_POST['new_replace'] != '')
		{
			if($_POST['new_word'] == '')
				throw new CommonException(CommonException::$messages['WORD_FOR_REPLACE']);
			if($_POST['new_replace'] == '')
				throw new CommonException(CommonException::$messages['REPLACE_FOR_WORD']);
			/*
			 * Проверим, длинну слов
			 */
			 if(strlen($_POST['new_word'])>100)
				throw new CommonException(CommonException::$messages['TOO_LONG']);
			 if(strlen($_POST['new_replace'])>100)
				throw new CommonException(CommonException::$messages['TOO_LONG']);
			/*
			 * Проверим, нет ли уже такого слова. Если есть, то изменим
			 * параметры существующей.
			 */
			/*
			 * Присваиваем переменным пост данные, хотя можно было бы обойтись и без этого.
			 */
			$new_word = $_POST['new_word'];
			$new_replace = $_POST['new_replace'];
			
			$found = false;
			foreach($words as $word)
				if($word['word'] == $new_word && $found = true)
				{
					words_edit($word['id'], $new_word, $new_replace);
					$reload_words = true;
					break;
				}
			if(!$found)
			{
				words_add($new_word, $new_replace);
				$reload_words = true;
			}
		}// Создание нового слова.
// Изменение параметров существующих слов.
		foreach($words as $word)
		{
			//Было ли изменено слово для замены
			$param_name = "word_{$word['id']}";
			$new_word = $word['id'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $word['id'])
			{
				if($_POST[$param_name] == '')
					$new_word = null;
				else
					$new_word = $_POST[$param_name];
				if(strlen($new_word)>100)
					throw new CommonException(CommonException::$messages['TOO_LONG']);
			}
			//Было ли изменено слово-замена?
			$param_name = "replace_{$word['id']}";
			$new_replace = $word['replace'];
			if(isset($_POST[$param_name])
				&& $_POST[$param_name] != $word['replace'])
			{
				if($_POST[$param_name] == '')
					$new_replace = null;
				else
					$new_replace = $_POST[$param_name];
				if(strlen($new_replace)>100)
					throw new CommonException(CommonException::$messages['TOO_LONG']);
			}
			// Были ли произведены какие-либо изменения?
			if($new_word != $word['id']
				|| $new_replace != $word['replace'])
			{
				words_edit($word['id'], $new_word, $new_replace);
				$reload_words = true;
			}
		}// Изменение параметров существующих досок.
// Удаление выбранных досок.
		foreach($words as $word)
			if(isset($_POST["delete_{$word['id']}"]))
			{
				words_delete($word['id']);
				$reload_words = true;
			}
	}
// Вывод формы редактирования.
	if($reload_words)
		$words = words_get_all();
	DataExchange::releaseResources();
	$smarty->assign('words', $words);
	$smarty->display('edit_words.tpl');
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>