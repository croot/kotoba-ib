<?php

/***********************************
 * Edit constants after this line. *
 ***********************************/

// Source directory. Directory where working copy of Kotoba engine will be
// created.
$SRC_DIR = "/tmp/kotoba";

// Destination directory. Directory where engine will actually work.
$DST_DIR = "/var/www/html/kotoba";

// Path from server document root to kotoba directory.
$KOTOBA_DOC_ROOT = "/kotoba";

// Mysql user name.
$MYSQL_USR = "root";

// Mysql password. Empty string means no password.
$MYSQL_PASWD = "";

// Set this value to 1.
$CONFIRM_CHANGES = 0;

/*************************************
 * Do not edit code after this line. *
 *************************************/

// Debug installation script.
$DEBUG = 0;

if ($CONFIRM_CHANGES != 1) {
    echo "Edit constants in this file first. And then start again.\n";
    exit(1);
}

echo "SRC_DIR=$SRC_DIR\n";
echo "DST_DIR=$DST_DIR\n";
echo "KOTOBA_DOC_ROOT=$KOTOBA_DOC_ROOT\n";
echo "MYSQL_USR=$MYSQL_USR\n";
echo "MYSQL_PASWD=$MYSQL_PASWD\n";
echo "DEBUG=$DEBUG\n";

/**************
 * Constants. *
 **************/

$R__ = '400';
$RWX = '700';

