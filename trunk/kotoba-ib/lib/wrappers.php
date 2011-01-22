<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Wrappers for generate html-code.
 * @package api
 */

/**
 * Create html-code of simple post.
 * @param SmartyKotobaSetup $smarty Template engine.
 * @param array $board Board.
 * @param array $thread Thread.
 * @param array $post Post.
 * @param array $posts_attachments Posts attachments relations.
 * @param array $attachments Attachments.
 * @param boolean $crop Crop message text.
 * @param int $lines_per_post Count of lines per post. Ignored if crop is FALSE.
 * @param boolean $author_admin Author of this post is admin.
 */
function post_simple_generate_html($smarty,
                                   $board,
                                   $thread,
                                   $post,
                                   $posts_attachments,
                                   $attachments,
                                   $crop,
                                   $lines_per_post,
                                   $author_admin) {

    if ($crop) {
        $post['text'] = posts_corp_text($post['text'],
                                        $lines_per_post,
                                        $post['text_cutted']);
    }

    $post_attachments = wrappers_attachments_get_by_post($smarty,
                                                         $post,
                                                         $posts_attachments,
                                                         $attachments);
    $post['ip'] = long2ip($post['ip']);
    if ($post['ip'] != '127.0.0.1' && strpos($post['ip'], '192.168') === false) {
        $geoip = geoip_record_by_name($post['ip']);
        $smarty->assign('country',
                        array('name' => $geoip['country_name'],
                              'code' => strtolower($geoip['country_code'])));
    }
    $smarty->assign('simple_post', $post);
    $smarty->assign('simple_attachments', $post_attachments);
    $postid = calculate_tripcode("#{$post['ip']}");
    $smarty->assign('postid', $postid[1]);
    $smarty->assign('author_admin', $author_admin);
    return $smarty->fetch('post_simple.tpl');
}
/**
 * Create html-code of original post.
 * @param SmartyKotobaSetup $smarty Template engine.
 * @param array $board Board.
 * @param array $thread Thread.
 * @param array $post Post.
 * @param array $posts_attachments Posts attachments relations.
 * @param array $attachments Attachments.
 * @param boolean $crop Crop message text.
 * @param int $lines_per_post Count of lines per post. Ignored if crop is FALSE.
 * @param boolean $show_skipped Show count of skipped posts.
 * @param int $posts_per_thread Count of posts per thread.
 * preview. Ignored if show_skipped is FALSE.
 * @param boolean $show_reply Show "Reply" link.
 * @param boolean $author_admin Author of this post is admin.
 */
function post_original_generate_html($smarty,
                                     $board,
                                     $thread,
                                     $post,
                                     $posts_attachments,
                                     $attachments,
                                     $crop,
                                     $lines_per_post,
                                     $show_skipped,
                                     $posts_per_thread,
                                     $show_reply,
                                     $author_admin) {

    if ($crop) {
        $post['text'] = posts_corp_text($post['text'],
                                        $lines_per_post,
                                        $post['text_cutted']);
    }

    $original_attachments = wrappers_attachments_get_by_post($smarty,
                                                             $post,
                                                             $posts_attachments,
                                                             $attachments);
    $post['ip'] = long2ip($post['ip']);
    if ($post['ip'] != '127.0.0.1' && strpos($post['ip'], '192.168') === false) {
        $geoip = geoip_record_by_name($post['ip']);
        $smarty->assign('country',
                        array('name' => $geoip['country_name'],
                              'code' => strtolower($geoip['country_code'])));
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
    $postid = calculate_tripcode("#{$post['ip']}");
    $smarty->assign('postid', $postid[1]);
    $smarty->assign('author_admin', $author_admin);
    return $smarty->fetch('post_original.tpl');
}

function wrappers_attachments_get_by_post($smarty, $post, $posts_attachments, $attachments) {
    $desired_attachments = array();

    foreach ($posts_attachments as $pa) {
        if ($pa['post'] == $post['id']) {
            foreach ($attachments as $a) {
                if ($a['attachment_type'] == $pa['attachment_type']) {
                    switch ($a['attachment_type']) {
                        case Config::ATTACHMENT_TYPE_FILE:
                            if ($a['id'] == $pa['file']) {
                                $a['file_link'] = Config::DIR_PATH
                                    . "/{$board['name']}/other/{$a['name']}";
                                $a['thumbnail_link'] = Config::DIR_PATH
                                    . "/img/{$a['thumbnail']}";
                                $post['with_attachments'] = true;
                                array_push($desired_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_IMAGE:
                            if ($a['id'] == $pa['image']) {
                                $a['image_link'] = Config::DIR_PATH
                                    . "/{$board['name']}/img/{$a['name']}";
                                $a['thumbnail_link'] = Config::DIR_PATH
                                    . "/{$board['name']}/thumb/{$a['thumbnail']}";
                                $post['with_attachments'] = true;
                                array_push($desired_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_LINK:
                            if ($a['id'] == $pa['link']) {
                                $post['with_attachments'] = true;
                                array_push($desired_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_VIDEO:
                            if ($a['id'] == $pa['video']) {
                                $smarty->assign('code', $a['code']);
                                $a['video_link'] = $smarty->fetch('youtube.tpl');
                                $post['with_attachments'] = true;
                                array_push($desired_attachments, $a);
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

    return $desired_attachments;
}
?>
