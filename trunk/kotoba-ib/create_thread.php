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

// Скрипт создания нитей.

require_once 'config.php';
require_once Config::ABS_PATH . '/lib/errors.php';
require Config::ABS_PATH . '/locale/' . Config::LANGUAGE . '/errors.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/popdown_handlers.php';
require_once Config::ABS_PATH . '/lib/upload_handlers.php';
require_once Config::ABS_PATH . '/lib/mark.php';
require_once Config::ABS_PATH . '/lib/latex_render.php';

try {
    kotoba_session_start();
    locale_setup();
    $smarty = new SmartyKotobaSetup($_SESSION['language'],
            $_SESSION['stylesheet']);

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

    /* TODO: Логичней было бы если бы проверка была вне фунции. */
    $board = boards_get_changeable_by_id(boards_check_id($_POST['board']),
            $_SESSION['user']);

    if (!is_admin() && (($board['enable_captcha'] === null && Config::ENABLE_CAPTCHA) || $board['enable_captcha'])) {
        if (!isset($_POST['captcha_code']) || !isset($_SESSION['captcha_code']) || mb_strtolower($_POST['captcha_code'], Config::MB_ENCODING) != $_SESSION['captcha_code']) {
            var_dump(mb_strtolower($_POST['captcha_code'], Config::MB_ENCODING));
            var_dump($_SESSION['captcha_code']);
            throw new CommonException(CommonException::$messages['CAPTCHA']);
        }
    }

    $goto = null;
    $should_update_goto = false;
    if (isset($_POST['goto'])) {
        $goto = users_check_goto($_POST['goto']);
        if (!isset($_SESSION['goto']) || $_SESSION['goto'] != $goto) {
            $_SESSION['goto'] = $goto;
            $should_update_goto = true;
        }
    } else {
        throw new FormatException(FormatException::$messages['USER_GOTO']);
    }

    $password = null;
    $should_update_password = false;
    if (isset($_POST['password']) && $_POST['password'] != '') {
        $password = posts_check_password($_POST['password']);
        if (!isset($_SESSION['password'])
                || $_SESSION['password'] != $password) {
            $_SESSION['password'] = $password;
            $should_update_password = true;
        }
    }

    $name = null;
    $tripcode = null;
    if (!$board['force_anonymous']) {
        posts_check_name_size($_POST['name']);
        $name = htmlentities($_POST['name'], ENT_QUOTES, Config::MB_ENCODING);
        $name = str_replace('\\', '\\\\', $name);
        posts_check_name_size($name);
        $name = str_replace("\n", '', $name);
        $name = str_replace("\r", '', $name);
        posts_check_name_size($name);
        // TODO: Check what tripcode cannot be empty string.
        $name_tripcode = calculate_tripcode($name);
        $_SESSION['name'] = $name;
        $name = $name_tripcode[0];
        $tripcode = $name_tripcode[1];
    }

    posts_check_subject_size($_POST['subject']);
    $subject = htmlentities($_POST['subject'], ENT_QUOTES, Config::MB_ENCODING);
    $subject = str_replace('\\', '\\\\', $subject);
    posts_check_subject_size($subject);
    $subject = str_replace("\n", '', $subject);
    $subject = str_replace("\r", '', $subject);

    posts_check_text_size($_POST['text']);
    if (Config::ENABLE_SPAMFILTER) {
        $spam_filter = spamfilter_get_all();
        foreach ($spam_filter as $record) {
            if (preg_match("/{$record['pattern']}/", $_POST['text']) > 0) {
                throw new CommonException(CommonException::$messages['SPAM_DETECTED']);
            }
        }
    }
    $text = htmlentities($_POST['text'], ENT_QUOTES, Config::MB_ENCODING);
    $text = transform($text);
	posts_check_text_size($text);
    if (Config::ENABLE_WORDFILTER) {
        $words = words_get_all_by_board(boards_check_id($_POST['board']));
        foreach ($words as $word) {
            $text = preg_replace("#".$word['word']."#iu", $word['replace'], $text);
        }
    }
    $text = str_replace('\\', '\\\\', $text);
    posts_check_text_size($text);
    posts_check_text($text);
    posts_prepare_text($text, $board);
    posts_check_text_size($text);

    $attachment_type = null;
    if ($board['with_attachments']) {
        if ($_FILES['file']['error'] != UPLOAD_ERR_NO_FILE || use_oekaki()) {
            if (!use_oekaki()) {
                check_upload_error($_FILES['file']['error']);
                $uploaded_file_path = $_FILES['file']['tmp_name'];
                $uploaded_file_name = $_FILES['file']['name'];
                $uploaded_file_size = $_FILES['file']['size'];
            } else {
                $uploaded_file_path = Config::ABS_PATH . "/shi/{$_SESSION['oekaki']['file']}";
                $uploaded_file_name = $_SESSION['oekaki']['file'];
                if ( ($uploaded_file_size = filesize($uploaded_file_path)) === false) {
                    throw new Exception('Cannot calculate filesize.');
                }
            }
            $uploaded_file_ext = get_extension($uploaded_file_name);
            $uploaded_file_ext = mb_strtolower($uploaded_file_ext, Config::MB_ENCODING);
            $upload_types = upload_types_get_by_board($board['id']);
            $found = false;
            $upload_type = null;
            foreach ($upload_types as $ut) {
                if ($ut['extension'] == $uploaded_file_ext) {
                    $found = true;
                    $upload_type = $ut;
                    break;
                }
            }
            if (!$found) {
                throw new UploadException(UploadException::$messages['UPLOAD_FILETYPE_NOT_SUPPORTED']);
            }
            if ($upload_type['is_image']) {
                $attachment_type = Config::ATTACHMENT_TYPE_IMAGE;
                images_check_size($uploaded_file_size);
            } else {
                $attachment_type = Config::ATTACHMENT_TYPE_FILE;
            }
        } elseif (($board['enable_macro'] === null && Config::ENABLE_MACRO || $board['enable_macro']) && isset($_POST['macrochan_tag']) && $_POST['macrochan_tag'] != '') {
            /* TODO Actually macrochan tag entity is a pair: id, name. Is
             * $macrochan_tag should be $macrochan_tag_name?
             */
            $macrochan_tag = macrochan_tags_check($_POST['macrochan_tag']);
            $attachment_type = Config::ATTACHMENT_TYPE_LINK;
        } elseif (($board['enable_youtube'] === null && Config::ENABLE_YOUTUBE || $board['enable_youtube'])
                && isset($_POST['youtube_video_code'])
                && $_POST['youtube_video_code'] != '') {
            $youtube_video_code = videos_check_code($_POST['youtube_video_code']);
            $attachment_type = Config::ATTACHMENT_TYPE_VIDEO;
        } else {
            throw new UploadException(UploadException::$messages['UNKNOWN']);
        }
    }

    /* TODO: А если одни пробелы или другие пустые сущности? */
    if ($attachment_type === null && $text == '') {
        throw new NodataException(NodataException::$messages['EMPTY_MESSAGE']);
    }

    if ($attachment_type !== null) {
        if ($attachment_type == Config::ATTACHMENT_TYPE_FILE
                || $attachment_type == Config::ATTACHMENT_TYPE_IMAGE) {
            $file_hash = calculate_file_hash($uploaded_file_path);
            $file_exists = false;
            $same_attachments = null;
            switch ($board['same_upload']) {
                case 'once':
                    $same_attachments = attachments_get_same($board['id'],
                        $_SESSION['user'], $file_hash);
                    if (count($same_attachments) > 0) {
                        $file_exists = true;
                    }
                    break;

                case 'no':
                    $same_attachments = attachments_get_same($board['id'],
                        $_SESSION['user'], $file_hash);
                    if (count($same_attachments) > 0) {
                        show_same_attachments($smarty, $board['name'],
                            $same_attachments);
                        DataExchange::releaseResources();
                        exit;
                    }
                    break;

                case 'yes':
                default:
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
                $attachment_id = files_add($file_hash, $file_names[0],
                    $uploaded_file_size, $upload_type['thumbnail_image'],
                    Config::THUMBNAIL_WIDTH, Config::THUMBNAIL_HEIGHT);
            }
        } elseif ($attachment_type === Config::ATTACHMENT_TYPE_IMAGE) {
            if ($file_exists) {
                $attachment_id = $same_attachments[0]['id'];
            } else {
                $img_dimensions = image_get_dimensions($upload_type, $uploaded_file_path);
                if($img_dimensions['x'] < Config::MIN_IMGWIDTH && $img_dimensions['y'] < Config::MIN_IMGHEIGHT) {
                    throw new LimitException(LimitException::$messages['MIN_IMG_DIMENTIONS']);
                }
                $abs_img_path = Config::ABS_PATH . "/{$board['name']}/img/{$file_names[0]}";
                $abs_thumb_path = Config::ABS_PATH . "/{$board['name']}/thumb/{$file_names[1]}";
                if (!use_oekaki()) {
                    move_uploded_file($uploaded_file_path, $abs_img_path);

                    // TODO Unhardcode handler name.
                    $force = $upload_type['upload_handler_name'] === 'thumb_internal_png' ? true : false;

                    $thumb_dimensions = create_thumbnail($abs_img_path,
                                                         $abs_thumb_path,
                                                         $img_dimensions,
                                                         $upload_type,
                                                         Config::THUMBNAIL_WIDTH,
                                                         Config::THUMBNAIL_HEIGHT,
                                                         $force);
                } else {
                    copy_uploded_file($uploaded_file_path, $abs_img_path);
                    copy_uploded_file(Config::ABS_PATH . "/shi/{$_SESSION['oekaki']['thumbnail']}",
                                      $abs_thumb_path);
                    $thumb_dimensions = array('x' => Config::THUMBNAIL_WIDTH,
                                              'y' => Config::THUMBNAIL_HEIGHT);
                }
                $spoiler = (isset($_POST['spoiler']) && $_POST['spoiler'] == '1') ? true : false;
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
            $macrochan_image = macrochan_images_get_random($macrochan_tag);
            $macrochan_image['name'] = "http://12ch.ru/macro/index.php/image/{$macrochan_image['name']}";
            $macrochan_image['thumbnail'] = "http://12ch.ru/macro/index.php/thumb/{$macrochan_image['thumbnail']}";
            $attachment_id = links_add($macrochan_image['name'],
                    $macrochan_image['width'], $macrochan_image['height'],
                    $macrochan_image['size'], $macrochan_image['thumbnail'],
                    $macrochan_image['thumbnail_w'],
                    $macrochan_image['thumbnail_h']);
        } elseif ($attachment_type == Config::ATTACHMENT_TYPE_VIDEO) {
            $attachment_id = videos_add($youtube_video_code, 220, 182);
        } else {
            throw new CommonException('Not supported.');
        }
    }

    // Create empty thread and create post.
    $thread = threads_add($board['id'], null, null, 0, null);
    date_default_timezone_set(Config::DEFAULT_TIMEZONE);
    $post = posts_add($board['id'], $thread['id'], $_SESSION['user'], $password,
            $name, $tripcode, ip2long($_SERVER['REMOTE_ADDR']), $subject,
            date(Config::DATETIME_FORMAT), $text, null);

    // Закрепляем сообщение как оригинальное сообщение созданной пустой нити.
    threads_edit_original_post($thread['id'], $post['number']);

    if ($attachment_type !== null) {
        switch ($attachment_type) {
            case Config::ATTACHMENT_TYPE_FILE:
                posts_files_add($post['id'], $attachment_id, 0);
                break;
            case Config::ATTACHMENT_TYPE_IMAGE:
                posts_images_add($post['id'], $attachment_id, 0);
                break;
            case Config::ATTACHMENT_TYPE_LINK:
                posts_links_add($post['id'], $attachment_id, 0);
                break;
            case Config::ATTACHMENT_TYPE_VIDEO:
                posts_videos_add($post['id'], $attachment_id, 0);
                break;
            default:
                throw new CommonException('Not supported.');
                break;
        }
    }

    if ($_SESSION['user'] != Config::GUEST_ID && $should_update_password) {
        users_set_password($_SESSION['user'], $password);
    }
    if ($should_update_goto) {
        users_set_goto($_SESSION['user'], $goto);
    }

    foreach (popdown_handlers_get_all() as $popdown_handler) {
        if ($board['popdown_handler'] == $popdown_handler['id']) {
            $popdown_handler['name']($board['id']);
            break;
        }
    }

    DataExchange::releaseResources();
    unset($_SESSION['oekaki']);

    if ($_SESSION['goto'] == 't') {
        header('Location: ' . Config::DIR_PATH . "/{$board['name']}/{$post['number']}/");
    } else {
        header('Location: ' . Config::DIR_PATH . "/{$board['name']}/");
    }
    exit;
} catch (Exception $e) {
    $smarty->assign('msg', $e->__toString());
    DataExchange::releaseResources();
    if (isset($abs_file_path)) { // Удаление загруженного файла.
        @unlink($abs_file_path);
    }
    if (isset($abs_img_path)) { // Удаление загруженного файла.
        @unlink($abs_img_path);
    }
    if (isset($abs_thumb_path)) { // Удаление уменьшенной копии.
        @unlink($abs_thumb_path);
    }
    die($smarty->fetch('error.tpl'));
}
?>