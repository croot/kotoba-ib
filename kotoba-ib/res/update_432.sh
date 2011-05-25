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

UPDATED_SCRIPTS="lib/mysql.php \
                 lib/db.php"

RESOURCE_DIR="res"

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
execute "is_file_r $ABS_PATH" "`basename $0`:$LINENO"
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
# 3. Update files.
#
echo "Update files."
for s in $UPDATED_SCRIPTS; do
    execute "cp $SRC_DIR/$s $ABS_PATH/$s" "`basename $0`:$LINENO"
    execute "chown $APACHE_UG $ABS_PATH/$s" "`basename $0`:$LINENO"
    execute "chmod $R__ $ABS_PATH/$s" "`basename $0`:$LINENO"
done

#
# 4. Update stored procs.
#
echo "Update stored procs."
execute "mysql -u $DB_USER `if ! [ -z "$DB_PASS" ]; then echo "-p $DB_PASS "; fi`-D $DB_BASENAME < $SRC_DIR/$RESOURCE_DIR/create_procedures.sql" "`basename $0`:$LINENO"

#
# 5. Epilogue.
#
echo "Update successful."

exit 0
