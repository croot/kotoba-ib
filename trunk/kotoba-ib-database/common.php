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

error_reporting(E_ALL);

/*
 * Разбирает строку настроек $settings.
 * Вовзращает ассоциированный массив с 
 * настройками КЛЮЧ => ЗНАЧЕНИЕ.
 * 
 * Строка настроек состоит из пар КЛЮЧ:ЗНАЧЕНИЕ,
 * разделённых символом \n:
 * 
 * КЛЮЧ1:ЗНАЧЕНИЕ1\n
 * КЛЮЧ2:ЗНАЧЕНИЕ2\n
 * ...
 */
function get_settings($type, $settings)
{
    $h = array();
	$settings_array = explode("\n", $settings);

	for($i = 0; $i < count($settings_array); $i++)
	{
		$key = substr($settings_array[$i], 0, strpos($settings_array[$i], ':'));
		$value = substr($settings_array[$i], strpos($settings_array[$i], ':') + 1, strlen($settings_array[$i]));

		if($value != '')
			$h[$key] = $value;
	}

	return $h;
}

/*
 * Проверяет корректность значения $value
 * в зависимости от типа $type.
 * 
 * Например: значение типа "board" должно быть
 * строкой длины от 1 до 16 байт включительно,
 * которая состоит из символов a-zA-Z0-9_-.
 * 
 */
function CheckFormat($type, $value)
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
 * Алиас для CheckFormat($type, $value);
 */
function check_format($type, $value)
{
    return CheckFormat($type, $value);
}

// smarty setup
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

/* kotoba_setup - initialize global variables, start session and so on
 * nothing returns and dont expect arguments
 * locale setings hardcoded!
 */
function kotoba_setup() {
	ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH  . '/sessions/');
	ini_set('session.gc_maxlifetime', 60 * 60 * 24);    // 1 день.
	ini_set('session.cookie_lifetime', 60 * 60 * 24);
	session_start();
	// todo: configure locales!
	mb_language('ru');
	mb_internal_encoding("UTF-8");
	$res = setlocale(LC_ALL, 'ru_RU.UTF-8', 'ru', 'rus', 'russian');
	if(!$res) {
		kotoba_error("locale failed");
	}
}

// vim: set encoding=utf-8:
?>
