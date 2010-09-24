#!/bin/sh

# Description.

# $1 - Destenation directory. E.g. /var/www/html/kotoba/
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
if ! [ -w $DEST_DIR ]
then
    echo "Error. Cant write to destenation directory."
    exit 1
fi

# 3. Get working copy to destenation direcrory.
svn checkout http://kotoba-ib.googlecode.com/svn/trunk/kotoba-ib/config.default $DEST_DIR
if [ -z $? ]
then
    echo "Error. Destenation directory not specifed."
    exit 1
fi
echo "Destenation directory is $DEST_DIR"

# 5. Create database.
# 6. Creater tables, etc.
# 7. Download smarty, unpack and patch.
# 8. Download phpdoc, unpack and paеch.
# 9. Create documentation.