$SCRIPTS_TREE = array(
    'admin' => array(
        'archive.php',
        'delete_dangling_attachments.php',
        'delete_marked.php',
        'edit_acl.php',
        'edit_bans.php',
        'edit_boards.php',
        'edit_board_upload_types.php',
        'edit_categories.php',
        'edit_groups.php',
        'edit_languages.php',
        'edit_popdown_handlers.php',
        'edit_spamfilter.php',
        'edit_stylesheets.php',
        'edit_threads.php',
        'edit_upload_handlers.php',
        'edit_upload_types.php',
        'edit_user_groups.php',
        'edit_words.php',
        'hard_ban.php',
        'log_view.php',
        'mass_ban.php',
        'moderate.php',
        'move_thread.php',
        'reports.php',
        'update_macrochan.php'
    ),
    'animaptcha' => array(
        'animaptcha.php',
        'boxxy001.png'
    ),
    'boards.php',
    'captcha' => array(
        'image.php'
    ),
    'catalog.php',
    'create_thread.php',
    'css' => array(
        'global.css',
        'kusaba.css' => array(
            'closed.png',
            'delete.png',
            'delfile.png',
            'favorites.png',
            'hide.png',
            'kusaba.css',
            'kusaba_menu.css',
            'report.png',
            'sticky.png'
        ),
    ),
    'edit_settings.php',
    'favorites.php',
    'flower.png',
    'hide_thread.php',
    'img' => array(
        'deleted.png',
        'errors' => array(
            'board_not_found.png',
            'default_error.png'
        ),
        'spoiler.png'
    ),
    'index.php',
    'kotoba.js',
    'latexcache' => array(),
    'lib' => array(
        'db.php',
        'errors.php',
        'events.php',
        'exceptions.php',
        'kgettext.php',
        'latex_render.php',
        'logging.php',
        'mark.php',
        'misc.php',
        'mysql.php',
        'popdown_handlers.php',
        'shi_applet.php',
        'shi_exit.php',
        'upload_handlers.php',
        'wrappers.php'
    ),
    'license.txt',
    'locale' => array(
        'eng' => array(
            'errors.php',
            'exceptions.php',
            'logging.php',
            'messages.php'
        ),
        'rus' => array(
            'errors.php',
            'exceptions.php',
            'logging.php',
            'messages.php'
        )
    ),
    'log' => array(),
    'logout.php',
    'manage.php',
    'my_id.php',
    'menu.php',
    'news.php',
    'over.php',
    'post.php',
    'protoaculous-compressed.js',
    'remove_post.php',
    'remove_upload.php',
    'reply.php',
    'report.php',
    'search.php',
    'sessions' => array(),
    'shi' => array(),
    'smarty' => array(
        'kotoba' => array(
            'cache' => array(),
            'configs' => array(),
            'templates' => array(
                'locale' => array(
                    'eng' => array(
                        'adminbar.tpl',
                        'adm_panel.tpl',
                        'banned.tpl',
                        'board_list.tpl',
                        'board_view.tpl',
                        'catalog_pages_list.tpl',
                        'catalog_thread.tpl',
                        'catalog.tpl',
                        'delete_dangling_files.tpl',
                        'edit_acl.tpl',
                        'edit_bans.tpl',
                        'edit_boards.tpl',
                        'edit_board_upload_types.tpl',
                        'edit_categories.tpl',
                        'edit_groups.tpl',
                        'edit_languages.tpl',
                        'edit_popdown_handlers.tpl',
                        'edit_settings.tpl',
                        'edit_spamfilter.tpl',
                        'edit_stylesheets.tpl',
                        'edit_threads_pages_list.tpl',
                        'edit_threads.tpl',
                        'edit_upload_handlers.tpl',
                        'edit_upload_types.tpl',
                        'edit_user_groups.tpl',
                        'edit_words.tpl',
                        'error.tpl',
                        'exception.tpl',
                        'footer.tpl',
                        'hard_ban.tpl',
                        'header.tpl',
                        'index.tpl',
                        'log_view.tpl',
                        'manage.tpl',
                        'mass_ban.tpl',
                        'menu.tpl',
                        'moderate_pages_list.tpl',
                        'moderate_post.tpl',
                        'moderate.tpl',
                        'mod_mini_panel.tpl',
                        'mod_panel.tpl',
                        'move_thread.tpl',
                        'my_id.tpl',
                        'navbar.tpl',
                        'news.tpl',
                        'pages_list.tpl',
                        'post_original_archive.tpl',
                        'post_original.tpl',
                        'post_simple_archive.tpl',
                        'post_simple.tpl',
                        'remove_attachment.tpl',
                        'remove_post.tpl',
                        'reports_pages_list.tpl',
                        'reports_post.tpl',
                        'reports.tpl',
                        'report.tpl',
                        'same_attachments.tpl',
                        'search_pages_list.tpl',
                        'search_post.tpl',
                        'search.tpl',
                        'shi_applet.tpl',
                        'thread_archive.tpl',
                        'threads_settings_list.tpl',
                        'thread.tpl',
                        'thread_view.tpl',
                        'uwb4tp.tpl',
                        'youtube.tpl'
                    ),
                    'rus' => array(
                        'adminbar.tpl',
                        'adm_panel.tpl',
                        'banned.tpl',
                        'board_list.tpl',
                        'board_view.tpl',
                        'catalog_pages_list.tpl',
                        'catalog_thread.tpl',
                        'catalog.tpl',
                        'delete_dangling_files.tpl',
                        'edit_acl.tpl',
                        'edit_bans.tpl',
                        'edit_boards.tpl',
                        'edit_board_upload_types.tpl',
                        'edit_categories.tpl',
                        'edit_groups.tpl',
                        'edit_languages.tpl',
                        'edit_popdown_handlers.tpl',
                        'edit_settings.tpl',
                        'edit_spamfilter.tpl',
                        'edit_stylesheets.tpl',
                        'edit_threads_pages_list.tpl',
                        'edit_threads.tpl',
                        'edit_upload_handlers.tpl',
                        'edit_upload_types.tpl',
                        'edit_user_groups.tpl',
                        'edit_words.tpl',
                        'error.tpl',
                        'exception.tpl',
                        'footer.tpl',
                        'hard_ban.tpl',
                        'header.tpl',
                        'index.tpl',
                        'log_view.tpl',
                        'manage.tpl',
                        'mass_ban.tpl',
                        'menu.tpl',
                        'moderate_pages_list.tpl',
                        'moderate_post.tpl',
                        'moderate.tpl',
                        'mod_mini_panel.tpl',
                        'mod_panel.tpl',
                        'move_thread.tpl',
                        'my_id.tpl',
                        'navbar.tpl',
                        'news.tpl',
                        'pages_list.tpl',
                        'post_original_archive.tpl',
                        'post_original.tpl',
                        'post_simple_archive.tpl',
                        'post_simple.tpl',
                        'remove_attachment.tpl',
                        'remove_post.tpl',
                        'reports_pages_list.tpl',
                        'reports_post.tpl',
                        'reports.tpl',
                        'report.tpl',
                        'same_attachments.tpl',
                        'search_pages_list.tpl',
                        'search_post.tpl',
                        'search.tpl',
                        'shi_applet.tpl',
                        'thread_archive.tpl',
                        'threads_settings_list.tpl',
                        'thread.tpl',
                        'thread_view.tpl',
                        'uwb4tp.tpl',
                        'youtube.tpl'
                    )
                ),
            ),
            'templates_c' => array()
        ),
    ),
    'threads.php',
    'unhide_thread.php'
);

