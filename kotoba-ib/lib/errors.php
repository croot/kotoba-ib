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
                        . '<b>' . __FILE__ . '</b> but its not.');
}
if (!array_filter(get_included_files(), function($path) { return basename($path) == 'exceptions.php'; })) {
    throw new Exception('File <b>exceptions.php</b> must be included and executed BEFORE '
                        . '<b>' . __FILE__ . '</b> but its not.');
}

// Load default errors data array.
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';

$K_ERROR['default_error']['handler'] = function ($smarty) {
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('ib_name', Config::IB_NAME);
    $smarty->assign('error', $K_ERROR['default_error']);

    die($smarty->fetch('error.tpl'));
};

/*function kotoba_error_make($format, $func) {
    return function() use($format, $func) {
        return call_user_func_array($func, array_merge(array($format), func_get_args()));
    };
}
$KOTOBA_ERROR_DEFAULT_HANDLER = function($format) {
    echo "$format\n";
};
$default = kotoba_error_make('Default error.', $KOTOBA_ERROR_DEFAULT_HANDLER);
$default();
$custom1 = kotoba_error_make('Custom error 1.', $KOTOBA_ERROR_DEFAULT_HANDLER);
$custom1();
$custom2 = kotoba_error_make('Custom error 2. v = %s',
                             function($format, $v) {
                                 echo sprintf($format, $v) . "\n";
                             });
$custom2('lol');

class KError {
    private $format;
    private $handler;

    function  __construct($format, $handler) {
        $this->format = $format;
        $this->handler = $handler;
    }

    function __invoke() {
        if (is_callable($this->handler)) {
            call_user_func_array($this->handler, array_merge(array($this->format), func_get_args()));
        }
    }
};

$K_ERRORS_DEFAULT_HANDLER = function($format) {
    echo "$format\n";
};
$K_ERRORS['DEFAULT'] = new KError('Default error.',
                                  $K_ERRORS_DEFAULT_HANDLER);
$K_ERRORS['CUSTOM1'] = new KError('Custom error 1.',
                                  $K_ERRORS_DEFAULT_HANDLER);
$K_ERRORS['CUSTOM2'] = new KError('Custom error 2. v = %s',
                                  function($format, $v) {
                                      echo sprintf($format, $v) . "\n";
                                  });

$K_ERRORS['DEFAULT']();
$K_ERRORS['CUSTOM1']();
$K_ERRORS['CUSTOM2']('lol');*/
?>