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

/**
 * Фукнции-обёртки для формирования html-кода страниц.
 * @package api
 */

/**
 * Формирует html-код ответа в нить. Если урезание текста сообщения не
 * требуется, то флаг урезания текста сообщения должен быть false, а аргумент
 * lines_per_post должен быть null.
 * @param SmartyKotobaSetup $smarty Шаблонизатор.
 * @param array $board Доска.
 * @param array $thread Нить.
 * @param array $post Сообщение.
 * @param array $posts_attachments Связи вложений с сообщениями.
 * @param array $attachments Вложения.
 * @param boolean $crop Флаг урезания текста сообщения.
 * @param int $lines_per_post Количество строк, которые нужно
 * оставить при урезании текста сообщения.
 */
function post_simple_generate_html($smarty, $board, $thread, $post, $posts_attachments, $attachments, $crop, $lines_per_post) {
    $post_attachments = array();
    $is_cutted = false;

    if ($crop) {
        $post['text'] = posts_corp_text($post['text'], $lines_per_post, $is_cutted);
    }
    $post['text_cutted'] = $is_cutted; // Fake field.

    foreach ($posts_attachments as $pa) {
        if ($pa['post'] == $post['id']) {
            foreach ($attachments as $a) {
                if ($a['attachment_type'] == $pa['attachment_type']) {
                    switch ($a['attachment_type']) {
                        case Config::ATTACHMENT_TYPE_FILE:
                            if ($a['id'] == $pa['file']) {
                                $a['file_link'] = Config::DIR_PATH . "/{$board['name']}/other/{$a['name']}";
                                $a['thumbnail_link'] = Config::DIR_PATH . "/img/{$a['thumbnail']}";
                                $post['with_attachments'] = true;
                                array_push($post_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_IMAGE:
                            if ($a['id'] == $pa['image']) {
                                $a['image_link'] = Config::DIR_PATH . "/{$board['name']}/img/{$a['name']}";
                                $a['thumbnail_link'] = Config::DIR_PATH . "/{$board['name']}/thumb/{$a['thumbnail']}";
                                $post['with_attachments'] = true;
                                array_push($post_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_LINK:
                            if ($a['id'] == $pa['link']) {
                                $post['with_attachments'] = true;
                                array_push($post_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_VIDEO:
                            if ($a['id'] == $pa['video']) {
                                $smarty->assign('code', $a['code']);
                                $a['video_link'] = $smarty->fetch('youtube.tpl');
                                $post['with_attachments'] = true;
                                array_push($post_attachments, $a);
                            }
                            break;
                        default:
                            throw new CommonException('Not supported.');
                            break;
                    }
                }
            }
        }
    }
    $post['ip'] = long2ip($post['ip']);
    if ($post['ip'] != '127.0.0.1') {
        $geoip = geoip_record_by_name($post['ip']);
        $smarty->assign('country', array('name' => $geoip['country_name'], 'code' => strtolower($geoip['country_code'])));
    }
    $smarty->assign('simple_post', $post);
    $smarty->assign('simple_attachments', $post_attachments);
    return $smarty->fetch('post_simple.tpl');
}
/**
 * Формирует html-код оригинального сообщения.
 * @param SmartyKotobaSetup $smarty Шаблонизатор.
 * @param array $board Доска.
 * @param array $thread Нить.
 * @param array $post Сообщение.
 * @param array $posts_attachments Связи вложений с сообщениями.
 * @param array $attachments Вложения.
 * @param boolean $crop Флаг урезания текста сообщения.
 * @param int $lines_per_post Количество строк, которые нужно
 * @param boolean $show_skipped Флаг показа количества не показанных сообщений.
 * @param int $posts_per_thread Количество показываемых сообщений в нити.
 * @param int $show_reply Показывать ссылку на просмотр нити и ответ.
 */
function post_original_generate_html($smarty, $board, $thread, $post, $posts_attachments, $attachments, $crop, $lines_per_post, $show_skipped, $posts_per_thread, $show_reply) {
    $original_attachments = array();
    $is_cutted = false;

    if ($crop) {
        $post['text'] = posts_corp_text($post['text'], $lines_per_post, $is_cutted);
    }
    $post['text_cutted'] = $is_cutted; // Fake field.

    foreach ($posts_attachments as $pa) {
        if ($pa['post'] == $post['id']) {
            foreach ($attachments as $a) {
                if ($a['attachment_type'] == $pa['attachment_type']) {
                    switch ($a['attachment_type']) {
                        case Config::ATTACHMENT_TYPE_FILE:
                            if ($a['id'] == $pa['file']) {
                                $a['file_link'] = Config::DIR_PATH . "/{$board['name']}/other/{$a['name']}";
                                $a['thumbnail_link'] = Config::DIR_PATH . "/img/{$a['thumbnail']}";
                                $post['with_attachments'] = true;
                                array_push($original_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_IMAGE:
                            if ($a['id'] == $pa['image']) {
                                $a['image_link'] = Config::DIR_PATH . "/{$board['name']}/img/{$a['name']}";
                                $a['thumbnail_link'] = Config::DIR_PATH . "/{$board['name']}/thumb/{$a['thumbnail']}";
                                $post['with_attachments'] = true;
                                array_push($original_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_LINK:
                            if ($a['id'] == $pa['link']) {
                                $post['with_attachments'] = true;
                                array_push($original_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_VIDEO:
                            if ($a['id'] == $pa['video']) {
                                $smarty->assign('code', $a['code']);
                                $a['video_link'] = $smarty->fetch('youtube.tpl');
                                $post['with_attachments'] = true;
                                array_push($original_attachments, $a);
                            }
                            break;
                        default:
                            throw new CommonException('Not supported.');
                            break;
                    }
                }
            }
        }
    }
    $post['ip'] = long2ip($post['ip']);
    if ($post['ip'] != '127.0.0.1') {
        $geoip = geoip_record_by_name($post['ip']);
        $smarty->assign('country', array('name' => $geoip['country_name'], 'code' => strtolower($geoip['country_code'])));
    }
    $smarty->assign('sticky', $thread['sticky']);
    $smarty->assign('show_skipped', $show_skipped);
    if ($show_skipped) {

        // - 1 is original post.
        $smarty->assign('skipped', ($thread['posts_count'] - $posts_per_thread - 1));
    }
    $smarty->assign('original_post', $post);
    $smarty->assign('original_attachments', $original_attachments);
    $smarty->assign('show_reply', $show_reply);
    return $smarty->fetch('post_original.tpl');
}
?>
