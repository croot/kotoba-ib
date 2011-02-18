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
                                   &$post,
                                   $posts_attachments,
                                   $attachments,
                                   $crop,
                                   $lines_per_post,
                                   $author_admin,
                                   $enable_geoip,
                                   $enable_postid,
                                   $enable_doubledash = TRUE,
                                   $enable_anchor = TRUE,
                                   $enable_remove_post = TRUE,
                                   $enable_extrabtns = TRUE) {

    if ($crop) {
        $post['text'] = posts_corp_text($post['text'],
                                        $lines_per_post,
                                        $post['text_cutted']);
    } else {
        $post['text_cutted'] = false;
    }

    $post_attachments = wrappers_attachments_get_by_post($smarty,
                                                         $board,
                                                         $post,
                                                         $posts_attachments,
                                                         $attachments);
    $post['ip'] = long2ip($post['ip']);
    if ($enable_geoip && $post['ip'] != '127.0.0.1' && strpos($post['ip'], '192.168') === false) {
        $geoip = geoip_record_by_name($post['ip']);
        $smarty->assign('country',
                        array('name' => $geoip['country_name'],
                              'code' => strtolower($geoip['country_code'])));
    }
    $smarty->assign('enable_doubledash', $enable_doubledash);
    $smarty->assign('enable_anchor', $enable_anchor);
    $smarty->assign('enable_remove_post', $enable_remove_post);
    $smarty->assign('enable_extrabtns', $enable_extrabtns);
    $smarty->assign('enable_geoip', $enable_geoip);
    $smarty->assign('enable_postid', $enable_postid);
    $smarty->assign('is_admin', is_admin());
    $smarty->assign('enable_translation', is_translation_enabled($board));
    $smarty->assign('post', $post);
    $smarty->assign('attachments', $post_attachments);
    if ($enable_postid) {
        $postid = calculate_tripcode("#{$post['ip']}");
        $smarty->assign('postid', $postid[1]);
    }
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
                                     &$post,
                                     $posts_attachments,
                                     $attachments,
                                     $crop,
                                     $lines_per_post,
                                     $show_skipped,
                                     $posts_per_thread,
                                     $show_reply,
                                     $author_admin,
                                     $enable_geoip,
                                     $enable_postid,
                                     $enable_anchor = TRUE,
                                     $enable_remove_post = TRUE,
                                     $enable_extrabtns = TRUE) {

    if ($crop) {
        $post['text'] = posts_corp_text($post['text'],
                                        $lines_per_post,
                                        $post['text_cutted']);
    } else {
        $post['text_cutted'] = false;
    }

    $original_attachments = wrappers_attachments_get_by_post($smarty,
                                                             $board,
                                                             $post,
                                                             $posts_attachments,
                                                             $attachments);
    $post['ip'] = long2ip($post['ip']);
    if ($enable_geoip && $post['ip'] != '127.0.0.1' && strpos($post['ip'], '192.168') === false) {
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
    $smarty->assign('enable_anchor', $enable_anchor);
    $smarty->assign('enable_remove_post', $enable_remove_post);
    $smarty->assign('enable_extrabtns', $enable_extrabtns);
    $smarty->assign('enable_geoip', $enable_geoip);
    $smarty->assign('enable_postid', $enable_postid);
    $smarty->assign('is_admin', is_admin());
    $smarty->assign('enable_translation', is_translation_enabled($board));
    $smarty->assign('post', $post);
    $smarty->assign('attachments', $original_attachments);
    $smarty->assign('show_reply', $show_reply);
    if ($enable_postid) {
        $postid = calculate_tripcode("#{$post['ip']}");
        $smarty->assign('postid', $postid[1]);
    }
    $smarty->assign('author_admin', $author_admin);
    return $smarty->fetch('post_original.tpl');
}
/**
 *
 */
function post_search_generate_html($smarty, &$post, $author_admin) {

    $post['ip'] = long2ip($post['ip']);
    if (is_geoip_enabled($post['board']) && $post['ip'] != '127.0.0.1' && strpos($post['ip'], '192.168') === false) {
        $geoip = geoip_record_by_name($post['ip']);
        $smarty->assign('country',
                        array('name' => $geoip['country_name'],
                              'code' => strtolower($geoip['country_code'])));
    }

    if (is_postid_enabled($post['board'])) {
        $postid = calculate_tripcode("#{$post['ip']}");
        $smarty->assign('postid', $postid[1]);
    }

    $smarty->assign('author_admin', $author_admin);

    $smarty->assign('post', $post);
    $smarty->assign('enable_translation', is_translation_enabled($post['board']));

    return $smarty->fetch('search_post.tpl');
}
/**
 * 
 */
function post_report_generate_html($smarty,
                                   &$post,
                                   $posts_attachments,
                                   $attachments,
                                   $author_admin) {

    $post_attachments = wrappers_attachments_get_by_post($smarty,
                                                         $post['board'],
                                                         $post,
                                                         $posts_attachments,
                                                         $attachments);
    $post['ip'] = long2ip($post['ip']);
    $smarty->assign('post', $post);
    $smarty->assign('author_admin', $author_admin);
    $smarty->assign('attachments', $post_attachments);
    $smarty->assign('enable_translation', is_translation_enabled($post['board']));
    return $smarty->fetch('reports_post.tpl');
}
/**
 * 
 */
function post_moderate_generate_html($smarty,
                                     &$post,
                                     $posts_attachments,
                                     $attachments,
                                     $author_admin) {

    $post_attachments = wrappers_attachments_get_by_post($smarty,
                                                         $post['board'],
                                                         $post,
                                                         $posts_attachments,
                                                         $attachments);
    $post['ip'] = long2ip($post['ip']);
    $smarty->assign('post', $post);
    $smarty->assign('author_admin', $author_admin);
    $smarty->assign('attachments', $post_attachments);
    $smarty->assign('enable_translation', is_translation_enabled($post['board']));
    return $smarty->fetch('reports_post.tpl');
}
/**
 * Get attachments of post.
 * @param SmartyKotobaSetup $smarty Template engine.
 * @param array $board Board.
 * @param array $post Post.
 * @param array $posts_attachments Posts attachemnts relations.
 * @param array $attachments Attachemnts.
 * @return array
 * attachments.
 */
function wrappers_attachments_get_by_post($smarty, $board, &$post, $posts_attachments, $attachments) {
    $desired_attachments = array();

    $post['with_attachments'] = false;
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
                                $a['deleted'] = $pa['deleted'];
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
                                $a['deleted'] = $pa['deleted'];
                                $post['with_attachments'] = true;
                                array_push($desired_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_LINK:
                            if ($a['id'] == $pa['link']) {
                                $a['deleted'] = $pa['deleted'];
                                $post['with_attachments'] = true;
                                array_push($desired_attachments, $a);
                            }
                            break;
                        case Config::ATTACHMENT_TYPE_VIDEO:
                            if ($a['id'] == $pa['video']) {
                                $a['deleted'] = $pa['deleted'];
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
