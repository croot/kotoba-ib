<?php
$SVN_PATH = $argv[1];
$WEB_PATH = $argv[2];
$DEBUG = isset($argv[3]) ? $argv[3] : 0;

$default_check = array(function ($c) {
                           global $DEBUG;
                           $command = "diff \"{$c['s']}\" \"{$c['d']}\"";

                           if ($DEBUG) {
                               echo "Executing $command\n";
                           }
                           exec($command, $output, $return_var);
                           return $return_var == 1;
                       });

$default_copy = array(function ($c) {
                          $command = "install -o {$c['u']} -g {$c['g']} -m {$c['m']} \"{$c['s']}\" \"{$c['d']}\"";

                          echo "$command\n";
                          exec($command, $output, $return_var);
                          if ($return_var != 0) {
                              die("Error executing $command\n");
                          }
                      });

$default_copy_check = array('check' => array($default_check, array()),
                            'copy' => array($default_copy, array()));

$tree = array('admin' => array('archive.php' => $default_copy_check,
                               'delete_dangling_attachments.php' => $default_copy_check,
                               'delete_marked.php' => $default_copy_check,
                               'edit_acl.php' => $default_copy_check,
                               'edit_bans.php' => $default_copy_check,
                               'edit_boards.php' => $default_copy_check,
                               'edit_board_upload_types.php' => $default_copy_check,
                               'edit_categories.php' => $default_copy_check,
                               'edit_groups.php' => $default_copy_check,
                               'edit_languages.php' => $default_copy_check,
                               'edit_popdown_handlers.php' => $default_copy_check,
                               'edit_spamfilter.php' => $default_copy_check,
                               'edit_stylesheets.php' => $default_copy_check,
                               'edit_threads.php' => $default_copy_check,
                               'edit_upload_handlers.php' => $default_copy_check,
                               'edit_upload_types.php' => $default_copy_check,
                               'edit_user_groups.php' => $default_copy_check,
                               'edit_words.php' => $default_copy_check,
                               'hard_ban.php' => $default_copy_check,
                               'log_view.php' => $default_copy_check,
                               'mass_ban.php' => $default_copy_check,
                               'moderate.php' => $default_copy_check,
                               'move_thread.php' => $default_copy_check,
                               'reports.php' => $default_copy_check,
                               'update_macrochan.php' => $default_copy_check),
              'animaptcha' => array('animaptcha.php' => $default_copy_check),
              'captcha' => array('image.php' => $default_copy_check),
              'css' => array('futaba.css' => array('futaba.css' => $default_copy_check),
                             'kusaba.css' => array('kusaba.css' => $default_copy_check),
                             'kotoba.css' => array('kotoba.css' => $default_copy_check),
                             'global.css' => $default_copy_check),
              'lib' => array('db.php' => $default_copy_check,
                             'errors.php' => $default_copy_check,
                             'events.php' => $default_copy_check,
                             'exceptions.php' => $default_copy_check,
                             'latex_render.php' => $default_copy_check,
                             'logging.php' => $default_copy_check,
                             'mark.php' => $default_copy_check,
                             'misc.php' => $default_copy_check,
                             'mysql.php' => $default_copy_check,
                             'popdown_handlers.php' => $default_copy_check,
                             'shi_applet.php' => $default_copy_check,
                             'shi_exit.php' => $default_copy_check,
                             'shi_save.php' => $default_copy_check,
                             'upload_handlers.php' => $default_copy_check,
                             'wrappers.php' => $default_copy_check),
              'locale' => array('eng' => array('errors.php' => $default_copy_check,
                                               'exceptions.php' => $default_copy_check,
                                               'logging.php' => $default_copy_check),
                                'rus' => array('errors.php' => $default_copy_check,
                                               'exceptions.php' => $default_copy_check,
                                               'logging.php' => $default_copy_check)),
              'smarty' => array('kotoba' => array('templates' => array('locale' => array('eng' => array('adminbar.tpl' => $default_copy_check,
                                                                                                        'adm_panel.tpl' => $default_copy_check,
                                                                                                        'banned.tpl' => $default_copy_check,
                                                                                                        'board_list.tpl' => $default_copy_check,
                                                                                                        'board_view.tpl' => $default_copy_check,
                                                                                                        'catalog_thread.tpl' => $default_copy_check,
                                                                                                        'catalog.tpl' => $default_copy_check,
                                                                                                        'catalog_pages_list.tpl' => $default_copy_check,
                                                                                                        'delete_dangling_files.tpl' => $default_copy_check,
                                                                                                        'edit_acl.tpl' => $default_copy_check,
                                                                                                        'edit_bans.tpl' => $default_copy_check,
                                                                                                        'edit_boards.tpl' => $default_copy_check,
                                                                                                        'edit_board_upload_types.tpl' => $default_copy_check,
                                                                                                        'edit_categories.tpl' => $default_copy_check,
                                                                                                        'edit_groups.tpl' => $default_copy_check,
                                                                                                        'edit_languages.tpl' => $default_copy_check,
                                                                                                        'edit_popdown_handlers.tpl' => $default_copy_check,
                                                                                                        'edit_settings.tpl' => $default_copy_check,
                                                                                                        'edit_spamfilter.tpl' => $default_copy_check,
                                                                                                        'edit_stylesheets.tpl' => $default_copy_check,
                                                                                                        'edit_threads_pages_list.tpl' => $default_copy_check,
                                                                                                        'edit_threads.tpl' => $default_copy_check,
                                                                                                        'edit_upload_handlers.tpl' => $default_copy_check,
                                                                                                        'edit_upload_types.tpl' => $default_copy_check,
                                                                                                        'edit_user_groups.tpl' => $default_copy_check,
                                                                                                        'edit_words.tpl' => $default_copy_check,
                                                                                                        'error.tpl' => $default_copy_check,
                                                                                                        'exception.tpl' => $default_copy_check,
                                                                                                        'footer.tpl' => $default_copy_check,
                                                                                                        'hard_ban.tpl' => $default_copy_check,
                                                                                                        'header.tpl' => $default_copy_check,
                                                                                                        'index.tpl' => $default_copy_check,
                                                                                                        'log_view.tpl' => $default_copy_check,
                                                                                                        'manage.tpl' => $default_copy_check,
                                                                                                        'mass_ban.tpl' => $default_copy_check,
                                                                                                        'moderate_pages_list.tpl' => $default_copy_check,
                                                                                                        'moderate_post.tpl' => $default_copy_check,
                                                                                                        'moderate.tpl' => $default_copy_check,
                                                                                                        'mod_mini_panel.tpl' => $default_copy_check,
                                                                                                        'mod_panel.tpl' => $default_copy_check,
                                                                                                        'move_thread.tpl' => $default_copy_check,
                                                                                                        'my_id.tpl' => $default_copy_check,
                                                                                                        'navbar.tpl' => $default_copy_check,
                                                                                                        'pages_list.tpl' => $default_copy_check,
                                                                                                        'post_original_archive.tpl' => $default_copy_check,
                                                                                                        'post_original.tpl' => $default_copy_check,
                                                                                                        'post_simple_archive.tpl' => $default_copy_check,
                                                                                                        'post_simple.tpl' => $default_copy_check,
                                                                                                        'remove_attachment.tpl' => $default_copy_check,
                                                                                                        'remove_post.tpl' => $default_copy_check,
                                                                                                        'reports_pages_list.tpl' => $default_copy_check,
                                                                                                        'reports_post.tpl' => $default_copy_check,
                                                                                                        'reports.tpl' => $default_copy_check,
                                                                                                        'report.tpl' => $default_copy_check,
                                                                                                        'same_attachments.tpl' => $default_copy_check,
                                                                                                        'search_pages_list.tpl' => $default_copy_check,
                                                                                                        'search_post.tpl' => $default_copy_check,
                                                                                                        'search.tpl' => $default_copy_check,
                                                                                                        'shi_applet.tpl' => $default_copy_check,
                                                                                                        'thread_archive.tpl' => $default_copy_check,
                                                                                                        'threads_settings_list.tpl' => $default_copy_check,
                                                                                                        'thread.tpl' => $default_copy_check,
                                                                                                        'thread_view.tpl' => $default_copy_check,
                                                                                                        'uwb4tp.tpl' => $default_copy_check,
                                                                                                        'youtube.tpl' => $default_copy_check),
                                                                                         'rus' => array('adminbar.tpl' => $default_copy_check,
                                                                                                        'adm_panel.tpl' => $default_copy_check,
                                                                                                        'banned.tpl' => $default_copy_check,
                                                                                                        'board_list.tpl' => $default_copy_check,
                                                                                                        'board_view.tpl' => $default_copy_check,
                                                                                                        'catalog_thread.tpl' => $default_copy_check,
                                                                                                        'catalog.tpl' => $default_copy_check,
                                                                                                        'catalog_pages_list.tpl' => $default_copy_check,
                                                                                                        'delete_dangling_files.tpl' => $default_copy_check,
                                                                                                        'edit_acl.tpl' => $default_copy_check,
                                                                                                        'edit_bans.tpl' => $default_copy_check,
                                                                                                        'edit_boards.tpl' => $default_copy_check,
                                                                                                        'edit_board_upload_types.tpl' => $default_copy_check,
                                                                                                        'edit_categories.tpl' => $default_copy_check,
                                                                                                        'edit_groups.tpl' => $default_copy_check,
                                                                                                        'edit_languages.tpl' => $default_copy_check,
                                                                                                        'edit_popdown_handlers.tpl' => $default_copy_check,
                                                                                                        'edit_settings.tpl' => $default_copy_check,
                                                                                                        'edit_spamfilter.tpl' => $default_copy_check,
                                                                                                        'edit_stylesheets.tpl' => $default_copy_check,
                                                                                                        'edit_threads_pages_list.tpl' => $default_copy_check,
                                                                                                        'edit_threads.tpl' => $default_copy_check,
                                                                                                        'edit_upload_handlers.tpl' => $default_copy_check,
                                                                                                        'edit_upload_types.tpl' => $default_copy_check,
                                                                                                        'edit_user_groups.tpl' => $default_copy_check,
                                                                                                        'edit_words.tpl' => $default_copy_check,
                                                                                                        'error.tpl' => $default_copy_check,
                                                                                                        'exception.tpl' => $default_copy_check,
                                                                                                        'footer.tpl' => $default_copy_check,
                                                                                                        'hard_ban.tpl' => $default_copy_check,
                                                                                                        'header.tpl' => $default_copy_check,
                                                                                                        'index.tpl' => $default_copy_check,
                                                                                                        'log_view.tpl' => $default_copy_check,
                                                                                                        'manage.tpl' => $default_copy_check,
                                                                                                        'mass_ban.tpl' => $default_copy_check,
                                                                                                        'moderate_pages_list.tpl' => $default_copy_check,
                                                                                                        'moderate_post.tpl' => $default_copy_check,
                                                                                                        'moderate.tpl' => $default_copy_check,
                                                                                                        'mod_mini_panel.tpl' => $default_copy_check,
                                                                                                        'mod_panel.tpl' => $default_copy_check,
                                                                                                        'move_thread.tpl' => $default_copy_check,
                                                                                                        'my_id.tpl' => $default_copy_check,
                                                                                                        'navbar.tpl' => $default_copy_check,
                                                                                                        'pages_list.tpl' => $default_copy_check,
                                                                                                        'post_original_archive.tpl' => $default_copy_check,
                                                                                                        'post_original.tpl' => $default_copy_check,
                                                                                                        'post_simple_archive.tpl' => $default_copy_check,
                                                                                                        'post_simple.tpl' => $default_copy_check,
                                                                                                        'remove_attachment.tpl' => $default_copy_check,
                                                                                                        'remove_post.tpl' => $default_copy_check,
                                                                                                        'reports_pages_list.tpl' => $default_copy_check,
                                                                                                        'reports_post.tpl' => $default_copy_check,
                                                                                                        'reports.tpl' => $default_copy_check,
                                                                                                        'report.tpl' => $default_copy_check,
                                                                                                        'same_attachments.tpl' => $default_copy_check,
                                                                                                        'search_pages_list.tpl' => $default_copy_check,
                                                                                                        'search_post.tpl' => $default_copy_check,
                                                                                                        'search.tpl' => $default_copy_check,
                                                                                                        'shi_applet.tpl' => $default_copy_check,
                                                                                                        'thread_archive.tpl' => $default_copy_check,
                                                                                                        'threads_settings_list.tpl' => $default_copy_check,
                                                                                                        'thread.tpl' => $default_copy_check,
                                                                                                        'thread_view.tpl' => $default_copy_check,
                                                                                                        'uwb4tp.tpl' => $default_copy_check,
                                                                                                        'youtube.tpl' => $default_copy_check))))),
              'boards.php' => $default_copy_check,
              'catalog.php' => $default_copy_check,
              'create_thread.php' => $default_copy_check,
              'edit_settings.php' => $default_copy_check,
              'favorites.php' => $default_copy_check,
              'hide_thread.php' => $default_copy_check,
              'index.php' => $default_copy_check,
              'kotoba.js' => $default_copy_check,
              'license.txt' => $default_copy_check,
              'logout.php' => $default_copy_check,
              'manage.php' => $default_copy_check,
              'my_id.php' => $default_copy_check,
              'over.php' => $default_copy_check,
              'post.php' => $default_copy_check,
              'protoaculous-compressed.js' => $default_copy_check,
              'remove_post.php' => $default_copy_check,
              'remove_upload.php' => $default_copy_check,
              'reply.php' => $default_copy_check,
              'report.php' => $default_copy_check,
              'search.php' => $default_copy_check,
              'threads.php' => $default_copy_check,
              'unhide_thread.php' => $default_copy_check);

function walk_trough($node, $route) {
    global $SVN_PATH, $WEB_PATH;

    foreach ($node as $key => $value) {
        if (isset($value['check']) && isset($value['copy'])) {
            $pass = true;
            $c = array('s' => "{$SVN_PATH}$route/$key",
                       'd' => "{$WEB_PATH}$route/$key",
                       'ug' => "apache:apache",
                       'u' => "apache",
                       'g' => "apache",
                       'm' => "400");
            foreach ($value['check'] as $check) {
                foreach ($check as $command) {
                    $pass = $pass && $command($c);
                }
            }
            if ($pass) {
                foreach ($value['copy'] as $copy) {
                    foreach ($copy as $command) {
                        $command($c);
                    }
                }
            }
        } else {
            walk_trough($value, "$route/$key");
        }
    }
}

walk_trough($tree, '');
?>
