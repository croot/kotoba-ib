#!/bin/bash

########## Edit constants after this line ######################################

# Source directory. Directory where working copy of Kotoba pleaced.
SRC_DIR="/tmp/kotoba"

# Destination directory. Directory where Kotoba actually work.
ABS_PATH="/var/www/html/kotoba"

# Path from server document root to kotoba directory.
DIR_PATH="/kotoba"

# Mysql user name.
DB_USER="root"

# Mysql password. Empty string means no password.
DB_PASS=""

# Kotoba database name.
DB_BASENAME="kotoba"

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

RESOURCE_DIR="res"

UPDATED_SCRIPTS="threads.php \
                 reply.php \
                 hide_thread.php \
                 my_id.php \
                 remove_upload.php \
                 favorites.php \
                 search.php \
                 edit_settings.php \
                 logout.php \
                 locale/eng/errors.php \
                 locale/eng/exceptions.php \
                 locale/rus/errors.php \
                 locale/rus/exceptions.php \
                 boards.php \
                 report.php \
                 lib/mark.php \
                 lib/misc.php \
                 lib/errors.php \
                 lib/shi_exit.php \
                 lib/exceptions.php \
                 lib/mysql.php \
                 lib/db.php \
                 lib/logging.php \
                 lib/shi_applet.php \
                 post.php \
                 manage.php \
                 create_thread.php \
                 remove_post.php \
                 admin/edit_categories.php \
                 admin/update_macrochan.php \
                 admin/move_thread.php \
                 admin/hard_ban.php \
                 admin/reports.php \
                 admin/edit_threads.php \
                 admin/delete_dangling_attachments.php \
                 admin/moderate.php \
                 admin/edit_words.php \
                 admin/edit_acl.php \
                 admin/edit_upload_handlers.php \
                 admin/log_view.php \
                 admin/edit_popdown_handlers.php \
                 admin/edit_languages.php \
                 admin/edit_spamfilter.php \
                 admin/edit_upload_types.php \
                 admin/edit_boards.php \
                 admin/archive.php \
                 admin/edit_stylesheets.php \
                 admin/edit_user_groups.php \
                 admin/mass_ban.php \
                 admin/edit_groups.php \
                 admin/edit_board_upload_types.php \
                 admin/delete_marked.php \
                 admin/edit_bans.php \
                 unhide_thread.php \
                 index.php \
                 catalog.php \
                 over.php \
                 captcha/image.php \
                 animaptcha/animaptcha.php"

R__=400

################################################################################
# Functions
#

. $SRC_DIR/$RESOURCE_DIR/functions.sh

################################################################################
#
#

#
# 1. Validate pathes.
#
echo "Validate pathes."
execute "is_file_rx $SRC_DIR" "`basename $0`:$LINENO"
execute "is_file_r $ABS_PATH" "`basename $0`:$LINENO"
execute "is_file_rwx /tmp" "`basename $0`:$LINENO"

#
# 2. Read apache user name and group.
#
echo "Read apache user name and group."
APACHE_U=`grep -e "^User ." /etc/httpd/conf/httpd.conf | awk '{print $2}'`
APACHE_G=`grep -e "^Group ." /etc/httpd/conf/httpd.conf | awk '{print $2}'`
if [ -z $APACHE_U ] || [ -z $APACHE_G ]; then
    echo "Parse apache user name and group failed."
    exit 1
fi
APACHE_UG="$APACHE_U:$APACHE_G"

#
# 3. Update files.
#
echo "Update files."
for s in $UPDATED_SCRIPTS; do
    execute "cp $SRC_DIR/$s $ABS_PATH/$s" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $ABS_PATH/$s" "`basename $0`:$LINENO"
    execute "chmod $R__ $ABS_PATH/$s" "`basename $0`:$LINENO"
done

#
# 4. Epilogue.
#
echo "Update successful."

exit 0
