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

/*
 * Скрипт просмотра сообщения. Скрипт принимает три парамета, которые передаются
 * с помощью POST или GET запроса:
 * board - Имя доски.
 * thread - Номер нити.
 * post - Номер сообщения.
 */

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

try {
    // Инициализация.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    // Проверка, не заблокирован ли клиент.
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

    // Получение данных о доске, нити, сообщении и вложениях и т.д.

    // Фильтр выбирает доску с заданным именем.
    $bfilter = function ($board, $name) {
        if ($board['name'] == $name) {
            return true;
        } else {
            return false;
        }
    };
    $boards = boards_get_visible_filtred($_SESSION['user'], $bfilter, boards_check_name($_GET['board']));
    $board = $boards[0];

    // Фильтр выбирает нить с заданным идентификатором.
    $tfilter = function ($thread, $id) {
        if ($thread['id'] == $id) {
            return true;
        } else {
            return false;
        }
    };
    $threads = threads_get_visible_filtred_by_board($board['id'], $_SESSION['user'], $tfilter, threads_check_original_post($_GET['thread']));
    $thread = $threads[0];

    $post = posts_get_visible_by_number($board['id'], posts_check_number($_GET['post']), $_SESSION['user']);

    $posts_attachments = array();
    $attachments = array();
    if ($thread['with_attachments'] || ($thread['with_attachments'] === null && $board['with_attachments'])) {
        $posts_attachments = posts_attachments_get_by_posts(array($post));
        $attachments = attachments_get_by_posts(array($post));
    }

    // Автор является администратором?
    $author_admin = false;
    foreach (users_get_admins() as $admin) {
        if ($post['user'] == $admin['id']) {
            $author_admin = true;
            break;
        }
    }

    // Формирование кода заголовка сообщения.

    $smarty->assign('board', $board);
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    $post_html = $smarty->fetch('header.tpl');

    // Формирование кода сообщения.

    $smarty->assign('thread', $thread);

    // Имя отправителя по умолчанию.
    if (!$board['force_anonymous'] && $board['default_name'] && !$post['name']) {
        $post['name'] = $board['default_name'];
    }
    if ($thread['original_post'] == $post['number']) {

        // Оригинальное сообщение.
        $post_html .= post_original_generate_html($smarty,
                $board,
                $thread,
                $post,
                $posts_attachments,
                $attachments,
                false,
                null,
                false,
                null,
                false,
                $author_admin);
    } else {

        // Ответ в нить.
        $post_html .= post_simple_generate_html($smarty,
                $board,
                $thread,
                $post,
                $posts_attachments,
                $attachments,
                false,
                null,
                $author_admin);
    }

    // Вывод кода сообщения.
    echo $post_html . $smarty->fetch('footer.tpl');
    
    // Освобождение ресурсов и очистка.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
