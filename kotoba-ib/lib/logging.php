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
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'config.php'; })) {
    throw new Exception('Configuration file <b>config.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'errors.php'; })) {
    throw new Exception('Error handing file <b>errors.php</b> must be included and executed BEFORE '
                        . '<b>' . basename(__FILE__) . '</b> but its not.');
}

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

        date_default_timezone_set(Config::DEFAULT_TIMEZONE);
        if (self::$log_file == null) {
            // TODO Надо добавить в конфиг этот формат даты.
            self::$log_file = fopen(Config::ABS_PATH . '/log/actions-' . date('Y-m-d') . '.log', 'a');
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