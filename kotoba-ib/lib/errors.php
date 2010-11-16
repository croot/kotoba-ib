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

/***/
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}

/**
 * Разные ошибки.
 * @package exceptions
 */
class CommonException extends Exception {
    static $messages;
    private $reason;
    /**
     * Создаёт новое исключение с заданным сообщением.
     * @param string $message Сообщение.
     */
    public function __construct($message) {
        $this->reason = $message;
        parent::__construct($message);
    }
    /**
     * Возвращает данные об исключении.
     * @return string
     */
    public function __toString() {
        return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
    }
    /**
     * Возвращает причину произошедшей ошибки.
     * @return string
     */
    public function getReason() {
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
     * @param string $message Сообщение.
     */
    public function __construct($message) {
        $this->reason = $message;
        parent::__construct($message);
    }
    /**
     * Возвращает данные об исключении.
     * @return string
     */
    public function __toString() {
        return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
    }
    /**
     * Возвращает причину произошедшей ошибки.
     * @return string
     */
    public function getReason() {
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
     * @param string $message Сообщение.
     */
    public function __construct($message) {
        if (func_num_args() > 1) {
            $message = vsprintf($message, array_slice(func_get_args(), 1, func_num_args() - 1));
        }
        $this->reason = $message;
        parent::__construct($message);
    }
    /**
     * Возвращает данные об исключении.
     * @return string
     */
    public function __toString() {
        return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
    }
    /**
     * Возвращает причину произошедшей ошибки.
     * @return string
     */
    public function getReason() {
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
     * Создаёт новое исключение с заданным сообщением.
     * @param string $message Сообщение.
     */
    public function __construct($message) {
        $this->reason = $message;
        parent::__construct($message);
    }
    /**
     * Возвращает данные об исключении.
     * @return string
     */
    public function __toString() {
        return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
    }
    /**
     * Возвращает причину произошедшей ошибки.
     * @return string
     */
    public function getReason() {
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
     * Создаёт новое исключение с заданным сообщением.
     * @param string $message Сообщение.
     */
    public function __construct($message) {
        $this->reason = $message;
        parent::__construct($message);
    }
    /**
     * Возвращает данные об исключении.
     * @return string
     */
    public function __toString() {
        return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
    }
    /**
     * Возвращает причину произошедшей ошибки.
     * @return string
     */
    public function getReason() {
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
     * Создаёт новое исключение с заданным сообщением.
     * @param string $message Сообщение.
     */
    public function __construct($message) {
        $this->reason = $message;
        parent::__construct($message);
    }
    /**
     * Возвращает данные об исключении.
     * @return string
     */
    public function __toString() {
        return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
    }
    /**
     * Возвращает причину произошедшей ошибки.
     * @return string
     */
    public function getReason() {
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
     * Создаёт новое исключение с заданным сообщением.
     * @param string $message Сообщение.
     */
    public function __construct($message) {
        $this->reason = $message;
        parent::__construct($message);
    }
    /**
     * Возвращает данные об исключении.
     * @return string
     */
    public function __toString() {
        return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
    }
    /**
     * Возвращает причину произошедшей ошибки.
     * @return string
     */
    public function getReason() {
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
     * Создаёт новое исключение с заданным сообщением.
     * @param string $message Сообщение.
     */
    public function __construct($message) {
        $this->reason = $message;
        parent::__construct($message);
    }
    /**
     * Возвращает данные об исключении.
     * @return string
     */
    public function __toString() {
        return str_replace("\n", "<br>\n", htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING));
    }
    /**
     * Возвращает причину произошедшей ошибки.
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }
}

// Загрузка сообщений об ошибках на языке по умолчанию.
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
?>