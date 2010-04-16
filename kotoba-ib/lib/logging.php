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

/*
 * Логирование.
 */

/**
 * Просто обёртка для удобства.
 */
class Logging
{
	static $messages;
	private static $log_file = null;
	private static $path = null;
	public static function close_logfile()
	{
		fclose(self::$log_file);
		self::$path = null;
	}
	/**
	 * Записывает сообщение $message в лог файл $path.
	 *
	 * Аргументы:
	 * $message - сообщение.
	 * $path - лог файл.
	 */
	public static function write_message($message, $path)
	{
		if(self::$log_file == null || self::$path != $path)
		{
			if((self::$log_file = @fopen($path, 'a')) === false)
				throw new CommonException(CommonException::$messages['LOG_FILE']);
			self::$path = $path;
		}
		fwrite(self::$log_file, "$message (" . @date("Y-m-d H:i:s") . ")\n");
	}
}
?>