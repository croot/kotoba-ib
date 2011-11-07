<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

// Update macrochan data from database.

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
    call_user_func(Logging::$f['UPDATE_MACROCHAN_USE']);

    // Download data.
    include Config::ABS_PATH . '/res/macrochan_data.php';

    // Remove tags what not exist anymore.
    $tags_removed = 0;
    $tag_names = kotoba_array_column($MACROCHAN_TAGS, 1);
    foreach (macrochan_tags_get_all() as $tag) {
        if (!in_array($tag['name'], $tag_names)) {
            macrochan_tags_delete_by_name($tag['name']);
            $tags_removed++;
        }
    }
    echo "Tags removed: $tags_removed<br>\n";

    // Add tags what we havent.
    $tags_added = 0;
    $tag_names = kotoba_array_column(macrochan_tags_get_all(), 1);
    foreach ($MACROCHAN_TAGS as $tag) {
        if (!in_array($tag[1], $tag_names)) {
            macrochan_tags_add($tag[1]);
            $tags_added++;
        }
    }
    echo "Tags added: $tags_added<br>\n";

    // Remove images what not exist anymore.
    $images_removed = 0;
    $image_names = kotoba_array_column($MACROCHAN_IMAGES, 1);
    foreach (macrochan_images_get_all() as $image) {
        if (!in_array($image['name'], $image_names)) {
            macrochan_images_delete_by_name($image['name']);
            $images_removed++;
        }
    }
    echo "Images removed: $images_removed<br>\n";

    // Add macrochan images what we havent.
    $images_added = 0;
    $image_names = kotoba_array_column(macrochan_images_get_all(), 1);
    foreach ($MACROCHAN_IMAGES as $image) {
        if (!in_array($image[1], $image_names)) {
            macrochan_images_add($image[1],
                                 $image[2],
                                 $image[3],
                                 $image[4],
                                 $image[5],
                                 $image[6],
                                 $image[7]);
            $images_added++;
        }
    }
    echo "Images added: $images_added<br>\n";

    // Add new tags images relations.
    $relations_added = 0;
    foreach ($MACROCHAN_TAGS_IMAGES as $ti) {

        // Find tag name.
        foreach ($MACROCHAN_TAGS as $tag) {
            if ($ti[0] == $tag[0]) {

                // Find image name.
                foreach ($MACROCHAN_IMAGES as $image) {
                    if ($ti[1] == $image[0]) {

                        // If we havent such relation add it.
                        if (macrochan_tags_images_get($tag[1], $image[1]) === null) {
                            macrochan_tags_images_add($tag[1], $image[1]);
                            $relations_added++;
                        }
                    }
                }
            }
        }
    }
    echo "Tag Image relations added: $relations_added<br>\n";

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