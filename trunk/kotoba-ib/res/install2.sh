#!/bin/bash

########## Edit constants after this line ######################################

# Source directory. Directory where working copy of Kotoba engine will be
# created.
SRC_DIR="/home/sauce/src/kotoba"

# Destination directory. Directory where engine will actually work.
DST_DIR="/var/www/html/kotoba"

# Path from server document root to kotoba directory.
KOTOBA_DOC_ROOT="/kotoba"

# Apache user name and group.
APACHE_UG="apache:apache"

# Mysql user name.
MYSQL_USR="root"

# Mysql password. Empty string means no password.
MYSQL_PASWD=""

# Debug installation script.
DEBUG=0

# Set this value to 1
CONFIRM_CHANGES=0

########## Do not edit code after this line ####################################

if [ $CONFIRM_CHANGES -ne 1 ]; then
    echo "Edit constants in this file first. And then start again."
    exit 1
fi

################################################################################
# Constants
#

MAIN_SCRIPTS="$SRC_DIR/boards.php \
              $SRC_DIR/catalog.php \
              $SRC_DIR/config.default \
              $SRC_DIR/config.php \
              $SRC_DIR/create_thread.php \
              $SRC_DIR/edit_settings.php \
              $SRC_DIR/favorites.php \
              $SRC_DIR/flower.png \
              $SRC_DIR/hide_thread.php \
              $SRC_DIR/index.php \
              $SRC_DIR/kotoba.js \
              $SRC_DIR/license.txt \
              $SRC_DIR/logout.php \
              $SRC_DIR/manage.php \
              $SRC_DIR/my_id.php \
              $SRC_DIR/over.php \
              $SRC_DIR/post.php \
              $SRC_DIR/protoaculous-compressed.js \
              $SRC_DIR/remove_post.php \
              $SRC_DIR/remove_upload.php \
              $SRC_DIR/reply.php \
              $SRC_DIR/report.php \
              $SRC_DIR/search.php \
              $SRC_DIR/threads.php \
              $SRC_DIR/unhide_thread.php"

ADMIN_DIR="$SRC_DIR/admin"
ADMIN_SCRIPTS="$ADMIN_DIR/archive.php \
               $ADMIN_DIR/delete_dangling_attachments.php \
               $ADMIN_DIR/delete_marked.php \
               $ADMIN_DIR/edit_acl.php \
               $ADMIN_DIR/edit_bans.php \
               $ADMIN_DIR/edit_boards.php \
               $ADMIN_DIR/edit_board_upload_types.php \
               $ADMIN_DIR/edit_categories.php \
               $ADMIN_DIR/edit_groups.php \
               $ADMIN_DIR/edit_languages.php \
               $ADMIN_DIR/edit_popdown_handlers.php \
               $ADMIN_DIR/edit_spamfilter.php \
               $ADMIN_DIR/edit_stylesheets.php \
               $ADMIN_DIR/edit_threads.php \
               $ADMIN_DIR/edit_upload_handlers.php \
               $ADMIN_DIR/edit_upload_types.php \
               $ADMIN_DIR/edit_user_groups.php \
               $ADMIN_DIR/edit_words.php \
               $ADMIN_DIR/hard_ban.php \
               $ADMIN_DIR/log_view.php \
               $ADMIN_DIR/mass_ban.php \
               $ADMIN_DIR/moderate.php \
               $ADMIN_DIR/move_thread.php \
               $ADMIN_DIR/reports.php \
               $ADMIN_DIR/update_macrochan.php"

ANIMAPTCHA_DIR="$SRC_DIR/animaptcha"
ANIMAPTCHA_SCRIPTS="$ANIMAPTCHA_DIR/animaptcha.php"

CAPTCHA_DIR="$SRC_DIR/captcha"
CAPTCHA_SCRIPTS="$CAPTCHA_DIR/image.php"

CSS_DIR="$SRC_DIR/css"
CSS_SCRIPTS="$CSS_DIR/global.css \
             $CSS_DIR/img_global.css"
CSS="futaba.css \
     kusaba.css"
CSS_ICONS="closed.png \
           delete.png \
           delfile.png \
           favorites.png \
           hide.png \
           report.png \
           sticky.png"

