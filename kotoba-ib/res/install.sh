#!/bin/sh

# Description.

# $1 - Destenation directory. E.g. /var/www/html/kotoba
# $2 - 

# 1. Check and read parameters.
DEST_DIR=$1
if [ -z $DEST_DIR ]
then
    echo "Error. Destenation directory not specifed."
    exit 1
fi
echo "Destenation directory is $DEST_DIR"

# 2. Check access to destenation directory.
if ! [ -d $DEST_DIR ]
then
    echo "Error. $DEST_DIR is not exist or not direcotory."
    exit 1
fi
if ! [ -r $DEST_DIR ] || ! [ -w $DEST_DIR ]
then
    echo "Error. Cant write to or read from destenation directory."
    exit 1
fi

# 3. Get working copy to destenation direcrory.
svn checkout http://kotoba-ib.googlecode.com/svn/trunk/kotoba-ib/ $DEST_DIR/
RETVAL=$?
if [ $RETVAL -ne 0 ]
then
    echo "Error. svn checkout return $RETVAL"
    exit 1
fi

# 4. Create database.
mysql -u root < $DEST_DIR/res/create_struct.sql
RETVAL=$?
if [ $RETVAL -ne 0 ]
then
    echo "Error. mysql return $RETVAL"
#    rm -r $DEST_DIR/*
    exit 1
fi
mysql -u root < $DEST_DIR/res/create_procedures.sql
RETVAL=$?
if [ $RETVAL -ne 0 ]
then
    echo "Error. mysql return $RETVAL. Before another try, manually drop database what was created on previous step."
#    rm -r $DEST_DIR/*
    exit 1
fi

# 5. Download smarty, unpack and patch.
# 8. Download phpdoc, unpack and paеch.
# 9. Create documentation.
