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

// Скрипт просмотра сообщения.
// read.php?b=' + ainfo[1] + '&t=' + ainfo[2] + '&p='

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

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

    // TODO: visibility not checked!
    $board = boards_get_by_name(boards_check_name($_GET['board']));
    $thread = threads_get_by_original_post($board['id'], threads_check_original_post($_GET['thread']));
    $post = posts_get_visible_by_number($board['id'], posts_check_number($_GET['post']), $_SESSION['user']);

    // TODO: What if attachments disables on this board?
    $posts_attachments = posts_attachments_get_by_posts(array($post));
    $attachments = attachments_get_by_posts(array($post));

    $smarty->assign('board', $board);
    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);

    $post_html = $smarty->fetch('header.tpl');

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
                false);
    } else {

        // Ответ в нить.
        $post_html .= post_simple_generate_html($smarty,
                $board,
                $thread,
                $post,
                $posts_attachments,
                $attachments,
                false,
                null);
    }

    DataExchange::releaseResources();
    echo $post_html . $smarty->fetch('footer.tpl');;
    exit;
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('error.tpl'));
}
?>
