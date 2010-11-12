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

// Скрипт редактирования списка контроля доступа.

require '../config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/logging.php';
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

    // Check permission and write message to log file.
    if (!is_admin()) {
        throw new PermissionException(PermissionException::$messages['NOT_ADMIN']);
    }
    call_user_func(Logging::$f['EDIT_ACL_USE']);

    $groups = groups_get_all();
    $boards = boards_get_all();
    $acl = acl_get_all();
    $reload_acl = false;    // Были ли произведены изменения.

    if (isset($_POST['submited'])) {

        // Добавление записи.
        if ((isset($_POST['new_group']) && isset($_POST['new_board'])
                && isset($_POST['new_thread']) && isset($_POST['new_post']))
                && ( $_POST['new_group'] !== '' || $_POST['new_board'] !== ''
                || $_POST['new_thread'] !== '' || $_POST['new_post'] !== '')) {

            $new_group = ($_POST['new_group'] === '') ? null : groups_check_id($_POST['new_group']);
            $new_board = ($_POST['new_board'] === '') ? null : boards_check_id($_POST['new_board']);
            $new_thread = ($_POST['new_thread'] === '') ? null : threads_check_id($_POST['new_thread']);
            $new_post = ($_POST['new_post'] === '') ? null : posts_check_id($_POST['new_post']);
            $new_view = (isset($_POST['new_view'])) ? 1 : 0;
            $new_change = (isset($_POST['new_change'])) ? 1 : 0;
            $new_moderate = (isset($_POST['new_moderate'])) ? 1 : 0;

            /*
             * Идентификатор доски, нити или сообщения в правилах однозначно
             * определяют доску, нить и сообщение соотвественно.
             */
            if ($new_board !== null && ($new_thread !== null || $new_post !== null)) {
                throw new CommonException(CommonException::$messages['ACL_RULE_EXCESS']);
            }
            if ($new_thread !== null && ($new_board !== null || $new_post !== null)) {
                throw new CommonException(CommonException::$messages['ACL_RULE_EXCESS']);
            }
            if ($new_post != null && ($new_board !== null || $new_thread !== null)) {
                throw new CommonException(CommonException::$messages['ACL_RULE_EXCESS']);
            }

            /*
             * Если запрещен просмотр, то редактирование и модерирование не
             * имеют смысла. Если запрещено редактирование, то модерирование не
             * имеет смысла.
             */
            if ($new_view == 0 && ($new_change != 0 || $new_moderate != 0)) {
                throw new CommonException(CommonException::$messages['ACL_RULE_CONFLICT']);
            } elseif($new_change == 0 && $new_moderate != 0) {
                throw new CommonException(CommonException::$messages['ACL_RULE_CONFLICT']);
            }

            /*
             * Поищем, нет ли уже такого правила. Если есть, то надо только
             * изменить существующее.
             */
            $found = false;
            foreach ($acl as $record) {
                if (	(($record['group'] === null && $new_group === null) || ($record['group'] == $new_group)) &&
                        (($record['board'] === null && $new_board === null) || ($record['board'] == $new_board)) &&
                        (($record['thread'] === null && $new_thread === null) || ($record['thread'] == $new_thread)) &&
                        (($record['post'] === null && $new_post === null) || ($record['post'] == $new_post))) {

                    acl_edit($new_group, $new_board, $new_thread, $new_post,
                    $new_view, $new_change, $new_moderate);
                    $reload_acl = true;
                    $found = true;
                }
            }
            if (!$found) {
                acl_add($new_group, $new_board, $new_thread, $new_post,
                $new_view, $new_change, $new_moderate);
                $reload_acl = true;
            }
        }// Добавление записи.

        // Изменение записей.
        foreach($acl as $record) {

            // Изменили право просмотра.
            if ($record['view'] == 1 && !isset($_POST["view_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // Сняли право просмотра.
                acl_edit($record['group'], $record['board'], $record['thread'],
                $record['post'], 0, 0, 0);
                $reload_acl = true;
                continue;
            }
            if ($record['view'] == 0 && isset($_POST["view_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                /*
                 * Добавили право просмотра. Проверим, не было ли добавлено
                 * других прав.
                 */
                if ($record['change'] == 0 && isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                    /*
                     * Добавили право редактирования. Проверим, не было ли
                     * добавлено права модерирования.
                     */
                    if ($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                        // Добавили право модерирования.
                        acl_edit($record['group'], $record['board'],
                        $record['thread'], $record['post'], 1, 1, 1);
                        $reload_acl = true;
                        continue;
                    } else {
                        acl_edit($record['group'], $record['board'],
                        $record['thread'], $record['post'], 1, 1, 0);
                        $reload_acl = true;
                        continue;
                    }
                } else {

                    /*
                     * Поскольку без права редактирования право модерирования не
                     * имеет смысла, то и проверять, добавили его или нет, не
                     * нужно.
                     */
                    acl_edit($record['group'], $record['board'],
                    $record['thread'], $record['post'], 1, 0, 0);
                    $reload_acl = true;
                    continue;
                }
            }// Добавили право просмотра.

            // Право просмотра не меняли.
            if ($record['change'] == 1 && !isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // Сняли право редактирования.
                acl_edit($record['group'], $record['board'], $record['thread'],
                $record['post'], $record['view'], 0, 0);
                $reload_acl = true;
                continue;
            }
            if ($record['change'] == 0 && isset($_POST["change_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {
                // Добавили право редактирования.
                if ($record['view'] == 0) {

                    /*
                     * Если права просмотра не было, то игнориуем добавление
                     * права просмотра и переходим к следующей записи.
                     */
                    continue;
                } else {

                    // Проверим, не было ли добавлено права модерирования.
                    if ($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                        // Добавили  право модерирования.
                        acl_edit($record['group'], $record['board'],
                        $record['thread'], $record['post'], 1, 1, 1);
                        $reload_acl = true;
                        continue;
                    } else {
                        acl_edit($record['group'], $record['board'],
                        $record['thread'], $record['post'], 1, 1, 0);
                        $reload_acl = true;
                        continue;
                    }
                }
            }// Добавили право редактирования.

            // Право просмотра и редактирования не меняли.
            if ($record['moderate'] == 1 && !isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // Сняли право модерирования.
                acl_edit($record['group'], $record['board'], $record['thread'],
                $record['post'], $record['view'], $record['change'], 0);
                $reload_acl = true;
                continue;
            }
            if ($record['moderate'] == 0 && isset($_POST["moderate_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {

                // Добавили право модерирования.
                acl_edit($record['group'], $record['board'], $record['thread'],
                $record['post'], $record['view'], $record['change'], 1);
                $reload_acl = true;
                continue;
            }
        }// Изменение записей.

        // Удаление записей из списка контроля доступа.
        foreach ($acl as $record) {
            if (isset($_POST["delete_{$record['group']}_{$record['board']}_{$record['thread']}_{$record['post']}"])) {
                acl_delete($record['group'], $record['board'],
                $record['thread'], $record['post']);
                $reload_acl = true;
            }
        }
    }

    // Вывод формы редактирования.
    if ($reload_acl) {
        $acl = acl_get_all();
    }
    $smarty->assign('groups', $groups);
    $smarty->assign('boards', $boards);
    $smarty->assign('acl', $acl);
    $smarty->display('edit_acl.tpl');

    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>