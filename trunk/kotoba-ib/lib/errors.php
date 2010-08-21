<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Скрипт расширений стандартного исключения.
 * @package api
 */

/**
 * Разные ошибки.
 * @package exceptions
 */
class CommonException extends Exception {
	static $messages;
	private $reason;
	/**
	 * Создаёт новое исключение с заданным сообщением.
	 * @param message string <p>Сообщение.</p>
	 */
	public function __construct($message)
	{
		$this->reason = $message;
		parent::__construct($message);
	}
	/**
	 * Возвращает данные об исключении.
	 */
	public function __toString()
	{
		return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
	}
	/**
	 * Возвращает причину произошедшей ошибки.
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
/**
 * Ошибки поиска.
 * @package exceptions
 */
class SearchException extends Exception {
	static $messages;
	private $reason;
	/**
	 * Создаёт новое исключение с заданным сообщением.
	 * @param message string <p>Сообщение.</p>
	 */
	public function __construct($message)
	{
		$this->reason = $message;
		parent::__construct($message);
	}
	/**
	 * Возвращает данные об исключении.
	 */
	public function __toString()
	{
		return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
	}
	/**
	 * Возвращает причину произошедшей ошибки.
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
/**
 * Ошибки отсутствия данных.
 * @package exceptions
 */
class NodataException extends Exception {
	static $messages;
	private $reason;
	/**
	 * Создаёт новое исключение с заданным сообщением.
	 * @param message string <p>Сообщение.</p>
	 */
	public function __construct($message)
	{
		$this->reason = $message;
		parent::__construct($message);
	}
	/**
	 * Возвращает данные об исключении.
	 */
	public function __toString()
	{
		return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
	}
	/**
	 * Возвращает причину произошедшей ошибки.
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
/**
 * Ошибки формата данных.
 * @package exceptions
 */
class FormatException extends Exception {
	static $messages;
	private $reason;
	/**
	 * Создаёт новое исключение с сообщением $message.
	 * @param message string <p>Сообщение.</p>
	 */
	public function __construct($message)
	{
		$this->reason = $message;
		parent::__construct($message);
	}
	/**
	 * Возвращает данные об исключении.
	 */
	public function __toString()
	{
		return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
	}
	/**
	 * Возвращает причину произошедшей ошибки.
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
/**
 * Ошибки при регистрации, авторизации, идентификации и прав доступа.
 * @package exceptions
 */
class PermissionException extends Exception {
	static $messages;
	private $reason;
	/**
	 * Создаёт новое исключение с сообщением $message.
	 * @param message string <p>Сообщение.</p>
	 */
	public function __construct($message)
	{
		$this->reason = $message;
		parent::__construct($message);
	}
	/**
	 * Возвращает данные об исключении.
	 */
	public function __toString()
	{
		return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
	}
	/**
	 * Возвращает причину произошедшей ошибки.
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
/**
 * Ошибки обмена данными с хранилищем.
 * @package exceptions
 */
class DataExchangeException extends Exception {
	static $messages;
	private $reason;
	/**
	 * Создаёт новое исключение с сообщением $message.
	 * @param message string <p>Сообщение.</p>
	 */
	public function __construct($message)
	{
		$this->reason = $message;
		parent::__construct($message);
	}
	/**
	 * Возвращает данные об исключении.
	 */
	public function __toString()
	{
		return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
	}
	/**
	 * Возвращает причину произошедшей ошибки.
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
/**
 * Ошибки загрузки файла.
 * @package exceptions
 */
class UploadException extends Exception {
	static $messages;
	private $reason;
	/**
	 * Создаёт новое исключение с сообщением $message.
	 * @param message string <p>Сообщение.</p>
	 */
	public function __construct($message)
	{
		$this->reason = $message;
		parent::__construct($message);
	}
	/**
	 * Возвращает данные об исключении.
	 */
	public function __toString()
	{
		return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
	}
	/**
	 * Возвращает причину произошедшей ошибки.
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
/**
 * Нарушение ограничений.
 * @package exceptions
 */
class LimitException extends Exception {
	static $messages;
	private $reason;
	/**
	 * Создаёт новое исключение с сообщением $message.
	 * @param message string <p>Сообщение.</p>
	 */
	public function __construct($message)
	{
		$this->reason = $message;
		parent::__construct($message);
	}
	/**
	 * Возвращает данные об исключении.
	 */
	public function __toString()
	{
		return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
	}
	/**
	 * Возвращает причину произошедшей ошибки.
	 */
	public function getReason()
	{
		return $this->reason;
	}
}
?>