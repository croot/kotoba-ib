<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit boards script.

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
    call_user_func(Logging::$f['EDIT_BOARDS_USE']);

    $popdown_handlers = popdown_handlers_get_all();
    $categories = categories_get_all();
    $boards = boards_get_all();
    $reload_boards = false;

    // Make category-boards tree for navigation panel.
    foreach ($categories as &$c) {
        $c['boards'] = array();
        foreach ($boards as $b) {
            if ($b['category'] == $c['id'] && !in_array($b['name'], Config::$INVISIBLE_BOARDS)) {
                array_push($c['boards'], $b);
            }
        }
    }

    if (isset($_POST['submited'])) {
        $new_board = array();

        // New board creation.
		if (isset($_POST['new_name'])
                && isset($_POST['new_title'])
                && isset($_POST['new_annotation'])
                && isset($_POST['new_bump_limit'])
                && isset($_POST['new_default_name'])
                && isset($_POST['new_enable_macro'])
                && isset($_POST['new_enable_youtube'])
                && isset($_POST['new_enable_captcha'])
                && isset($_POST['new_enable_translation'])
                && isset($_POST['new_enable_geoip'])
                && isset($_POST['new_enable_shi'])
                && isset($_POST['new_enable_postid'])
                && isset($_POST['new_same_upload'])
                && isset($_POST['new_popdown_handler'])
                && isset($_POST['new_category'])
                && $_POST['new_name'] != ''
                && $_POST['new_bump_limit'] != ''
                && $_POST['new_same_upload'] != ''
                && $_POST['new_popdown_handler'] != ''
                && $_POST['new_category'] != '') {

            // Check parameters.
            $new_board['name'] = boards_check_name($_POST['new_name']);
            $new_board['title'] = boards_check_title($_POST['new_title']);
            $new_board['annotation'] = boards_check_annotation($_POST['new_annotation']);
            $new_board['bump_limit'] = boards_check_bump_limit($_POST['new_bump_limit']);
            $new_board['force_anonymous'] = isset($_POST['new_force_anonymous']) ? true : false;
            $new_board['default_name'] = boards_check_default_name($_POST['new_default_name']);
            $new_board['with_attachments'] = isset($_POST['new_with_attachments']) ? true : false;
            foreach (array('enable_macro',
                           'enable_youtube',
                           'enable_captcha',
                           'enable_translation',
                           'enable_geoip',
                           'enable_shi',
                           'enable_postid') as $param_name) {

                if ($_POST["new_$param_name"] == '2') {
                    $new_board[$param_name] = null;
                } else {
                    $new_board[$param_name] = $_POST["new_$param_name"] ? true : false;
                }
            }
            $new_board['same_upload'] = boards_check_same_upload($_POST['new_same_upload']);
            $new_board['popdown_handler'] = popdown_handlers_check_id($_POST['new_popdown_handler']);
            $new_board['category'] =  categories_check_id($_POST['new_category']);

            // If board with that name already exists, change parameters of this board.
            $found = false;
            foreach ($boards as $board) {
                if ($board['name'] == $new_board['name'] && $found = true) {
                    $new_board['id'] = $board['id'];
                    //echo "edit {$board['name']}<br>\n";
                    boards_edit($new_board);
                    $reload_boards = true;
                    break;
                }
            }
            // If not exits, create new one.
            if (!$found) {
                //echo "add {$new_board['name']}<br>\n";
                boards_add($new_board);
                if (!create_directories($new_board['name'])) {
                    throw new CommonException('Directories creation failed.');
                }
                $reload_boards = true;
            }
        }// New board creation.

        // Change parameters of existing boards.
		foreach ($boards as $board) {
            $changed = false;

            // Is title was changed?
            if (isset($_POST["title_{$board['id']}"])) {
                $new_board['title'] = boards_check_title($_POST["title_{$board['id']}"]);
                if (($new_board['title'] === null xor $board['title'] === null)
                        || $new_board['title'] != $board['title']) {

                    $changed = true;
                    //echo "title changed<br>\n";
                }
            }

            // Is annotation was changed?
            if (isset($_POST["annotation_{$board['id']}"])) {
                $new_board['annotation'] = boards_check_annotation($_POST["annotation_{$board['id']}"]);
                if (($new_board['annotation'] === null xor $board['annotation'] === null)
                        || $new_board['annotation'] != $board['annotation']) {

                    $changed = true;
                    //echo "annotation changed<br>\n";
                }
            }

            // Is board-specified bump limit was changed?
            if (isset($_POST["bump_limit_{$board['id']}"])) {
				$new_board['bump_limit'] = boards_check_bump_limit($_POST["bump_limit_{$board['id']}"]);
                if ($new_board['bump_limit'] != $board['bump_limit']) {
                    $changed = true;
                    //echo "bump_limit changed<br>\n";
                }
            }

			// Is display poster name flag was changed?
			$param_name = "force_anonymous_{$board['id']}";
			$new_board['force_anonymous'] = $board['force_anonymous'];
			if (isset($_POST[$param_name]) && $_POST[$param_name] != $board['force_anonymous']) {
				// Flag up 0 -> 1
				$new_board['force_anonymous'] = true;
                $changed = true;
                //echo "force_anonymous up<br>\n";
			}
			if (!isset($_POST[$param_name]) && $board['force_anonymous']) {
				// Flag down 1 -> 0
				$new_board['force_anonymous'] = false;
                $changed = true;
                //echo "force_anonymous down<br>\n";
			}

			// Is default poster name was changed?
            if (isset($_POST["default_name_{$board['id']}"])) {
                $new_board['default_name'] = boards_check_default_name($_POST["default_name_{$board['id']}"]);
                if (($new_board['default_name'] === null xor $board['default_name'] === null)
                        || $new_board['default_name'] != $board['default_name']) {

                    $changed = true;
                    //echo "default_name changed<br>\n";
                }
            }

			// Is attachments flag was changed?
			$param_name = "with_attachments_{$board['id']}";
			$new_board['with_attachments'] = $board['with_attachments'];
			if (isset($_POST[$param_name]) && $_POST[$param_name] != $board['with_attachments']) {
				// Flag up 0 -> 1
				$new_board['with_attachments'] = true;
                $changed = true;
                //echo "with_attachments up<br>\n";
			}
			if (!isset($_POST[$param_name]) && $board['with_attachments']) {
				// Flag down 1 -> 0
				$new_board['with_attachments'] = false;
                $changed = true;
                //echo "with_attachments down<br>\n";
			}

            foreach (array('enable_macro',
                           'enable_youtube',
                           'enable_captcha',
                           'enable_translation',
                           'enable_geoip',
                           'enable_shi',
                           'enable_postid') as $attr) {

                if ($_POST["{$attr}_{$board['id']}"] == '2') {
                    $new_board[$attr] = null;
                    if ($board[$attr] !== null) {
                        $changed = true;
                        //echo "$attr inherit<br>\n";
                    }
                } else {
                    $new_board[$attr] = $_POST["{$attr}_{$board['id']}"] ? true : false;
                    if ($board[$attr] === null || kotoba_intval($board[$attr]) != $new_board[$attr]) {
                        $changed = true;
                        //echo "$attr set to " . ($new_board[$attr] ? 'true' : 'false') . "<br>\n";
                    }
                }
            }

			// Is same uploads policy was changed?
            if (isset($_POST["same_upload_{$board['id']}"])) {
                $new_board['same_upload'] = boards_check_same_upload($_POST["same_upload_{$board['id']}"]);
                if ($new_board['same_upload'] != $board['same_upload']) {
                    $changed = true;
                    //echo "same_upload changed<br>\n";
                }
            }

			// Is threads popdown handler was changed?
            if (isset($_POST["popdown_handler_{$board['id']}"])) {
                $new_board['popdown_handler'] = popdown_handlers_check_id($_POST["popdown_handler_{$board['id']}"]);
                if ($new_board['popdown_handler'] != $board['popdown_handler']) {
                    $changed = true;
                    //echo "popdown_handler changed<br>\n";
                }
            }

			// Is board category was changed?
            if (isset($_POST["category_{$board['id']}"])) {
                $new_board['category'] = categories_check_id($_POST["category_{$board['id']}"]);
                if ($new_board['category'] != $board['category']) {
                    $changed = true;
                    //echo "category changed<br>\n";
                }
            }

			// Is something was changed?
			if ($changed) {
                $new_board['id'] = $board['id'];
                //echo "edit {$board['name']}<br>\n";
                boards_edit($new_board);
				$reload_boards = true;
			}
		}// Change parameters of existing boards.

        // Delete selected boards.
        foreach ($boards as $board) {
            if (isset($_POST["delete_{$board['id']}"])) {
                //echo "delete {$board['name']}<br>\n";
                boards_delete($board['id']);
                $reload_boards = true;
            }
        }
	}

    if ($reload_boards) {
        $boards = boards_get_all();
    }

    // Show edit form.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('popdown_handlers', $popdown_handlers);
    $smarty->assign('categories', $categories);
    $smarty->assign('boards', $boards);
    $smarty->display('edit_boards.tpl');

    // Cleanup.
    DataExchange::releaseResources();
    Logging::close_log();

    exit(0);
} catch(Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    die($smarty->fetch('exception.tpl'));
}
?>