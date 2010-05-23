<?php
/*
 * Use it only if in your database annotation stored as html code. Copy this
 * file to Kotoba root directory and execute via browser. Do not forget remove
 * it after apply.
 */
require "config.php";
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';

$boards = boards_get_all();

foreach ($boards as $board) {
    boards_edit($board['id'], $board['title'],
            htmlentities($board['annotation'], ENT_QUOTES, Config::MB_ENCODING),
            $board['bump_limit'], $board['force_anonymous'],
            $board['default_name'], $board['with_attachments'],
            $board['enable_macro'], $board['enable_youtube'],
            $board['enable_captcha'], $board['same_upload'],
            $board['popdown_handler'], $board['category']);
}
echo 'Done.';
?>