/**************
 * Functions. *
 **************/

function print_cleanup_info() {
    global $SRC_DIR, $DST_DIR, $MYSQL_USR;

    echo "You can manually clean up: ";
    echo "rm -rf $SRC_DIR/* /tmp/Smarty-3.0.4* /tmp/shi*\n";
    echo "Remove installation: ";
    echo "rm -rf $DST_DIR/*\n";
    echo "And drop database: ";
    echo "mysql -u $MYSQL_USR -e \"drop database kotoba\"\n";
}

function k_error() {
    echo "Installation failed in:\n";
    debug_print_backtrace();
    print_cleanup_info();
    exit(1);
}

function k_exec($command) {
    global $DEBUG;
    exec($command, $output, $return_var);
    if ($DEBUG) {
        echo "Execute command $command\n";
    }
    if ($return_var != 0) {
        k_error();
    }
}

function k_node($node, $path) {
    global $SRC_DIR, $DST_DIR, $opt_dir, $opt_file;

    foreach ($node as $key => $value) {
        if (is_array($value)) {
            $dst = $path ? "$DST_DIR/$path/$key" : "$DST_DIR/$key";
            k_exec("install $opt_dir -d $dst");
            $path ? k_node($value, "$path/$key") : k_node($value, "$key");
        } else {
            $src = $path ? "$SRC_DIR/$path/$value" : "$SRC_DIR/$value";
            $dst = $path ? "$DST_DIR/$path/$value" : "$DST_DIR/$value";
            k_exec("install $opt_file $src $dst");
        }
    }
}

/**************
 * Main code. *
 **************/

$_ = function ($arg) {
    $res = false;
    foreach (array_slice(func_get_args(), 1) as $func) {
        if ( ($res = $res || !$func($arg))) {
            break;
        }
    }
    return $res;
};

/***********************
 * 1. Validate pathes. *
 ***********************/

echo "Validate pathes.\n";
if ($_($SRC_DIR, 'file_exists', 'is_dir', 'is_readable')) {
    k_error();
}
if ($_($DST_DIR, 'file_exists', 'is_dir', 'is_readable', 'is_writable')) {
    k_error();
}
if ($_('/tmp', 'file_exists', 'is_dir', 'is_readable', 'is_writable')) {
    k_error();
}

/************************************
 * 2. Check for required libraries. *
 ************************************/

echo "Check for required libraries.\n";
if (!function_exists('mb_language')) {
    echo "Required library mb_strings not installed or not configured.\n";
    echo "In Fedora package calls php-mbstring.\n";
    k_error();
}

/***************************************
 * 2. Read apache user name and group. *
 ***************************************/

echo "Read apache user name and group.\n";
if ($_('/etc/httpd/conf/httpd.conf', 'file_exists', 'is_readable')) {
    k_error();
}
if ( !($httpdconf = file_get_contents('/etc/httpd/conf/httpd.conf'))) {
    k_error();
}
if (!preg_match('/\nUser (.+?)\n/', $httpdconf, $matches)) {
    k_error();
}
$APACHE_U = $matches[1];
if (!preg_match('/\nGroup (.+?)\n/', $httpdconf, $matches)) {
    k_error();
}
$APACHE_G = $matches[1];
$opt_dir  = "-o $APACHE_U -g $APACHE_G -m $RWX";
$opt_file = "-o $APACHE_U -g $APACHE_G -m $R__";

/********************************************
 * 3. Get working copy to source directory. *
 ********************************************/

echo "Get working copy to $SRC_DIR direcrory.\n";
k_exec("svn checkout https://kotoba-ib.googlecode.com/svn/trunk/kotoba-ib/ "
       . "$SRC_DIR/");

/***********************
 * 4. Create database. *
 ***********************/

echo "Create database.\n";
k_exec("mysql -u $MYSQL_USR "
           . ($MYSQL_PASWD != "" ? "-p $MYSQL_PASWD " : "")
           . "< $SRC_DIR/res/create_struct.sql");
k_exec("mysql -u $MYSQL_USR "
           . ($MYSQL_PASWD != "" ? "-p $MYSQL_PASWD " : "")
           . "-D kotoba < $SRC_DIR/res/create_procedures.sql");

/****************************************
 * 5. Copy files and setup permissions. *
 ****************************************/

