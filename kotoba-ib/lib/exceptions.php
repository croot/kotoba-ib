<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Exception extensions.
 * @package api
 */

/**
 * Ensure what requirements to use functions and classes from this script are met.
 */
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}

// Load default error messages.
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/exceptions.php';

/**
 * Common exception.
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
        $html_encoded_message = htmlentities(parent::__toString(), ENT_QUOTES, Config::MB_ENCODING);
        //var_dump($html_encoded_message);
        return str_replace("\n", "<br>\n", $html_encoded_message);
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
 * No data exception.
 * @package exceptions
 */
class NodataException extends Exception {
    static $messages;
    private $reason;
    /**
     * Creates new no data exception.
     * @param string $message Error message.
     */
    public function __construct($message) {
        if ( ($n = func_num_args()) > 1) {
            $message = vsprintf($message, array_slice(func_get_args(), 1, $n));
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
 * Data format exception.
 * @package exceptions
 */
class FormatException extends Exception {

    static $messages;
    private $reason;

    /**
     * Creates new data format exception.
     * @param string $message Error message.
     */
    public function __construct($message) {
        if ( ($n = func_num_args()) > 1) {
            $message = vsprintf($message, array_slice(func_get_args(), 1, $n));
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
 * Registration, authorization, identification and access violation exception.
 * @package exceptions
 */
class PermissionException extends Exception {
    static $messages;
    private $reason;
    /**
     * Creates new exception.
     * @param string $message Error message.
     */
    public function __construct($message) {
        if ( ($n = func_num_args()) > 1) {
            $message = vsprintf($message, array_slice(func_get_args(), 1, $n));
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
 * Limit exceptions.
 * @package exceptions
 */
class LimitException extends Exception {
    static $messages;
    private $reason;
    /**
     * Creates new exception.
     * @param string $message Error message.
     */
    public function __construct($message) {
        if ( ($n = func_num_args()) > 1) {
            $message = vsprintf($message, array_slice(func_get_args(), 1, $n));
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
?>