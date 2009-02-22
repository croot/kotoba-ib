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
define('KOTOBA_POST_LIMIT', 400);       // Число постов доски, по достижению которого начинают тонуть треды.

/*
 * Разбирает строку настроек $settings
 * в зависимости от типа $type. Вовзращает
 * ассоциированный массив с настройками.
 * 
 * Например, для типа "post" строка настроек представляет
 * собой пары КЛЮЧ:ЗНАЧЕНИЕ, разделённые символом \n:
 * КЛЮЧ1:ЗНАЧЕНИЕ1\n
 * КЛЮЧ2:ЗНАЧЕНИЕ2\n
 * ...
 * Функция вернёт ассоциированный массив:
 * КЛЮЧ1 => ЗНАЧЕНИЕ1
 * КЛЮЧ2 => ЗНАЧЕНИЕ2
 * ...
 * 
 */
function GetSettings($type, $settings)
{
    switch($type)
    {
        case 'post':
        case 'thread':
        case 'board':
		case 'user':
            $h = array();
            $settings_array = explode("\n", $settings);

            for($i = 0; $i < count($settings_array); $i++)
            {
                $key = substr($settings_array[$i], 0, strpos($settings_array[$i], ':'));
                $value = substr($settings_array[$i], strpos($settings_array[$i], ':') + 1, strlen($settings_array[$i]));
                
                if($value != '')
                {
                    $h[$key] = $value;
                }
            }

            return $h;

        default:
            return null;
    }
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

				if($length > 2 || (ctype_digit($value) === false) || $length < 1)
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