echo "Copy files and setup permissions.\n";
k_exec("install $opt_file $SRC_DIR/config.default $DST_DIR/config.php");
k_node($SCRIPTS_TREE, '');

/***********************************
 * 6. Download and install smarty. *
 ***********************************/

echo "Download and install smarty.\n";
k_exec("wget -P /tmp/ http://www.smarty.net/files/Smarty-3.0.4.tar.gz");
k_exec("tar -zxvf /tmp/Smarty-3.0.4.tar.gz -C /tmp/");
k_exec("cp -r /tmp/Smarty-3.0.4/* $DST_DIR/smarty/");

/********************************
 * 7. Download and install shi. *
 ********************************/

echo "Download and install shi.\n";
k_exec("wget -P /tmp/ http://kotoba-ib.googlecode.com/files/shi-1320754922.7z");
k_exec("7z x -o/tmp /tmp/shi-1320754922.7z");
k_exec("install $opt_file /tmp/shi/shi_save.php $DST_DIR/lib/");
foreach (array('spainter_all.jar',
               'spainter_normal.html',
               'sp.js') as $file) {

    k_exec("install $opt_file /tmp/shi/$file $DST_DIR/shi/");
}
k_exec("install $opt_dir -d $DST_DIR/shi/res");
foreach (array('bn.gif',
               'c1x16xy16y.gif',
               'normal.zip',
               'pro.zip',
               'res_ca.txt',
               'res_en.txt',
               'res_es.txt',
               'res_fr.txt',
               'res_it.txt',
               'res_ja.txt',
               'res_ko.txt',
               'res_normal.zip',
               'res_pro.zip',
               'res.txt',
               'res_zh.txt',
               'tt.zip') as $file) {

    k_exec("install $opt_file /tmp/shi/res/$file $DST_DIR/shi/res/");
}

/************************
 * 8. Create .htaccess. *
 ************************/

echo "Create .htaccess.\n";
k_exec("install $opt_file $SRC_DIR/res/.htaccess1 $DST_DIR/.htaccess");
k_exec("install $opt_file $SRC_DIR/res/.htaccess2 $DST_DIR/smarty/.htaccess");
k_exec("install $opt_file $SRC_DIR/res/.htaccess2 $DST_DIR/log/.htaccess");
k_exec("install $opt_file $SRC_DIR/res/.htaccess2 $DST_DIR/sessions/.htaccess");

/********************************************
 * 9. Add requied directives to httpd.conf. *
 ********************************************/

echo "Add requied directives to httpd.conf.\n";
$data = "

### Kotoba
#
# Options requied or recommended for Kotoba.

<Directory \"$DST_DIR\">
    AllowOverride FileInfo Limit Indexes
</Directory>
";
$res = file_put_contents("/etc/httpd/conf/httpd.conf", $data, FILE_APPEND);
if (!$res) {
    k_error();
}

/*********************************
 * 10. Grant $DST_DIR to Apache. *
 *********************************/

echo "Grant $DST_DIR to Apache.\n";
k_exec("chown $APACHE_U:$APACHE_G $DST_DIR");

/*****************
 * 11. Epilogue. *
 *****************/

echo "**********************************************************************\n";
echo "Installations successful.\n";
print_cleanup_info();
echo "**********************************************************************\n";
echo "Next steps:\n";
echo "1. Edit $DST_DIR/config.php\n";
echo "2. Open Settings page of Kotoba in your browser:\n";
echo "   http://your-server-domain-or-ip$KOTOBA_DOC_ROOT/edit_settings.php\n";
echo "3. Edit settings what you like and save it.\n";
echo "4. Open hiden Kotoba info page in your browser:\n";
echo "   http://your-server-domain-or-ip$KOTOBA_DOC_ROOT/my_id.php\n";
echo "5. You will see your id. For example 2.\n";
echo "6. Now move your user into admin group by executing:\n";
echo "   mysql -u root -D kotoba -e \"update \`user_groups\` set " .
     "\`group\` = 4 where \`user\` = yourid\"\n";
echo "   In case of your id is 2:\n";
echo "   mysql -u root -D kotoba -e \"update \`user_groups\` set " .
     "\`group\` = 4 where \`user\` = 2\"\n";
echo "7. Go back to settings page and load your settings. Then you will see " .
     "new Manage menu on top right of the page.\n";
echo "Good luck!\n";
echo "**********************************************************************\n";
?>
