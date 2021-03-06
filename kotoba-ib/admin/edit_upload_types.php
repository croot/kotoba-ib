<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Edit upload types.

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
    call_user_func(Logging::$f['EDIT_UPLOAD_TYPES_USE']);

    $upload_handlers = upload_handlers_get_all();
    $upload_types = upload_types_get_all();
    $reload_upload_types = false;

    if (isset($_POST['submited'])) {

        // Add upload type.
        if (isset($_POST['new_extension'])
                && isset($_POST['new_store_extension'])
                && isset($_POST['new_upload_handler'])
                && isset($_POST['new_thumbnail_image'])
                && $_POST['new_extension'] !== ''
                && $_POST['new_store_extension'] !== ''
                && $_POST['new_upload_handler'] !== '') {

            $new_extension = upload_types_check_extension(
                $_POST['new_extension']
            );
            if ($new_extension === FALSE) {

                // Cleanup.
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            $new_store_extension = upload_types_check_store_extension(
                $_POST['new_store_extension']
            );
            if ($new_store_extension === FALSE) {

                // Cleanup.
                DataExchange::releaseResources();
                Logging::close_log();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            $new_is_image = isset($_POST['new_is_image']) ? 1 : 0;
            $new_upload_handler_id = upload_handlers_check_id($_POST['new_upload_handler']);
            if ($_POST['new_thumbnail_image'] === '') {
                $new_thumbnail_image = null;
            } else {
                $new_thumbnail_image = upload_types_check_thumbnail_image(
                    $_POST['new_thumbnail_image']
                );
                if ($new_thumbnail_image === FALSE) {

                    // Cleanup.
                    DataExchange::releaseResources();
                    Logging::close_log();

                    display_error_page($smarty, kotoba_last_error());
                    exit(1);
                }
            }

            // Change or add upload type.
            $found = false;
            foreach ($upload_types as $upload_type) {
                if ($upload_type['extension'] == $new_extension && $found = true) {
                    upload_types_edit(
                        $upload_type['id'],
                        $new_store_extension,
                        $new_is_image,
                        $new_upload_handler_id,
                        $new_thumbnail_image
                    );
                    $reload_upload_types = true;
                    break;
                }
            }
            if (!$found) {
                upload_types_add(
                    $new_extension,
                    $new_store_extension,
                    $new_is_image,
                    $new_upload_handler_id,
                    $new_thumbnail_image
                );
                $reload_upload_types = true;
            }
        }

        // Change upload type.
        foreach ($upload_types as $upload_type) {

            // Is stored extension changes?
            $param_name = 'store_extension_' . $upload_type['id'];
            $new_store_extension = $upload_type['store_extension'];
            if (isset($_POST[$param_name]) && ($_POST[$param_name] != $upload_type['store_extension'])) {
                $new_store_extension = upload_types_check_store_extension(
                    $_POST[$param_name]
                );
                if ($new_store_extension === FALSE) {

                    // Cleanup.
                    DataExchange::releaseResources();
                    Logging::close_log();

                    display_error_page($smarty, kotoba_last_error());
                    exit(1);
                }
            }

            // Is image flag changes?
            $param_name = 'is_image_' . $upload_type['id'];
            $new_is_image = $upload_type['is_image'];
            if (isset($_POST[$param_name]) && ($_POST[$param_name] != $upload_type['is_image'])) {
                $new_is_image = TRUE;
            }
            if (!isset($_POST[$param_name]) && $upload_type['is_image']) {
                $new_is_image = FALSE;
            }

            // Is upload handler changes?
            $param_name = 'upload_handler_' . $upload_type['id'];
            $new_upload_handler_id = $upload_type['upload_handler'];
            if (isset($_POST[$param_name]) && ($_POST[$param_name] != $upload_type['upload_handler'])) {
                $new_upload_handler_id = upload_handlers_check_id($_POST[$param_name]);
            }

            // Is thumbnail changes?
            $param_name = 'thumbnail_image_' . $upload_type['id'];
            $new_thumbnail_image = $upload_type['thumbnail_image'];
            if (isset($_POST[$param_name]) && ($_POST[$param_name] != $upload_type['thumbnail_image'])) {
                if ($_POST[$param_name] === '') {
                    $new_thumbnail_image = null;
                } else {
                    $new_thumbnail_image = upload_types_check_thumbnail_image(
                        $_POST[$param_name]
                    );
                    if ($new_thumbnail_image === FALSE) {

                        // Cleanup.
                        DataExchange::releaseResources();
                        Logging::close_log();

                        display_error_page($smarty, kotoba_last_error());
                        exit(1);
                    }
                }
            }

            // Is something changes?
            if($new_store_extension != $upload_type['store_extension']
                    || $new_is_image != $upload_type['is_image']
                    || $new_upload_handler_id != $upload_type['upload_handler']
                    || $new_thumbnail_image != $upload_type['thumbnail_image']) {

                upload_types_edit($upload_type['id'], $new_store_extension,
                $new_is_image, $new_upload_handler_id, $new_thumbnail_image);
                $reload_upload_types = true;
            }
        }

        // Delete upload types.
        foreach ($upload_types as $upload_type) {
            if (isset($_POST['delete_' . $upload_type['id']])) {
                upload_types_delete($upload_type['id']);
                $reload_upload_types = true;
            }
        }
    }

    if ($reload_upload_types) {
        $upload_types = upload_types_get_all();
    }

    // Generate html code of edit upload types page and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_visible($_SESSION['user']));
    $smarty->assign('upload_handlers', $upload_handlers);
    $smarty->assign('upload_types', $upload_types);
    $smarty->display('edit_upload_types.tpl');

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