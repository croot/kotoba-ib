<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Post view script.
 *
 * Parameters:
 * board - Board name.
 * thread - Thread number.
 * post - Post number.
 */

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

try {
    // Инициализация.
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'], $_SESSION['stylesheet']);

    // Проверка, не заблокирован ли клиент.
    if (!isset($_SERVER['REMOTE_ADDR'])
            || ($ip = ip2long($_SERVER['REMOTE_ADDR'])) === FALSE) {

        throw new RemoteAddressException();
    }
    if (($ban = bans_check($ip)) !== false) {
        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        session_destroy();
        DataExchange::releaseResources();
        die($smarty->fetch('banned.tpl'));
    }

    $post = posts_get_visible_by_number(boards_check_name($_GET['board']), posts_check_number($_GET['post']), $_SESSION['user']);
    $board = $post['board'];
    $thread = $post['thread'];

    $posts_attachments = array();
    $attachments = array();
    if ($thread['with_attachments'] || ($thread['with_attachments'] === null && $board['with_attachments'])) {
        $posts_attachments = posts_attachments_get_by_posts(array($post));
        $attachments = attachments_get_by_posts(array($post));
    }

    // Find if author of this post is admin.
    $author_admin = false;
    foreach (users_get_admins() as $admin) {
        if ($post['user'] == $admin['id']) {
            $author_admin = true;
            break;
        }
    }

    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);

    $smarty->assign('page_title', NULL);
    $post_html = $smarty->fetch('header.tpl');

    // Set default post author name if enabled.
    if (!$board['force_anonymous'] && $board['default_name'] && !$post['name']) {
        $post['name'] = $board['default_name'];
    }
    // Requied for simple and original post templates =\
    $smarty->assign('is_board_view', FALSE);
    $smarty->assign('show_favorites', TRUE);
    // Original post or reply.
    if ($thread['original_post'] == $post['number']) {
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
                                                  $author_admin,
                                                  false,
                                                  false);
    } else {
        $post_html .= post_simple_generate_html($smarty,
                                                $board,
                                                $thread,
                                                $post,
                                                $posts_attachments,
                                                $attachments,
                                                false,
                                                null,
                                                $author_admin,
                                                false,
                                                false);
    }

    // Display post html-code.
    echo $post_html . $smarty->fetch('footer.tpl');
    
    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>
