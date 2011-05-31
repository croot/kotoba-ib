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

UPDATED_SCRIPTS="search.php \
                 smarty/kotoba/templates/locale/eng/catalog.tpl \
                 smarty/kotoba/templates/locale/eng/search.tpl \
                 smarty/kotoba/templates/locale/rus/catalog.tpl \
                 smarty/kotoba/templates/locale/rus/search.tpl \
                 catalog.php"

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
