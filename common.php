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

require_once 'config.php';
require_once 'exception_processing.php';

/*
 * Проверяет корректность значения $value
 * в зависимости от типа $type.
 *
 * Например: значение типа "board" должно быть
 * строкой длины от 1 до 16 байт включительно,
 * которая состоит из символов латинских букв в верхнем
 * и нижем регистре, цифр, знача подчеркивания и тире.
 *
 */
function check_format($type, $value)
{
    switch($type)
    {
        case 'stylesheet':
        case 'language':
            return RawUrlEncode($value);

        case 'keyword':
			$length = strlen($value);

			if($length <= 32 && $length >= 16)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 32 || (strpos($value, '%') !== false) || $length < 16)
					return false;
			}
			else
				return false;

			return $value;

        case 'posts_per_thread':
        case 'lines_per_post':
		case 'threads_per_page':
			$length = strlen($value);

			if($length <= 2 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 2 || (ctype_digit($value) === false) || $length < 1)
					return false;
			}
			else
				return false;

			return $value;

        case 'board':
			$length = strlen($value);

			if($length <= 16 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 16 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;

			return $value;

        case 'thread':
        case 'post':
            $length = strlen($value);

			if($length <= 9 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 9 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;

            return $value;

        case 'page':
            $length = strlen($value);

			if($length <= 2 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 2 || (ctype_digit($value) == false) || $length < 1)
					return false;
			}
			else
				return false;

            return $value;

        case 'pass':
            $length = strlen($value);

			if($length <= 12 && $length >= 1)
			{
				$value = RawUrlEncode($value);
				$length = strlen($value);

				if($length > 12 || (strpos($value, '%') !== false) || $length < 1)
					return false;
			}
			else
				return false;

            return $value;

        default:
            return false;
    }
}

/*
 * en: smarty setup
 * ru: Настройка Smarty.
 */
require_once($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/Smarty.class.php');

class SmartyKotobaSetup extends Smarty
{
	function SmartyKotobaSetup($language = KOTOBA_LANGUAGE)
	{
		$this->Smarty();

        $this->template_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/smarty/kotoba/templates/$language/";
		$this->compile_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/smarty/kotoba/templates_c/$language/";
		$this->config_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/smarty/kotoba/config/$language/";
		$this->cache_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . "/smarty/kotoba/cache/$language/";
        $this->caching = 0;

		$this->assign('KOTOBA_DIR_PATH', KOTOBA_DIR_PATH);
        $this->assign('stylesheet', KOTOBA_STYLESHEET);
    }
}

/*
 *
 */
function kotoba_session_start()
{
    ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH  . '/sessions/');
	ini_set('session.gc_maxlifetime', KOTOBA_SESSION_LIFETIME);
	ini_set('session.cookie_lifetime', KOTOBA_SESSION_LIFETIME);

    return session_start();
}

/*
 * Настройка кодировки для mbstrings, локали.
 */
function locale_setup()
{
	mb_language('ru');
	mb_internal_encoding("UTF-8");

	if(!setlocale(LC_ALL, 'ru_RU.UTF-8', 'ru', 'rus', 'russian'))
		kotoba_error("locale failed");
}

/*
 *
 */
function login() {
    if(!isset($_SESSION['user'])) { // По умолчанию пользователь является Гостем.
        $_SESSION['user'] = KOTOBA_GUEST_ID;
        $_SESSION['groups'] = array('Guests');
        $_SESSION['threads_per_page'] = KOTOBA_THREADS_PER_PAGE;
        $_SESSION['posts_per_thread'] = KOTOBA_POSTS_PER_THREAD;
        $_SESSION['lines_per_post'] = KOTOBA_LINES_PER_POST;
        $_SESSION['stylesheet'] = KOTOBA_STYLESHEET;
        $_SESSION['language'] = KOTOBA_LANGUAGE;
    }
}
// vim: set encoding=utf-8:
?>
