#!/bin/bash

########## Edit constants after this line ######################################

# Source directory. Directory where working copy of Kotoba engine will be
# created.
SRC_DIR="/tmp/kotoba"

# Destination directory. Directory where engine will actually work.
DST_DIR="/var/www/html/kotoba"

# Path from server document root to kotoba directory.
KOTOBA_DOC_ROOT="/kotoba"

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

MAIN_SCRIPTS="boards.php \
              catalog.php \
              config.default \
              create_thread.php \
              edit_settings.php \
              favorites.php \
              flower.png \
              hide_thread.php \
              index.php \
              kotoba.js \
              license.txt \
              logout.php \
              manage.php \
              my_id.php \
              over.php \
              post.php \
              protoaculous-compressed.js \
              remove_post.php \
              remove_upload.php \
              reply.php \
              report.php \
              search.php \
              threads.php \
              unhide_thread.php"

ADMIN_DIR="admin"
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

ANIMAPTCHA_DIR="animaptcha"
ANIMAPTCHA_SCRIPTS="$ANIMAPTCHA_DIR/animaptcha.php \
                    $ANIMAPTCHA_DIR/boxxy001.png"

CAPTCHA_DIR="captcha"
CAPTCHA_SCRIPTS="$CAPTCHA_DIR/image.php"

CSS_DIR="css"
CSS_SCRIPTS="$CSS_DIR/global.css"
CSS="futaba.css \
     kusaba.css"
CSS_ICONS="closed.png \
           delete.png \
           delfile.png \
           favorites.png \
           hide.png \
           report.png \
           sticky.png"

DEFAULT_IMAGES_DIR="img"
DEFAULT_IMAGES="$DEFAULT_IMAGES_DIR/deleted.png \
                $DEFAULT_IMAGES_DIR/spoiler.png"

LATEX_DIR="latexcache"

LIB_DIR="lib"
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
             $LIB_DIR/upload_handlers.php \
             $LIB_DIR/wrappers.php"

LOCALE_DIR="locale"
LOCALES="eng \
         rus"
LOCALE_SCRIPTS="errors.php \
                logging.php"

LOG_DIR="log"

RESOURCE_DIR="res"

SESSIONS_DIR="sessions"

SHI_DIR="shi"

SMARTY_DIR="smarty"
SMARTY_DIRS="$SMARTY_DIR/kotoba \
             $SMARTY_DIR/kotoba/cache \
             $SMARTY_DIR/kotoba/configs \
             $SMARTY_DIR/kotoba/templates \
             $SMARTY_DIR/kotoba/templates/locale \
             $SMARTY_DIR/kotoba/templates_c \
             $SMARTY_DIR/kotoba/templates_c/locale \
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

    echo "Execute command $1"
    eval $1
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

