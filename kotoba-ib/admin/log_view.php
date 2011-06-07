<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Log view script.

require_once '../config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/exceptions.php";
        require Config::ABS_PATH . "/locale/{$_SESSION['language']}/logging.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if ( ($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Check permission and write message to log file.
    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    call_user_func(Logging::$f['LOG_VIEW_USE']);

    $categories = categories_get_all();
    $boards = boards_get_all();

    // Make category-boards tree for navigation panel.
    foreach ($categories as &$c) {
        $c['boards'] = array();
        foreach ($boards as $b) {
            if ($b['category'] == $c['id'] && !in_array($b['name'], Config::$INVISIBLE_BOARDS)) {
                array_push($c['boards'], $b);
            }
        }
    }

    if (isset($_REQUEST['log_view'])) {
        $kargv = $_REQUEST['log_view'];
        $records_count = kotoba_intval($kargv['records_count']);
        $records_count = $records_count > 0 ? $records_count : 10;
        $smarty->assign('records_count_prev', $records_count);

        date_default_timezone_set(Config::DEFAULT_TIMEZONE);
        $logf = fopen(Config::ABS_PATH . '/log/actions-' . date(Config::LOG_DATETIME_FORMAT) . '.log', 'r');
        $i = 0;
        while (($line = fgets($logf))) {
            $log[$i] = preg_split('/\|/', $line, -1, PREG_SPLIT_NO_EMPTY);
            $log[$i][4] = htmlentities($log[$i][4], ENT_QUOTES, Config::MB_ENCODING);
            $i++;
        }

        if ( ($n = count($log)) > $records_count) {
            $log = array_slice($log, $n - $records_count, $records_count);
        }
        $log = array_reverse($log);

        $smarty->assign('log', $log);
    } else {
        $smarty->assign('records_count_prev', 10);
    }

    // Generate html code of log view page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('categories', $categories);
    $smarty->assign('boards', $boards);
    $smarty->display('log_view.tpl');

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
