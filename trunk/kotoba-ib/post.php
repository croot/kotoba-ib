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

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/wrappers.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH
            . "/locale/{$_SESSION['language']}/messages.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ban = bans_check(get_remote_addr())) !== FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        $smarty->display('banned.tpl');

        session_destroy();
        exit(1);
    }

    // Check for requied parameters.
    foreach (array('board', 'thread', 'post') as $param) {
        if (!isset($_REQUEST[$param])) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, new RequiedParamError($param));
            exit(1);
        }
    }

    // Check board name.
    $board_name = boards_check_name($_REQUEST['board']);
    if ($board_name === FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }

    // Get post, board, thread and attachment.
    $post = posts_get_visible_by_number($board_name,
                                        posts_check_number($_REQUEST['post']),
                                        $_SESSION['user']);
    $board = $post['board'];
    $thread = $post['thread'];
    $posts_attachments = array();
    $attachments = array();
    if (is_attachments_enabled($board) || $thread['with_attachments']) {
        $posts_attachments = posts_attachments_get_by_posts(array($post));
        $attachments = attachments_get_by_posts(array($post));
    }

    // Find if author of this post is admin.
    $author_admin = posts_is_author_admin($post['user']);

    //
    // Create html-code and display it.
    //

    $smarty->assign('ATTACHMENT_TYPE_FILE', Config::ATTACHMENT_TYPE_FILE);
    $smarty->assign('ATTACHMENT_TYPE_LINK', Config::ATTACHMENT_TYPE_LINK);
    $smarty->assign('ATTACHMENT_TYPE_VIDEO', Config::ATTACHMENT_TYPE_VIDEO);
    $smarty->assign('ATTACHMENT_TYPE_IMAGE', Config::ATTACHMENT_TYPE_IMAGE);
    $smarty->assign('page_title', NULL);
    $post_html = $smarty->fetch('header.tpl');
    // Set default post author name if enabled.
    if (!$board['force_anonymous'] && $board['default_name']
            && !$post['name']) {

        $post['name'] = $board['default_name'];
    }
    // Requied for simple and original post templates :(
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
    echo $post_html . $smarty->fetch('footer.tpl');
    
    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch (KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    if (!isset($smarty)) {
        $smarty = new SmartyKotobaSetup();
    }
    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
