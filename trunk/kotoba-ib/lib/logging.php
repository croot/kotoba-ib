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
 * Логирование.
 * @package api
 */

/***/
// Конечный скрипт должен загрузить конфигурацию!
if (!class_exists('Config')) {
    throw new Exception('User-end script MUST load a configuraion!');
}
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
// TODO Может быть ещё что-то?

/**
 * Обёртка для удобства.
 * @package logging
 */
class Logging {
    static $messages;
    static $f;
    private static $log_file = null;

    /**
     * Закрвает лог файл.
     */
    public static function close_log() {
        if (self::$log_file) {
            fclose(self::$log_file);
            self::$log_file = null;
        }
    }
    
    /**
     * Записывает сообщение в лог файл.
     * @param string $msg Сообщение.
     */
    public static function write_msg($msg) {
        if (func_num_args() > 1) {
            throw new CommonException('Temporary disabled.');
        }

        if (self::$log_file == null) {
            self::$log_file = fopen(Config::ABS_PATH . '/log/actions.log', 'a');
            if (!self::$log_file) {
                throw new CommonException(CommonException::$messages['LOG_FILE']);
            }
        }

        fwrite(self::$log_file,
               date(Config::DATETIME_FORMAT) . '|'
               . "{$_SESSION['user']}" . '|'
               . join(', ', array_map(function($g){ return $g['name']; },
                                      groups_get_by_user($_SESSION['user']))) . '|'
               . "{$_SERVER['REMOTE_ADDR']}" . '|'
               . "$msg\n");
    }
}
?>