<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Reply script.
 *
 * Parameters:
 * MAX_FILE_SIZE - maximum size of uploaded file in bytes (see config.default).
 * name - name.
 * subject - subject.
 * text - text.
 * file (optional) - uploaded file.
 * spoiler (optional) - attachment is spoiler.
 * use_oekaki (optional) - use drawn picture as attachment.
 * macrochan_tag (optional) - macrochan tag name.
 * youtube_video_code (optional) - code of youtube video.
 * captcha_code (optional) - captcha code.
 * animaptcha_code (optional) - animaptcha word.
 * password - password.
 * goto - redirection.
 * sage - sage flag.
 * t - thread id.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';
require_once Config::ABS_PATH . '/lib/mark.php';
//require_once Config::ABS_PATH . '/lib/latex_render.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';

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

    // Check thread id, get thread and check if it unabled to posting.
    $thread_id = threads_check_id($_REQUEST['t']);
    $thread = threads_get_changeable_by_id($thread_id, $_SESSION['user']);
    if ($thread === FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }
    if ($thread['archived']) {

        // Cleanup
        DataExchange::releaseResources();

        display_error_page($smarty, new ThreadArchivedError($thread['id']));
        exit(1);
    }
    if ($thread['closed']) {

        // Cleanup
        DataExchange::releaseResources();

        display_error_page($smarty, new ThreadClosedError($thread['id']));
        exit(1);
    }

    $board = $thread['board'];

    // Captcha.
    if (is_captcha_enabled($board)) {
        switch (Config::CAPTCHA) {
            case 'captcha':
                if (is_captcha_valid()) {
                    // Pass! Do smth?
                } else {

                    // Cleanup.
                    DataExchange::releaseResources();

                    $_ = mb_strtolower($_REQUEST['captcha_code'],
                                       Config::MB_ENCODING);
                    display_error_page($smarty, new CaptchaError($_));
                    exit(1);
                }
                break;
            case 'animaptcha':
                if (is_animaptcha_valid()) {
                    // Pass! Do smth?
                } else {

                    // Cleanup.
                    DataExchange::releaseResources();

                    $_ = mb_strtolower($_REQUEST['animaptcha_code'],
                                       Config::MB_ENCODING);
                    display_error_page($smarty, new CaptchaError($_));
                    exit(1);
                }
                break;
            default:

                // Cleanup.
                DataExchange::releaseResources();

                $_ = 'Unknown captcha type';
                display_error_page($smarty, new CaptchaError($_));
                exit(1);
                break;
        }
    }

    // Redirection.
    $goto = NULL;
    $should_update_goto = FALSE;
    if (isset($_REQUEST['goto'])) {

        // Check redirection.
        if ( ($goto = users_check_goto($_REQUEST['goto'])) === FALSE) {

            // Cleanup.
            DataExchange::releaseResources();

            display_error_page($smarty, new UserGotoError());
            exit(1);
        }

        if (!isset($_SESSION['goto']) || $_SESSION['goto'] != $goto) {
            $_SESSION['goto'] = $goto;
            $should_update_goto = TRUE;
        }
    } else {

        // Cleanup.
        DataExchange::releaseResources();

        display_error_page($smarty, new UserGotoError());
        exit(1);
    }

    // Password.
    $password = NULL;
    $should_update_password = FALSE;
    if (isset($_REQUEST['password']) && $_REQUEST['password'] != '') {
        $password = posts_check_password($_REQUEST['password']);
        if ($password === FALSE) {

            // Cleanup
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }

        if (!isset($_SESSION['password'])
                || $_SESSION['password'] != $password) {

            $_SESSION['password'] = $password;
            $should_update_password = TRUE;
        }
    }

    // Sage.
    $sage = $thread['sage'];
	if (isset($_REQUEST['sage']) && $_REQUEST['sage'] === 'sage') {
		$sage = 1;
    }

    // Name and tripcode.
    $name = NULL;
    $tripcode = NULL;
    if (!$board['force_anonymous']) {
        $name = htmlentities($_REQUEST['name'], ENT_QUOTES,
                             Config::MB_ENCODING);
        $name = str_replace('\\', '\\\\', $name);
        $name = str_replace("\n", '', $name);
        $name = str_replace("\r", '', $name);
        if (posts_check_name_size($name) == FALSE) {

            // Cleanup
            DataExchange::releaseResources();

            display_error_page($smarty, kotoba_last_error());
            exit(1);
        }
        $name_tripcode = calculate_tripcode($name);
        $_SESSION['name'] = $name;
        $name = $name_tripcode[0];
        $tripcode = $name_tripcode[1];
    }

    // Subject.
    $subject = htmlentities($_REQUEST['subject'], ENT_QUOTES,
                            Config::MB_ENCODING);
    $subject = str_replace('\\', '\\\\', $subject);
    $subject = str_replace("\n", '', $subject);
    $subject = str_replace("\r", '', $subject);
    if (posts_check_subject_size($subject) == FALSE) {

        // Cleanup
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }

    // Attachment type.
	$attachment_type = null;
    if ($thread['with_attachments'] || ($thread['with_attachments'] === NULL
            && $board['with_attachments'])) {

        if ((isset($_FILES['file'])
                    && $_FILES['file']['error'] != UPLOAD_ERR_NO_FILE)
                || use_oekaki()) {

            if (!use_oekaki()) {
                if (upload_check_error($_FILES['file']['error']) == FALSE) {

                    // Cleanup
                    DataExchange::releaseResources();

                    display_error_page($smarty, kotoba_last_error());
                    exit(1);
                }
                $uploaded_file_path = $_FILES['file']['tmp_name'];
                $uploaded_file_name = $_FILES['file']['name'];
                $uploaded_file_size = $_FILES['file']['size'];
            } else {
                $uploaded_file_path = Config::ABS_PATH
                                      . "/shi/{$_SESSION['oekaki']['file']}";
                $uploaded_file_name = $_SESSION['oekaki']['file'];
                $uploaded_file_size = filesize($uploaded_file_path);
                if ($uploaded_file_size === FALSE) {
                    throw new ParanoicException('Cannot calculate filesize.');
                }
            }

            // Get upload type.
            $_ = get_extension($uploaded_file_name);
            $_ = mb_strtolower($_, Config::MB_ENCODING);
            if ( ($_ = upload_types_check_extension($_)) === FALSE) {

                // Cleanup
                DataExchange::releaseResources();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            $upload_type = upload_types_get_by_board_ext($board['id'], $_);
            if ($upload_type == NULL) {

                // Cleanup
                DataExchange::releaseResources();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }

            if ($upload_type['is_image']) {
                if (images_check_size($uploaded_file_size) === FALSE) {

                    // Cleanup
                    DataExchange::releaseResources();

                    display_error_page($smarty, kotoba_last_error());
                    exit(1);
                }
                $attachment_type = Config::ATTACHMENT_TYPE_IMAGE;
            } else {
                $attachment_type = Config::ATTACHMENT_TYPE_FILE;
            }
        } elseif (is_macrochan_enabled($board)
                  && isset($_REQUEST['macrochan_tag'])
                  && $_REQUEST['macrochan_tag'] != '') {

            $_ = macrochan_tags_check($_REQUEST['macrochan_tag']);
            if ($_ === FALSE) {

                // Cleanup
                DataExchange::releaseResources();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            $macrochan_tag['name'] = $_;
            $attachment_type = Config::ATTACHMENT_TYPE_LINK;
        } elseif (is_youtube_enabled($board)
                  && isset($_REQUEST['youtube_video_code'])
                  && $_REQUEST['youtube_video_code'] != '') {

            $youtube_video_code = videos_check_code(
                $_REQUEST['youtube_video_code']
            );
            if ($youtube_video_code === FALSE) {

                // Cleanup
                DataExchange::releaseResources();

                display_error_page($smarty, kotoba_last_error());
                exit(1);
            }
            $attachment_type = Config::ATTACHMENT_TYPE_VIDEO;
        }
    }

    // Text.
    $text = $_REQUEST['text'];
    if ($attachment_type === NULL && !preg_match('/\S/', $text)) {

        // Cleanup
        DataExchange::releaseResources();

        display_error_page($smarty, new EmptyPostError());
        exit(1);
    }
    if (posts_check_text_size($text) === FALSE) {

        // Cleanup
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }
    if (Config::ENABLE_SPAMFILTER) {
        $spam_filter = spamfilter_get_all();
        foreach ($spam_filter as $record) {
            if (TRUE || preg_match("/{$record['pattern']}/", $text) > 0) {

                // Cleanup
                DataExchange::releaseResources();

                display_error_page($smarty, new SpamError());
                exit(1);
            }
        }
    }
    $text = htmlentities($text, ENT_QUOTES, Config::MB_ENCODING);
    //$text = transform($text);
    if (Config::ENABLE_WORDFILTER) {
        $words = words_get_all_by_board(boards_check_id($_REQUEST['board']));
        foreach ($words as $_) {
            $text = preg_replace("#".$_['word']."#iu", $_['replace'], $text);
        }
    }
    $text = str_replace('\\', '\\\\', $text);
    if (!posts_check_text($text)) {

        // Cleanup
        DataExchange::releaseResources();

        display_error_page($smarty, new NonUnicodeError());
        exit(1);
    }
    posts_prepare_text($text, $board);
    if (posts_check_text_size($text) === FALSE) {

        // Cleanup
        DataExchange::releaseResources();

        display_error_page($smarty, kotoba_last_error());
        exit(1);
    }

    // Attachment.
    if ($attachment_type !== null) {
        if ($attachment_type == Config::ATTACHMENT_TYPE_FILE
                || $attachment_type == Config::ATTACHMENT_TYPE_IMAGE) {

            $file_hash = calculate_file_hash($uploaded_file_path);
            $file_exists = false;
            $same_attachments = null;
            switch ($board['same_upload']) {
                case 'once':
                    $same_attachments = attachments_get_same($board['id'],
                                                             $_SESSION['user'],
                                                             $file_hash);
                    if (count($same_attachments) > 0) {
                        $file_exists = true;
                    }
                    break;

                case 'no':
                    $same_attachments = attachments_get_same($board['id'],
                                                             $_SESSION['user'],
                                                             $file_hash);
                    if (count($same_attachments) > 0) {
                        $smarty->assign('show_control', is_admin() || is_mod());
                        $smarty->assign('boards', boards_get_visible($_SESSION['user']));
                        $smarty->assign('board', $board);
                        $smarty->assign('same_attachments', $same_attachments);
                        $smarty->display('same_attachments.tpl');

                        // Cleanup.
                        DataExchange::releaseResources();

                        exit(0);
                    }
                    break;

                case 'yes':
                    break;
                default:
                    throw new ParanoicException('Unknown same uploads '
                                                .'behaviour.');
                    break;
            }
            if (!$file_exists) {
                $file_names = create_filenames($upload_type['store_extension']);
            }
        }

        if ($attachment_type == Config::ATTACHMENT_TYPE_FILE) {
            if ($file_exists) {
                $attachment_id = $same_attachments[0]['id'];
            } else {
                $abs_file_path = Config::ABS_PATH
                                 . "/{$board['name']}/other/{$file_names[0]}";
                move_uploded_file($uploaded_file_path, $abs_file_path);
                $attachment_id = files_add($file_hash,
                                           $file_names[0],
                                           $uploaded_file_size,
                                           $upload_type['thumbnail_image'],
                                           Config::THUMBNAIL_WIDTH,
                                           Config::THUMBNAIL_HEIGHT);
            }
        } elseif ($attachment_type === Config::ATTACHMENT_TYPE_IMAGE) {
            if ($file_exists) {
                $attachment_id = $same_attachments[0]['id'];
            } else {
                $img_dimensions = image_get_dimensions($upload_type,
                                                       $uploaded_file_path);
                if ($img_dimensions['x'] < Config::MIN_IMGWIDTH
                        && $img_dimensions['y'] < Config::MIN_IMGHEIGHT) {

                    // Cleanup
                    DataExchange::releaseResources();

                    display_error_page($smarty, new MinImgDimentionsError());
                    exit(1);
                }
                $abs_img_path = Config::ABS_PATH
                                . "/{$board['name']}/img/{$file_names[0]}";
                $abs_thumb_path = Config::ABS_PATH
                                  . "/{$board['name']}/thumb/{$file_names[1]}";
                if (!use_oekaki()) {
                    move_uploded_file($uploaded_file_path, $abs_img_path);
                    $thumb_dimensions = create_thumbnail(
                        $abs_img_path,
                        $abs_thumb_path,
                        $img_dimensions,
                        $upload_type,
                        Config::THUMBNAIL_WIDTH,
                        Config::THUMBNAIL_HEIGHT
                    );
                    if ($thumb_dimensions === FALSE) {

                        // Cleanup
                        unlink($abs_img_path);
                        DataExchange::releaseResources();

                        display_error_page($smarty, kotoba_last_error());
                        exit(1);
                    }
                } else {
                    copy_uploded_file($uploaded_file_path, $abs_img_path);
                    copy_uploded_file(
                        Config::ABS_PATH
                        . "/shi/{$_SESSION['oekaki']['thumbnail']}",
                        $abs_thumb_path
                    );
                    $thumb_dimensions = array('x' => Config::THUMBNAIL_WIDTH,
                                              'y' => Config::THUMBNAIL_HEIGHT);
                }
                $spoiler = (isset($_REQUEST['spoiler'])
                            && $_REQUEST['spoiler'] == '1') ? true : false;
                $attachment_id = images_add($file_hash,
                                            $file_names[0],
                                            $img_dimensions['x'],
                                            $img_dimensions['y'],
                                            $uploaded_file_size,
                                            $file_names[1],
                                            $thumb_dimensions['x'],
                                            $thumb_dimensions['y'],
                                            $spoiler);
            }
        } elseif ($attachment_type == Config::ATTACHMENT_TYPE_LINK) {
            $macrochan_image = macrochan_images_get_random(
                $macrochan_tag['name']
            );
            $macrochan_image['name'] = "http://12ch.ru/macro/index.php/image"
                                       . "/{$macrochan_image['name']}";
            $macrochan_image['thumbnail'] = "http://12ch.ru/macro/index.php/"
                                            . "thumb/"
                                            . "{$macrochan_image['thumbnail']}";
            $attachment_id = links_add($macrochan_image['name'],
                                       $macrochan_image['width'],
                                       $macrochan_image['height'],
                                       $macrochan_image['size'],
                                       $macrochan_image['thumbnail'],
                                       $macrochan_image['thumbnail_w'],
                                       $macrochan_image['thumbnail_h']);
        } elseif ($attachment_type == Config::ATTACHMENT_TYPE_VIDEO) {
            $attachment_id = videos_add($youtube_video_code, 220, 182);
        } else {
            throw new ParanoicException("Attachment type $attachment_type not "
                                        . "supported.");
        }
    }

    // Save post.
    $post = posts_add($board['id'], $thread['id'], $_SESSION['user'], $password,
                      $name, $tripcode, ip2long($_SERVER['REMOTE_ADDR']),
                      $subject, date(Config::DATETIME_FORMAT), $text, $sage);

    // Save attachment.
    if ($attachment_type !== NULL) {
        switch ($attachment_type) {
            case Config::ATTACHMENT_TYPE_FILE:
                posts_files_add($post['id'], $post['board'], $attachment_id, 0);
                break;
            case Config::ATTACHMENT_TYPE_IMAGE:
                posts_images_add($post['id'], $post['board'], $attachment_id,
                                 0);
                break;
            case Config::ATTACHMENT_TYPE_LINK:
                posts_links_add($post['id'], $attachment_id, 0);
                break;
            case Config::ATTACHMENT_TYPE_VIDEO:
                posts_videos_add($post['id'], $attachment_id, 0);
                break;
            default:
                throw new ParanoicException("Attachment type $attachment_type "
                                            . "not supported.");
        }
    }

    // Update password and redirection.
	if ($_SESSION['user'] != Config::GUEST_ID && $should_update_password) {
        users_set_password($_SESSION['user'], $password);
    }
    if ($_SESSION['user'] != Config::GUEST_ID && $should_update_goto) {
        users_set_goto($_SESSION['user'], $goto);
    }

    // Popdown threads.
	foreach (popdown_handlers_get_all() as $popdown_handler) {
        if ($board['popdown_handler'] == $popdown_handler['id']) {
            $popdown_handler['name']($board['id']);
            break;
        }
    }

    unset($_SESSION['oekaki']);

    // Redirect.
    if ($_SESSION['goto'] == 't') {
        header('Location: ' . Config::DIR_PATH
               . "/{$board['name']}/{$thread['original_post']}/");
    } else {
        header('Location: ' . Config::DIR_PATH . "/{$board['name']}/");
    }

    // Temporary code for highload tests!
    if (ctype_digit($post['id'])) {
        echo "{$post['id']}";
    } else {
        throw new ParanoicException('ID of new post is empty. Highload issue?');
    }

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch (KotobaException $e) {

    // Cleanup.
    if (isset($abs_file_path) && file_exists($abs_file_path)) {
        unlink($abs_file_path);
    }
    if (isset($abs_img_path) && file_exists($abs_img_path)) {
        unlink($abs_img_path);
    }
    if (isset($abs_thumb_path) && file_exists($abs_thumb_path)) {
        unlink($abs_thumb_path);
    }
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
