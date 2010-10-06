<?php
require_once '../config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';

try {
    // Initialization.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    // Check if remote host was banned.
    if (($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
        throw new CommonException(CommonException::$messages['REMOTE_ADDR']);
    }
    if (($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    // Check parameters.
    if (!isset($_GET['file']) || !ctype_digit($_GET['file'])) {
        throw new Exception('Bad file passed.');
    }
    if (!isset($_GET['time']) || !ctype_digit($_GET['time'])) {
        throw new Exception('Bad time passed.');
    }
    if ($_GET['painter'] !== 'shi_normal' && $_GET['painter'] !== 'shi_pro') {
        throw new Exception('Bad painter passed.');
    }

    $_SESSION['oekaki']['file'] = "{$_GET['file']}.png";
    $_SESSION['oekaki']['thumbnail'] = "{$_GET['file']}t.png";
    $_SESSION['oekaki']['ip'] = $ip;
    $_SESSION['oekaki']['time'] = $_GET['time'];
    $_SESSION['oekaki']['painter'] = $_GET['painter'];

    if (isset($_GET['thread']) && $_GET['thread'] != '' && isset($_GET['board']) && $_GET['board'] != '') {
        $board_name = boards_check_name($_GET['board']);
        $thread_original_post = threads_check_original_post($_GET['thread']);
        header('Location: ' . Config::DIR_PATH . "/$board_name/$thread_original_post/");
    } else if(isset($_GET['board']) && $_GET['board'] != '') {
        $board_name = boards_check_name($_GET['board']);
        header('Location: ' . Config::DIR_PATH . "/$board_name/");
    }

    // Clean up.
    DataExchange::releaseResources();

    exit(0);
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>