# Check if file exists and read,write,execute permissions granted.
# $1 - full file name.
function is_file_rwx {
    ret=1

    if [ -e $1 ]; then
        if [ -r $1 ]; then
            if [ -w $1 ]; then
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
                echo "Have no permission to write file $1"
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
execute "is_file_rwx /tmp" "`basename $0`:$LINENO"

#
# 2. Read apache user name and group.
#
APACHE_U=`grep -e "^User ." /etc/httpd/conf/httpd.conf | awk '{print $2}'`
APACHE_G=`grep -e "^Group ." /etc/httpd/conf/httpd.conf | awk '{print $2}'`
if [ -z $APACHE_U ] || [ -z $APACHE_G ]; then
    echo "Parse apache user name and group failed."
    exit 1
fi
APACHE_UG="$APACHE_U:$APACHE_G"

#
# 3. Get working copy to source directory.
#
echo "Get working copy to $SRC_DIR direcrory."
execute "svn checkout https://kotoba-ib.googlecode.com/svn/trunk/kotoba-ib/ $SRC_DIR/" "`basename $0`:$LINENO"

#
# 4. Create database.
#
echo "Create database."
execute "mysql -u $MYSQL_USR `if ! [ -z "$MYSQL_PSWD" ]; then echo "-p $MYSQL_PSWD "; fi`< $SRC_DIR/$RESOURCE_DIR/create_struct.sql" "`basename $0`:$LINENO"
execute "mysql -u $MYSQL_USR `if ! [ -z "$MYSQL_PSWD" ]; then echo "-p $MYSQL_PSWD "; fi`-D kotoba < $SRC_DIR/$RESOURCE_DIR/create_procedures.sql" "`basename $0`:$LINENO"

#
# 7. Copy files and setup permissions.
#
echo "Copy files and setup permissions."
for s in $MAIN_SCRIPTS; do
    execute "cp $SRC_DIR/$s $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chmod $R__ $DST_DIR/$s" "`basename $0`:$LINENO"
done

execute "mkdir $DST_DIR/$ADMIN_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$ADMIN_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$ADMIN_DIR" "`basename $0`:$LINENO"
for s in $ADMIN_SCRIPTS; do
    execute "cp $SRC_DIR/$s $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chmod $R__ $DST_DIR/$s" "`basename $0`:$LINENO"
done

execute "mkdir $DST_DIR/$ANIMAPTCHA_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$ANIMAPTCHA_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$ANIMAPTCHA_DIR" "`basename $0`:$LINENO"
for s in $ANIMAPTCHA_SCRIPTS; do
    execute "cp $SRC_DIR/$s $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chmod $R__ $DST_DIR/$s" "`basename $0`:$LINENO"
done

execute "mkdir $DST_DIR/$CAPTCHA_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$CAPTCHA_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$CAPTCHA_DIR" "`basename $0`:$LINENO"
for s in $CAPTCHA_SCRIPTS; do
    execute "cp $SRC_DIR/$s $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chmod $R__ $DST_DIR/$s" "`basename $0`:$LINENO"
done

execute "mkdir $DST_DIR/$CSS_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$CSS_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$CSS_DIR" "`basename $0`:$LINENO"
for s in $CSS_SCRIPTS; do
    execute "cp $SRC_DIR/$s $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chmod $R__ $DST_DIR/$s" "`basename $0`:$LINENO"
done
for c in $CSS; do
    execute "mkdir $DST_DIR/$CSS_DIR/$c" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$CSS_DIR/$c" "`basename $0`:$LINENO"
    execute "chmod $RWX $DST_DIR/$CSS_DIR/$c" "`basename $0`:$LINENO"

    execute "cp $SRC_DIR/$CSS_DIR/$c/$c $DST_DIR/$CSS_DIR/$c/$c" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$CSS_DIR/$c/$c" "`basename $0`:$LINENO"
    execute "chmod $R__ $DST_DIR/$CSS_DIR/$c/$c" "`basename $0`:$LINENO"
    for i in $CSS_ICONS; do
        execute "cp $SRC_DIR/$CSS_DIR/$c/$i $DST_DIR/$CSS_DIR/$c/$i" "`basename $0`:$LINENO"
        execute "chown $APACHE_UG $DST_DIR/$CSS_DIR/$c/$i" "`basename $0`:$LINENO"
        execute "chmod $R__ $DST_DIR/$CSS_DIR/$c/$i" "`basename $0`:$LINENO"
    done
done

execute "mkdir $DST_DIR/$DEFAULT_IMAGES_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$DEFAULT_IMAGES_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$DEFAULT_IMAGES_DIR" "`basename $0`:$LINENO"
for i in $DEFAULT_IMAGES; do
    execute "cp $SRC_DIR/$i $DST_DIR/$i" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$i" "`basename $0`:$LINENO"
    execute "chmod $R__ $DST_DIR/$i" "`basename $0`:$LINENO"
done

execute "mkdir $DST_DIR/$LATEX_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$LATEX_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$LATEX_DIR" "`basename $0`:$LINENO"

execute "mkdir $DST_DIR/$LIB_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$LIB_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$LIB_DIR" "`basename $0`:$LINENO"
for s in $LIB_SCRIPTS; do
    execute "cp $SRC_DIR/$s $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$s" "`basename $0`:$LINENO"
    execute "chmod $R__ $DST_DIR/$s" "`basename $0`:$LINENO"
done

execute "mkdir $DST_DIR/$LOG_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$LOG_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$LOG_DIR" "`basename $0`:$LINENO"

execute "mkdir $DST_DIR/$LOCALE_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$LOCALE_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$LOCALE_DIR" "`basename $0`:$LINENO"
for l in $LOCALES; do
    execute "mkdir $DST_DIR/$LOCALE_DIR/$l" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$LOCALE_DIR/$l" "`basename $0`:$LINENO"
    execute "chmod $RWX $DST_DIR/$LOCALE_DIR/$l" "`basename $0`:$LINENO"
    for s in $LOCALE_SCRIPTS; do
        execute "cp $SRC_DIR/$LOCALE_DIR/$l/$s $DST_DIR/$LOCALE_DIR/$l/$s" "`basename $0`:$LINENO"
        execute "chown $APACHE_UG $DST_DIR/$LOCALE_DIR/$l/$s" "`basename $0`:$LINENO"
        execute "chmod $R__ $DST_DIR/$LOCALE_DIR/$l/$s" "`basename $0`:$LINENO"
    done
done

execute "mkdir $DST_DIR/$SESSIONS_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$SESSIONS_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$SESSIONS_DIR" "`basename $0`:$LINENO"

execute "mkdir $DST_DIR/$SHI_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$SHI_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$SHI_DIR" "`basename $0`:$LINENO"

execute "mkdir $DST_DIR/$SMARTY_DIR" "`basename $0`:$LINENO"
execute "chown $APACHE_UG $DST_DIR/$SMARTY_DIR" "`basename $0`:$LINENO"
execute "chmod $RWX $DST_DIR/$SMARTY_DIR" "`basename $0`:$LINENO"
for dir in $SMARTY_DIRS; do
    execute "mkdir -p $DST_DIR/$dir" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$dir" "`basename $0`:$LINENO"
    execute "chmod $RWX $DST_DIR/$dir" "`basename $0`:$LINENO"
done
for l in $LOCALES; do
    execute "mkdir -p $DST_DIR/$SMARTY_DIR/kotoba/templates/locale/$l" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$SMARTY_DIR/kotoba/templates/locale/$l" "`basename $0`:$LINENO"
    execute "chmod $RWX $DST_DIR/$SMARTY_DIR/kotoba/templates/locale/$l" "`basename $0`:$LINENO"
    execute "mkdir -p $DST_DIR/$SMARTY_DIR/kotoba/templates_c/locale/$l" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $DST_DIR/$SMARTY_DIR/kotoba/templates_c/locale/$l" "`basename $0`:$LINENO"
    execute "chmod $RWX $DST_DIR/$SMARTY_DIR/kotoba/templates_c/locale/$l" "`basename $0`:$LINENO"
    for t in $TEMPLATES; do
        execute "cp $SRC_DIR/$SMARTY_DIR/kotoba/templates/locale/$l/$t $DST_DIR/$SMARTY_DIR/kotoba/templates/locale/$l/$t" "`basename $0`:$LINENO"
        execute "chown $APACHE_UG $DST_DIR/$SMARTY_DIR/kotoba/templates/locale/$l/$t" "`basename $0`:$LINENO"
        execute "chmod $R__ $DST_DIR/$SMARTY_DIR/kotoba/templates/locale/$l/$t" "`basename $0`:$LINENO"
    done
done

#
# 5. Download smarty, unpack and patch.
#
echo "Download smarty, unpack and patch."
execute "wget -P /tmp/ http://www.smarty.net/files/Smarty-2.6.26.tar.gz" "`basename $0`:$LINENO"
execute "tar -zxvf /tmp/Smarty-2.6.26.tar.gz -C /tmp/ > /dev/null" "`basename $0`:$LINENO"
execute "cp -r /tmp/Smarty-2.6.26/libs/* $DST_DIR/$SMARTY_DIR/libs/" "`basename $0`:$LINENO"
execute "cp $DST_DIR/$SMARTY_DIR/libs/Smarty.class.php $DST_DIR/$LIB_DIR/Smarty.class.php" "`basename $0`:$LINENO"

#
# 6. Shi
#
echo "Download shi, unpack and copy."
execute "wget -P /tmp/ http://kotoba-ib.googlecode.com/files/shi_1287405235.7z" "`basename $0`:$LINENO"
execute "7z x -o/tmp /tmp/shi_1287405235.7z" "`basename $0`:$LINENO"
execute "cp -r /tmp/shi/* $DST_DIR/$SHI_DIR/" "`basename $0`:$LINENO"
execute "cp $DST_DIR/$SHI_DIR/shi_save.php $DST_DIR/$LIB_DIR/shi_save.php" "`basename $0`:$LINENO"

#
# 8. Generate .htaccess.
#
echo "Generate .htaccess."
echo "DirectoryIndex index.php
RewriteEngine On
RewriteRule ^([\w]{1,16})/$ $KOTOBA_DOC_ROOT/boards.php?board=\$1 [NE,L]
RewriteRule ^([\w]{1,16})$ $KOTOBA_DOC_ROOT/boards.php?board=\$1 [NE,L]
RewriteRule ^([\w]{1,16})/([\d]+)/$ $KOTOBA_DOC_ROOT/threads.php?board=\$1&thread=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/([\d]+)$ $KOTOBA_DOC_ROOT/threads.php?board=\$1&thread=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/p([\d]+)/$ $KOTOBA_DOC_ROOT/boards.php?board=\$1&page=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/p([\d]+)$ $KOTOBA_DOC_ROOT/boards.php?board=\$1&page=\$2 [NE,L]" > $DST_DIR/.htaccess
echo "Deny from all" > $DST_DIR/$SMARTY_DIR/.htaccess
echo "Deny from all" > $DST_DIR/$LOG_DIR/.htaccess
echo "Deny from all" > $DST_DIR/$SESSIONS_DIR/.htaccess

#
# 9. Add requied directives to httpd.conf.
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
# 10. Grant $DST_DIR to Apache.
#
echo "Grant $DST_DIR to Apache."
execute "chown $APACHE_UG $DST_DIR" "`basename $0`:$LINENO"

#
# 11. Epilogue.
#
echo "Installations successful. You can manually clean up following dirs:"
echo "rm -rf $SRC_DIR /tmp/Smarty-2.6.26* /tmp/shi*"
echo "if you like."

exit 0