DEFAULT_IMAGES_DIR="$SRC_DIR/img"
DEFAULT_IMAGES="$DEFAULT_IMAGES_DIR/deleted.png \
                $DEFAULT_IMAGES_DIR/spoiler.png"

LATEX_DIR="$SRC_DIR/latexcache"

LIB_DIR="$SRC_DIR/lib"
LIB_SCRIPTS="$LIB_DIR/db.php \
             $LIB_DIR/errors.php \
             $LIB_DIR/events.php \
             $LIB_DIR/latex_render.php \
             $LIB_DIR/logging.php \
             $LIB_DIR/mark.php \
             $LIB_DIR/misc.php \
             $LIB_DIR/mysql.php \
             $LIB_DIR/popdown_handlers.php \
             $LIB_DIR/shi_applet.php \
             $LIB_DIR/shi_exit.php \
             $LIB_DIR/shi_save.php \
             $LIB_DIR/upload_handlers.php \
             $LIB_DIR/wrappers.php"

LOCALE_DIR="$SRC_DIR/locale"
LOCALES="eng \
         rus"
LOCALE_SCRIPTS="errors.php \
                logging.php"

LOG_DIR="$SRC_DIR/log"

RESOURCE_DIR="$SRC_DIR/res"

SESSIONS_DIR="$SRC_DIR/sessions"

SHI_DIR="$SRC_DIR/shi"

SMARTY_DIR="$SRC_DIR/smarty"
SMARTY_DIRS="$SMARTY_DIR/kotoba \
             $SMARTY_DIR/kotoba/cache \
             $SMARTY_DIR/kotoba/configs \
             $SMARTY_DIR/kotoba/templates \
             $SMARTY_DIR/kotoba/templates/locale \
             $SMARTY_DIR/kotoba/templates_c \
             $SMARTY_DIR/libs"
TEMPLATES="adminbar.tpl \
           adm_panel.tpl \
           banned.tpl \
           board_list.tpl \
           board_view.tpl \
           catalog_thread.tpl \
           catalog.tpl \
           delete_dangling_files.tpl \
           edit_acl.tpl \
           edit_bans.tpl \
           edit_boards.tpl \
           edit_board_upload_types.tpl \
           edit_categories.tpl \
           edit_groups.tpl \
           edit_languages.tpl \
           edit_popdown_handlers.tpl \
           edit_settings.tpl \
           edit_spamfilter.tpl \
           edit_stylesheets.tpl \
           edit_threads_pages_list.tpl \
           edit_threads.tpl \
           edit_upload_handlers.tpl \
           edit_upload_types.tpl \
           edit_user_groups.tpl \
           edit_words.tpl \
           error.tpl \
           footer.tpl \
           hard_ban.tpl \
           header.tpl \
           index.tpl \
           log_view.tpl \
           manage.tpl \
           mass_ban.tpl \
           moderate_pages_list.tpl \
           moderate_post.tpl \
           moderate.tpl \
           mod_mini_panel.tpl \
           mod_panel.tpl \
           move_thread.tpl \
           my_id.tpl \
           navbar.tpl \
           pages_list.tpl \
           post_original_archive.tpl \
           post_original.tpl \
           post_simple_archive.tpl \
           post_simple.tpl \
           remove_attachment.tpl \
           remove_post.tpl \
           reports_pages_list.tpl \
           reports_post.tpl \
           reports.tpl \
           report.tpl \
           same_attachments.tpl \
           search_pages_list.tpl \
           search_post.tpl \
           search.tpl \
           shi_applet.tpl \
           thread_archive.tpl \
           threads_settings_list.tpl \
           thread.tpl \
           thread_view.tpl \
           uwb4tp.tpl \
           youtube.tpl"

RWX=700
R__=400

################################################################################
# Functions
#

# Check if exit code not 0 (success) and termitante script.
# $1 - exit code.
# $2 - information about command call place (file, line number).
function check_exitcode {
    if [ $1 -ne 0 ]; then
        echo "Script failed"
        echo "in $2"
        exit 1
    fi
}

