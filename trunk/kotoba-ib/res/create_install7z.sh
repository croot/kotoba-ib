#!/bin/sh

#
# Description.
#
# $1 - Kotoba directory. E.g. /var/www/html/kotoba
# $2 - Pass 1 to remove old archives.
#

function show_help {
    echo ""
    echo "Usage: ./create_install7z.sh path [1]"
    echo ""
    echo "Parameters:"
    echo "    path - Kotoba directory. E.g. /var/www/html/kotoba"
    echo "    1 - (optional) remove old archives"
    echo ""
    echo "example: ./create_install7z.sh /var/www/html/kotoba 1"
    echo ""
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
    echo "Error. Kotoba directory not specifed."
    show_help
    exit 1
fi
echo "Destenation directory is $1"
if [ $2 -eq 1 ]
then
    rm $1/res/install_*.7z
fi

7z a $1/res/install_`date +%s`.7z $1/res/install.sh $1/res/patch_phpdoc.sh $1/res/generate_doc.sh
check_retval "7z" $?