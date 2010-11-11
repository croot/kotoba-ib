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

// Скрипт редактирования блокировок.

require '../config.php';
require Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
require Config::ABS_PATH . '/lib/db.php';
require Config::ABS_PATH . '/lib/misc.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

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

    if (!is_admin() && !is_mod()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']
                . ' ' . PermissionException::$messages['NOT_MOD']);
    }
    $func = Logging::$f['EDIT_BANS'];
    Logging::close_log();

    $bans = bans_get_all();
    date_default_timezone_set(Config::DEFAULT_TIMEZONE);
    $bans_decoded = array();
    foreach ($bans as $ban) {
        array_push($bans_decoded, array('id' => $ban['id'],
            'range_beg' => long2ip($ban['range_beg']),
            'range_end' => long2ip($ban['range_end']),
            'reason' => $ban['reason'],
            'untill' => $ban['untill']));
    }

    $reload_bans = false; // Были ли произведены изменения.
    if (isset($_POST['submit'])) {

        // Добавление нового бана.
        if (isset($_POST['new_range_beg']) && isset($_POST['new_range_end'])
                && isset($_POST['new_reason']) && isset($_POST['new_untill'])
                && $_POST['new_range_beg'] != ''
                && $_POST['new_range_end'] != ''
                && $_POST['new_untill'] != '') {
            $new_range_beg = bans_check_range_beg($_POST['new_range_beg']);
            $new_range_end = bans_check_range_end($_POST['new_range_end']);
            if ($_POST['new_reason'] === '') {
                $new_reason = null;
            }
            else {
                $new_reason = bans_check_reason($_POST['new_reason']);
            }
            $new_untill = bans_check_untill($_POST['new_untill']);
            bans_add($new_range_beg, $new_range_end, $new_reason,
                    date('Y-m-d H:i:s', time() + $new_untill));
            $reload_bans = true;
            if (isset($_POST['post'])) {
                if (isset($_POST['add_text'])) {
                    posts_add_text_by_id(posts_check_id($_POST['post']) , $smarty->fetch('uwb4tp.tpl'));
                } elseif (isset($_POST['del_post'])) {
                    posts_delete(posts_check_id($_POST['post']));
                } elseif (isset($_POST['del_all'])) {
                    date_default_timezone_set(Config::DEFAULT_TIMEZONE);
                    posts_delete_last(posts_check_id($_POST['post']), date(Config::DATETIME_FORMAT, time() - (60 * 60)));
                }
            }
        }

        // Удаление банов.
        foreach ($bans as $ban) {
            if (isset($_POST['delete_' . $ban['id']])) {
                bans_delete_by_id($ban['id']);
                $reload_bans = true;
            }
        }

        // Разбан заданного ip.
        if (isset($_POST['unban']) && $_POST['unban'] !== '') {

            // Так как начало и конец диапазона такие же ip адреса как и все.
            $ip = bans_check_range_beg($_POST['unban']);
            bans_delete_byip($ip);
            $reload_bans = true;
        }
    }

    // Вывод формы редактирования.
    if ($reload_bans) {
        $bans = bans_get_all();
    }
    $bans_decoded = array();
    foreach ($bans as $ban) {
        array_push($bans_decoded, array('id' => $ban['id'],
            'range_beg' => long2ip($ban['range_beg']),
            'range_end' => long2ip($ban['range_end']),
            'reason' => $ban['reason'],
            'untill' => $ban['untill']));
    }

    DataExchange::releaseResources();
    $smarty->assign('bans_decoded', $bans_decoded);
    $smarty->display('edit_bans.tpl');
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