# Execute command.
# $1 - command.
# $2 - information about command call place (file, line number).
function execute {
    if [ $DEBUG -eq 1 ]; then
        TABS=""
        echo "Execute command $1"
        for ((i=0; i<${#FUNCNAME[@]}; i++)); do
            echo -n "${TABS}in ${BASH_SOURCE[$i]}:${BASH_LINENO[$i]} "
            echo "${FUNCNAME[$i]}()"
            TABS="$TABS    "
        done
    fi

    $1
    check_exitcode $? $2

    return 0
}

# Check if file exists and read permissions granted.
# $1 - full file name.
function is_file_r {
    ret=1

    if [ -e $1 ]; then
        if [ -r $1 ]; then
            ret=0
            if [ $DEBUG -eq 1 ]; then
                TABS=""
                echo "File $1 is ok"
                for ((i=0; i<${#FUNCNAME[@]}; i++)); do
                    echo -n "${TABS}in ${BASH_SOURCE[$i]}:${BASH_LINENO[$i]} "
                    echo "${FUNCNAME[$i]}()"
                    TABS="$TABS    "
                done
            fi
        else
            echo "Have no permission to read file $1"
            ret=1
        fi
    else
        echo "File $1 not exist."
        ret=1
    fi

    return $ret
}

# Check if file exists and read,execute permissions granted.
# $1 - full file name.
function is_file_rx {
    ret=1

    if [ -e $1 ]; then
        if [ -r $1 ]; then
            if [ -x $1 ]; then
                ret=0
                if [ $DEBUG -eq 1 ]; then
                    TABS=""
                    echo "File $1 is ok"
                    for ((i=0; i<${#FUNCNAME[@]}; i++)); do
                        echo -n "${TABS}in ${BASH_SOURCE[$i]}:"
                        echo "${BASH_LINENO[$i]} ${FUNCNAME[$i]}()"
                        TABS="$TABS    "
                    done
                fi
            else
                echo "Have no permission to execute file $1"
                ret=1
            fi
        else
            echo "Have no permission to read file $1"
            ret=1
        fi
    else
        echo "File $1 not exist."
        ret=1
    fi

    return $ret
}

################################################################################
#
#

#
# 1. Validate pathes.
#
echo "Validate pathes."
execute "is_file_rx $SRC_DIR" "`basename $0`:$LINENO"
execute "is_file_r $DST_DIR" "`basename $0`:$LINENO"

#
# 2. Get working copy to source directory.
#
echo "Get working copy to $SRC_DIR direcrory."
execute "svn checkout https://kotoba-ib.googlecode.com/svn/trunk/kotoba-ib/ $SRC_DIR/" "`basename $0`:$LINENO"

#
# 3. Create database.
#
echo "Create database."
execute "mysql -u $MYSQL_USR `if ! [ -n "$MYSQL_PSWD" ]; then echo "-p $MYSQL_PSWD "; fi`< $RESOURCE_DIR/create_struct.sql" "`basename $0`:$LINENO"
execute "mysql -u $MYSQL_USR `if ! [ -n "$MYSQL_PSWD" ]; then echo "-p $MYSQL_PSWD "; fi`-D kotoba < $RESOURCE_DIR/create_procedures.sql" "`basename $0`:$LINENO"

#
# 4. Download smarty, unpack and patch.
#
echo "Download smarty, unpack and patch."
execute "wget -P /tmp/ http://www.smarty.net/files/Smarty-2.6.26.tar.gz" "`basename $0`:$LINENO"
execute "tar -zxvf /tmp/Smarty-2.6.26.tar.gz -C /tmp/ > /dev/null" "`basename $0`:$LINENO"
execute "cp -r /tmp/Smarty-2.6.26/libs/* $SMARTY_DIR/" "`basename $0`:$LINENO"

#
# 5. Add requied directives to httpd.conf.
#
echo "Add requied directives to httpd.conf."
echo "

### Kotoba
#
# Options requied or recommended for Kotoba.

<Directory \"$DST_DIR\">
    AllowOverride FileInfo Limit Indexes
</Directory>" >> /etc/httpd/conf/httpd.conf

#
# 6. Generate .htaccess.
#
echo "Generate .htaccess."
echo "DirectoryIndex index.php
RewriteEngine On
RewriteRule ^([\w]{1,16})/$ $2/boards.php?board=\$1 [NE,L]
RewriteRule ^([\w]{1,16})$ $2/boards.php?board=\$1 [NE,L]
RewriteRule ^([\w]{1,16})/([\d]+)/$ $2/threads.php?board=\$1&thread=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/([\d]+)$ $2/threads.php?board=\$1&thread=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/p([\d]+)/$ $2/boards.php?board=\$1&page=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/p([\d]+)$ $2/boards.php?board=\$1&page=\$2 [NE,L]" > $DST_DIR/.htaccess

#
# 7. Setup permissions.
#
echo "Setup permissions."

execute "chown $APACHE_UG $MAIN_SCRIPTS" "`basename $0`:$LINENO"
execute "chmod $R__ $MAIN_SCRIPTS" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $ADMIN_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $ADMIN_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $ADMIN_SCRIPTS" "`basename $0`:$LINENO"
execute "chmod $R__ $ADMIN_SCRIPTS" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $ANIMAPTCHA_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $ANIMAPTCHA_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $ANIMAPTCHA_SCRIPTS" "`basename $0`:$LINENO"
execute "chmod $R__ $ANIMAPTCHA_SCRIPTS" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $CAPTCHA_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $CAPTCHA_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $CAPTCHA_SCRIPTS" "`basename $0`:$LINENO"
execute "chmod $R__ $CAPTCHA_SCRIPTS" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $CSS_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $CSS_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $CSS_SCRIPTS" "`basename $0`:$LINENO"
execute "chmod $R__ $CSS_SCRIPTS" "`basename $0`:$LINENO"
for c in $CSS; do
    execute "chown $APACHE_UG $CSS_DIR/$c" "`basename $0`:$LINENO"
    execute "chmod $RWX $CSS_DIR/$c" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $CSS_DIR/$c/$c" "`basename $0`:$LINENO"
    execute "chmod $R__ $APACHE_UG $CSS_DIR/$c/$c" "`basename $0`:$LINENO"
    for i in $CSS_ICONS; do
        execute "chown $APACHE_UG $CSS_DIR/$c/$i" "`basename $0`:$LINENO"
        execute "chmod $R__ $APACHE_UG $CSS_DIR/$c/$i" "`basename $0`:$LINENO"
    done
done

execute "chown $APACHE_UG $DEFAULT_IMAGES_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DEFAULT_IMAGES_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DEFAULT_IMAGES" "`basename $0`:$LINENO"
execute "chmod $R__ $DEFAULT_IMAGES" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $LATEX_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $LATEX_DIR" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $LIB_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $LIB_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $LIB_SCRIPTS" "`basename $0`:$LINENO"
execute "chmod $R__ $LIB_SCRIPTS" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $LOG_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $LOG_DIR" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $LOCALE_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $LOCALE_DIR" "`basename $0`:$LINENO"
for l in $LOCALES; do
    execute "chown $APACHE_UG $LOCALE_DIR/$l" "`basename $0`:$LINENO"
    execute "chmod $RWX $LOCALE_DIR/$l" "`basename $0`:$LINENO"
    for s in $LOCALE_SCRIPTS; do
        execute "chown $APACHE_UG $LOCALE_DIR/$l/$s" "`basename $0`:$LINENO"
        execute "chmod $R__ $APACHE_UG $LOCALE_DIR/$l/$s" "`basename $0`:$LINENO"
    done
done

execute "chown $APACHE_UG $SESSIONS_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $SESSIONS_DIR" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $SHI_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $SHI_DIR" "`basename $0`:$LINENO"

execute "chown $APACHE_UG $SMARTY_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $SMARTY_DIR" "`basename $0`:$LINENO"
for dir in $SMARTY_DIRS; do
    execute "chown $APACHE_UG $dir" "`basename $0`:$LINENO"
    execute "chmod $RWX $dir" "`basename $0`:$LINENO"
done
for l in $LOCALES; do
    execute "chown $APACHE_UG $SMARTY_DIR/kotoba/locale/$l" "`basename $0`:$LINENO"
    execute "chmod $RWX $SMARTY_DIR/kotoba/locale/$l" "`basename $0`:$LINENO"
    for t in $TEMPLATES; do
        execute "chown $APACHE_UG $SMARTY_DIR/kotoba/locale/$l/$t" "`basename $0`:$LINENO"
        execute "chmod $R__ $SMARTY_DIR/kotoba/locale/$l/$t" "`basename $0`:$LINENO"
    done
done

exit 0
