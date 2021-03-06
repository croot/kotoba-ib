<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit word filter.

require_once dirname(dirname(__FILE__)) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/logging.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH
                . "/locale/{$_SESSION['language']}/messages.php";
        require Config::ABS_PATH
                . "/locale/{$_SESSION['language']}/logging.php";
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

    // Check permission and write message to log file.
    if (!is_admin()) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new NotAdminError());
        exit(1);
    }
    call_user_func(Logging::$f['EDIT_WORDFILTER_USE']);

    $boards = boards_get_all();
    $words = words_get_all();
    $reload_words = false;
    if (isset($_POST['submited'])) {

        // Add new word.
        if(isset($_POST['new_word'])
                && isset($_POST['new_replace'])
                && isset($_POST['new_bind_board'])
                && $_POST['new_bind_board'] !== ''
                && $_POST['new_word'] != ''
                && $_POST['new_replace'] != '') {

            $new_word = words_check_word($_POST['new_word']);
            if ($new_word === FALSE) {

                // Cleanup.
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            $new_replace = words_check_word($_POST['new_replace']);
            if ($new_replace === FALSE) {

                // Cleanup.
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }

            // If word already exist change it and replacement. If not - add.
            $found = false;
            foreach ($boards as $board) {
                foreach ($words as $word) {
                    if($board['id'] == $word['board_id'] && $word['word'] == $new_word) {
                        words_edit($word['id'], $new_word, $new_replace);
                        $reload_words = true;
                        break;
                    }
                }
            }
            if (!$found) {
                words_add(boards_check_id($_POST['new_bind_board']), $new_word, $new_replace);
                $reload_words = true;
            }
        }

        // Change attributes of existed words.
        foreach ($words as $word) {

            // Word was changed.
            $param_name = "word_{$word['id']}";
            $new_word = $word['id'];
            if (isset($_POST[$param_name]) && $_POST[$param_name] != $word['id']) {
                if ($_POST[$param_name] == '') {
                    $new_word = null;
                } else {
                    $new_word = words_check_word($_POST[$param_name]);
                    if ($new_word === FALSE) {

                        // Cleanup.
                        DataExchange::releaseResources();
                        Logging::close_log();

                        display_error_page($smarty, kotoba_last_error());
                        exit(1);
                    }
                }
            }

            // Replacement was changes.
            $param_name = "replace_{$word['id']}";
            $new_replace = $word['replace'];
            if (isset($_POST[$param_name]) && $_POST[$param_name] != $word['replace']) {
                if ($_POST[$param_name] == '') {
                    $new_replace = null;
                } else {
                    $new_replace = words_check_word($_POST[$param_name]);
                    if ($new_replace === FALSE) {

                        // Cleanup.
                        DataExchange::releaseResources();
                        Logging::close_log();

                        display_error_page($smarty, kotoba_last_error());
                        exit(1);
                    }
                }
            }

            // Any changes?
            if ($new_word != $word['id'] || $new_replace != $word['replace']) {
                words_edit($word['id'], $new_word, $new_replace);
                $reload_words = true;
            }
        }

        // Delete selected words.
        foreach ($words as $word) {
            if (isset($_POST["delete_{$word['id']}"])) {
                words_delete($word['id']);
                $reload_words = true;
            }
        }
    }

    if ($reload_words) {
        $words = words_get_all();
    }

    // Display edit wordfilter page.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', $boards);
    $smarty->assign('words', $words);
    $smarty->display('edit_words.tpl');

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
