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
// Скрипт эвентов.
/**
 * Эвент Day Night меняет цветовое оформление в зависимости от времени суток.
 *
 * Аргументы:
 * $smarty - шаблонизатор.
 */
function event_daynight($smarty)
{
	date_default_timezone_set(Config::DEFAULT_TIMEZONE);
	$hour = date('G');
	$background = '';
	$color = '';
	$active = false;
	if($hour == "0")
	{
		$background = '000000';
		$color = 'CC9E8E';
		$active = true;
	}
	elseif($hour == "1" || $hour == "23")
	{
		$background = '191011';
		$color = 'CC9E8E';
		$active = true;
	}
	elseif($hour == "2" || $hour == "22")
	{
		$background = '332123';
		$color = 'CC9E8E';
		$active = true;
	}
	elseif($hour == "3" || $hour == "21")
	{
		$background = '4C3235';
		$color = 'CC9E8E';
		$active = true;
	}
	elseif($hour == "4" || $hour == "20")
	{
		$background = '664347';
		$color = '000000';
		$active = true;
	}
	elseif($hour == "5" || $hour == "19")
	{
		$background = '7F5459';
		$color = '000000';
		$active = true;
	}
	elseif($hour == "6" || $hour == "18")
	{
		$background = '99646B';
		$color = '000000';
		$active = true;
	}
	else
	{
		$background = 'B2757C';
		$color = '000000';
		$active = true;
	}
	if($active)
	{
		$code = 'DayNight event output.<style>'
			. "html, body{background:#$background; color:#$color;}"
			. ".reply{background: #$background; color:#$color;}"
			. '</style><br>';
		$smarty->assign('event_daynight_active', $active);
		$smarty->assign('event_daynight_code', $code);
	}
}
?>