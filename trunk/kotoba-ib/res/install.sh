#!/bin/sh

#
# Description.
#
# $1 - Destenation directory. E.g. /var/www/html/kotoba
# $2 - Path to index.php from servers document root. E.g. /kotoba
#

#
# Permission notes:
#
# You install kotoba to /var/www/html/kotoba
# Your user name is sauce and your user group is sauce too.
# Your Apache run via user apache and has user group apache.
#
# Add user apache user to your user group sauce. And then:
# #chown -R sauce:sauce /var/www/html/kotoba
# #chmod -R g+w /var/www/html/kotoba
# 

function show_help {
    echo "Usage example: ./install.sh /var/www/html/kotoba /kotoba"
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
if [ -z $1 ]
then
    echo "Error. Destenation directory not specifed."
    show_help
    exit 1
fi
echo "Destenation directory is $1"
if [ -z $2 ]
then
    echo "Error. Path to index.php from servers document root."
    show_help
    exit 1
fi
echo "Path to index.php from servers document root is $2"

#
# 2. Check access to destenation directory.
#
echo "Check access to destenation directory."
if ! [ -d $1 ]
then
    echo "Error. $1 is not exist or not direcotory."
    exit 1
fi
if ! [ -r $1 ] || ! [ -w $1 ] || ! [ -x $1 ]
then
    echo "Error. Have no access to destenation directory."
    exit 1
fi

#
# 3. Get working copy to destenation direcrory.
#
echo "Get working copy to destenation direcrory."
svn checkout https://kotoba-ib.googlecode.com/svn/trunk/kotoba-ib/ $1/
check_retval "svn checkout" $?

#
# 4. Create database.
#
echo "Create database."
mysql -u root < $1/res/create_struct.sql
check_retval "mysql" $?
mysql -u root -D kotoba < $1/res/create_procedures.sql
check_retval "mysql" $? "Before another try, manually drop database what was created on previous step."

#
# 5. Download smarty, unpack and patch.
#
echo "Download smarty, unpack and patch."
wget -P /tmp/ http://www.smarty.net/do_download.php?download_file=Smarty-2.6.26.tar.gz
tar -zxvf /tmp/Smarty-2.6.26.tar.gz -C /tmp/ > /dev/null
check_retval "tar" $?
cp -r /tmp/Smarty-2.6.26/libs/* $1/smarty/

#
# 6. Download phpdoc, unpack and patch.
#
echo "Download phpdoc, unpack and patch."
wget -P /tmp/ http://kotoba-ib.googlecode.com/files/PhpDocumentor-1.4.3.tgz
tar -zxvf /tmp/PhpDocumentor-1.4.3.tgz -C /tmp/ > /dev/null
check_retval "tar" $?
cp -r /tmp/PhpDocumentor-1.4.3/* $1/phpdoc/
./patch_phpdoc.sh $1/phpdoc $1/res/phpdoc

#
# 7. Create documentation.
#
echo "Create documentation."
./generate_doc.sh $1

#
# 8. Add requied directives to httpd.conf.
#
echo "Add requied directives to httpd.conf."
echo "

### Kotoba
#
# Options requied or recommended for Kotoba.

<Directory \"$1\">
    AllowOverride FileInfo Limit Indexes
</Directory>
<DirectoryMatch \"\\.svn\">
    Options FollowSymLinks
    AllowOverride None
    Order deny,allow
    Deny from all
</DirectoryMatch>" >> /etc/httpd/conf/httpd.conf

#
# 9. Generate .htaccess.
#
echo "Generate .htaccess."
echo "DirectoryIndex index.php
RewriteEngine On
RewriteRule ^([\w]{1,16})/$ $2/boards.php?board=\$1 [NE,L]
RewriteRule ^([\w]{1,16})$ $2/boards.php?board=\$1 [NE,L]
RewriteRule ^([\w]{1,16})/([\d]+)/$ $2/threads.php?board=\$1&thread=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/([\d]+)$ $2/threads.php?board=\$1&thread=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/p([\d]+)/$ $2/boards.php?board=\$1&page=\$2 [NE,L]
RewriteRule ^([\w]{1,16})/p([\d]+)$ $2/boards.php?board=\$1&page=\$2 [NE,L]" > $1/.htaccess

#
# 10. Create Kotoba configuration file.
#
echo "Create Kotoba configuration file."
cp $1/config.default $1/config.php

exit 0
