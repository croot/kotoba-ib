#!/bin/sh

#
# Description.
#
# $1 - Destenation directory. E.g. /var/www/html/kotoba
# $2 - Path to index.php from servers document root. E.g. /kotoba
#

echo "Download installation scripts install.7z from http://kotoba-ib.googlecode.com/files/install.7z unpack and run install.sh."
exit 0

function show_help {
    echo "Usage example: ./install.sh /var/www/html/kotoba"
}

# $1 - command name.
# $2 - returned value.
# $3 - Additional comment.
function check_retval {
    if [ $2 -ne 0 ]; then
        echo "Error. $1 return $2. $3"
        exit 1
    fi
}

#
# 1. Check and read parameters.
#
DEST_DIR=$1
if [ -z $DEST_DIR ]
then
    echo "Error. Destenation directory not specifed."
    show_help
    exit 1
fi
echo "Destenation directory is $DEST_DIR"

#
# 2. Check access to destenation directory.
#
echo "Check access to destenation directory."
if ! [ -d $DEST_DIR ]
then
    echo "Error. $DEST_DIR is not exist or not direcotory."
    exit 1
fi
if ! [ -r $DEST_DIR ] || ! [ -w $DEST_DIR ] || ! [ -x $DEST_DIR ]
then
    echo "Error. Have no access to destenation directory."
    exit 1
fi

#
# 3. Get working copy to destenation direcrory.
#
echo "Get working copy to destenation direcrory."
svn checkout http://kotoba-ib.googlecode.com/svn/trunk/kotoba-ib/ $DEST_DIR/
check_retval "svn checkout" $?

#
# 4. Create database.
#
echo "Create database."
mysql -u root < $DEST_DIR/res/create_struct.sql
check_retval "mysql" $?
mysql -u root -D kotoba < $DEST_DIR/res/create_procedures.sql
check_retval "mysql" $? "Before another try, manually drop database what was created on previous step."

#
# 5. Download smarty, unpack and patch.
#
echo "Download smarty, unpack and patch."
wget -P /tmp/ http://www.smarty.net/do_download.php?download_file=Smarty-2.6.26.tar.gz
tar -zxvf /tmp/Smarty-2.6.26.tar.gz -C /tmp/ > /dev/null
check_retval "tar" $?
cp -r /tmp/Smarty-2.6.26/libs/* $DEST_DIR/smarty/

#
# 6. Download phpdoc, unpack and patch.
#
echo "Download phpdoc, unpack and patch."
wget -P /tmp/ http://kotoba-ib.googlecode.com/files/PhpDocumentor-1.4.3.tgz
tar -zxvf /tmp/PhpDocumentor-1.4.3.tgz -C /tmp/ > /dev/null
check_retval "tar" $?
cp -r /tmp/PhpDocumentor-1.4.3/* $DEST_DIR/phpdoc/
./patch_phpdoc.sh $DEST_DIR/phpdoc $DEST_DIR/res/phpdoc

#
# 7. Create documentation.
#
echo "Create documentation."
./generate_doc.sh $DEST_DIR

#
# 8. Generate .htaccess.
#
echo "Generate .htaccess."
echo "<Directory \"$1\">
    <IfModule dir_module>
        DirectoryIndex index.php
    </IfModule>

    RewriteEngine On
    RewriteRule ^([\w]{1,16})/$ $2/boards.php?board=\$1 [NE,L]
    RewriteRule ^([\w]{1,16})$ $2/boards.php?board=\$1 [NE,L]
    RewriteRule ^([\w]{1,16})/([\d]+)/$ $2/threads.php?board=\$1&thread=\$2 [NE,L]
    RewriteRule ^([\w]{1,16})/([\d]+)$ $2/threads.php?board=\$1&thread=\$2 [NE,L]
    RewriteRule ^([\w]{1,16})/p([\d]+)/$ $2/boards.php?board=\$1&page=\$2 [NE,L]
    RewriteRule ^([\w]{1,16})/p([\d]+)$ $2/boards.php?board=\$1&page=\$2 [NE,L]
</Directory>
<Directory \"$1/log\">
    Options FollowSymLinks
    AllowOverride None
    Order deny,allow
    Deny from all
</Directory>
<Directory \"$1/res\">
    Options FollowSymLinks
    AllowOverride None
    Order deny,allow
    Deny from all
</Directory>
<Directory \"$1/sessions\">
    Options FollowSymLinks
    AllowOverride None
    Order deny,allow
    Deny from all
</Directory>
<Directory \"$1/smarty\">
    Options FollowSymLinks
    AllowOverride None
    Order deny,allow
    Deny from all
</Directory>
<Directory \"$1/stat\">
    Options FollowSymLinks
    AllowOverride None
    Order deny,allow
    Deny from all
</Directory>
<DirectoryMatch \"\.svn\">
    Options FollowSymLinks
    AllowOverride None
    Order deny,allow
    Deny from all
</DirectoryMatch>" > $1/.htaccess

#
# 9. Grant permissions to Apache.
#
echo "Grant permissions to Apache."
chown -R apache:apache $1
check_retval "chown" $?

exit 0
