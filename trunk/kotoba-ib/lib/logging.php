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

/**
 * Обёртка для удобства.
 * @package logging
 */
class Logging {
    static $messages;
    private static $log_file = null;
    private static $path = null;

    /**
     * Закрвает лог файл.
     */
    public static function close_log() {
        fclose(self::$log_file);
        self::$log_file = null;
        self::$path = null;
    }
    
    /**
     * Записывает сообщение в лог файл.
     * @param string $path Лог файл.
     * @param string $msg Сообщение.
     * @param string $message,... Параметры для вставки в сообщение.
     */
    public static function write_msg($path, $msg) {
        if (self::$log_file == null || self::$path != $path) {
            if (self::$log_file != null) {
                self::close_log(); // Закроем старый файл.
            }
            if ( (self::$log_file = @fopen($path, 'a')) === false) {
                throw new CommonException(CommonException::$messages['LOG_FILE']);
            }
            self::$path = $path;
        }

        $n = func_num_args();
        if ($n > 2) {
            // Lets collect parametrs.
            $args = array();
            for ($i = 2; $i < $n; $i++) {	// Skip first two arguments.
                array_push($args, func_get_arg($i));
            }
            fwrite(self::$log_file,
                    vsprintf($msg, $args) . " (" . @date("Y-m-d H:i:s")
                    . ")\n");
        } else {
            fwrite(self::$log_file, "$msg (" . @date("Y-m-d H:i:s") . ")\n");
        }
    }
}
?>