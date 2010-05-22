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
// Скрипт удаления висячих файлов (не связанных с сообщениями).
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
	$smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);
	// Возможно завершение работы скрипта.
	bans_check($smarty, ip2long($_SERVER['REMOTE_ADDR']));
	if(!in_array(Config::ADM_GROUP_NAME, $_SESSION['groups']))
		throw new PremissionException(PremissionException::$messages['NOT_ADMIN']);
	Logging::write_message(sprintf(Logging::$messages['ADMIN_FUNCTIONS_DELETE_DANGLING_FILES'],
				$_SESSION['user'], $_SERVER['REMOTE_ADDR']),
			Config::ABS_PATH . '/log/' . basename(__FILE__) . '.log');
	$uploads = uploads_get_all_dangling();
	$boards = boards_get_all();
	$delete_count = 0;
	foreach($uploads as &$u)
	{
		switch($u['upload_type'])
		{
			case Config::LINK_TYPE_VIRTUAL:
				$full_path = null;
				$found = false;
				foreach($boards as $b)
				{
					$full_path = Config::ABS_PATH
						. "/{$b['name']}/img/{$u['link']}";
					if(file_exists($full_path))
					{
						$found = true;
						break;
						// TODO А если вдруг файл не только на этой доске?
					}
				}
				if($u['hash'] === null || ($found
						&& $u['hash'] == calculate_file_hash($full_path)))
				{
					if(isset($_POST['submit'])
						&& (isset($_POST["delete_{$u['id']}"])
							|| isset($_POST['delete_all'])))
					{
						if($found)
							unlink($full_path);
						uploads_delete_specifed($u['id']);
						$delete_count++;
					}
					elseif(!isset($_POST['submit']))
					{
						$u['flag'] = true;
						$u['link'] = Config::DIR_PATH
							. "/{$b['name']}/img/{$u['link']}";
						$u['thumbnail'] = Config::DIR_PATH
							. "/{$b['name']}/thumb/{$u['thumbnail']}";
					}
				}
				break;
			case Config::LINK_TYPE_URL:
				if(isset($_POST['submit'])
					&& (isset($_POST["delete_{$u['id']}"])
						|| isset($_POST['delete_all'])))
				{
					uploads_delete_specifed($u['id']);
					$delete_count++;
				}
				elseif(!isset($_POST['submit']))
				{
					$u['flag'] = true;
				}
				break;
			case Config::LINK_TYPE_CODE:
				if(isset($_POST['submit'])
					&& (isset($_POST["delete_{$u['id']}"])
						|| isset($_POST['delete_all'])))
				{
					uploads_delete_specifed($u['id']);
					$delete_count++;
				}
				elseif(!isset($_POST['submit']))
				{
					$u['flag'] = true;
					$u['is_embed'] = true;
					$smarty->assign('code', $u['link']);
					$u['link'] = $smarty->fetch('youtube.tpl');
				}
				break;
		}
	}
	DataExchange::releaseResources();
	$smarty->assign('uploads', $uploads);
	if(isset($_POST['submit']))
		$smarty->assign('delete_count', $delete_count);
	$smarty->display('delete_dangling_files.tpl');
	exit;
}
catch(Exception $e)
{
	$smarty->assign('msg', $e->__toString());
	DataExchange::releaseResources();
	die($smarty->fetch('error.tpl'));
}
?>