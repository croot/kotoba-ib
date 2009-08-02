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
	function SmartyKotobaSetup()
	{
		$this->Smarty();

        $this->template_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/kotoba/templates/';
		$this->compile_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/kotoba/templates_c/';
		$this->config_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/kotoba/config/';
		$this->cache_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/kotoba/cache/';
        $this->caching = 0;

		$this->assign('KOTOBA_DIR_PATH', KOTOBA_DIR_PATH);
    }
}

/*
 * kotoba_setup - initialize global variables and so on
 * nothing returns and dont expect arguments
 * locale setings hardcoded!
 * ru: Настройка сессий, кукис, кодировки для mbstrings, локали.
 */
function kotoba_setup()
{
	ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH  . '/sessions/');
	ini_set('session.gc_maxlifetime', KOTOBA_SESSION_LIFETIME);
	ini_set('session.cookie_lifetime', KOTOBA_SESSION_LIFETIME);

    mb_language('ru');
	mb_internal_encoding("UTF-8");

	if(!setlocale(LC_ALL, 'ru_RU.UTF-8', 'ru', 'rus', 'russian'))
		kotoba_error("locale failed");
}

// vim: set encoding=utf-8:
?>
