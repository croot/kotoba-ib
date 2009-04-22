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

define('KOTOBA_DIR_PATH', '/k');		// Путь от корня документов к директории, где хранится index.php. Например: /test/kotoba_drive
define('KOTOBA_ALLOW_SAEMIMG', false);	// Разрешить постить одинакоые картинки. true or false.
define('KOTOBA_ENABLE_STAT', true);		// Включить сбор статистики. true or false.
define('KOTOBA_BUMPLIMIT', 30);			// Глобальный бамплимит.

define('KOTOBA_MIN_IMGWIDTH', 32);      // Мнимальная ширина загружаемого изображения.
define('KOTOBA_MIN_IMGHEIGTH', 32);     // Минимальная высота загружаемого изображения.
define('KOTOBA_MIN_IMGSIZE', 1000);     // Минимальный размер загружаемого файла.

define('KOTOBA_LONGPOST_LINES', 10);    // Число строк поста, отображаемое в предпросмотре доски.
define('KOTOBA_POST_LIMIT', 600);       // Число постов доски, по достижению которого начинают тонуть треды.

define('KOTOBA_MAX_MESSAGE_LENGTH', 30000);	// Максимальная длина текста сообщения в байтах.
define('KOTOBA_MAX_THEME_LENGTH', 120);		// Максимальная длина темы в байтах.
define('KOTOBA_MAX_NAME_LENGTH', 64);		// Максимальная длина имени в байтах.

define('KOTOBA_TRY_IMAGE_GD', 1);		// try load libgd support for image processing
define('KOTOBA_TRY_IMAGE_IM', 0);		// try load imagemagick support for image processing

define('KOTOBA_SMALLIMAGE_LIMIT_FILE_SIZE', 1048576);	// small image over that limit wouldn't 
														// processing

define('KOTOBA_THUMB_SUCCESS', 0);		// thumbnail succesfully created
define('KOTOBA_THUMB_UNSUPPORTED', 1);	// unsupported format
define('KOTOBA_THUMB_NOLIBRARY', 2);	// no suitable library
define('KOTOBA_THUMB_TOOBIG', 4);		// file too big
define('KOTOBA_THUMB_UNKNOWN', 255);	// unknown error

define('KOTOBA_SESSION_LIFETIME', 86400);	// Используется для параметров session.gc_maxlifetime и session.cookie_lifetime

define('KOTOBA_FRAMED_INTERFACE', 1);	// Kotoba uses frames

/*
 * Обёртка с настройками Smarty.
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
?>
