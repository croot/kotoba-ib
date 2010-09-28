#!/bin/sh

# $1 - command name.
# $2 - returned value.
# $3 - Additional comment.
function check_retval {
    if [ $2 -ne 0 ]; then
        echo "Error. $1 return $2. $3"
        exit 1
    fi
}

# $ ./patch_phpdoc.sh /var/www/html/kotoba/phpdoc /var/www/html/kotoba/res/phpdoc
PHPDOC_PATH=$1
PATCHES_PATH=$2

#
# Patch Setup.inc.php to support UTF-8
#
echo "Patch Setup.inc.php."
patch $PHPDOC_PATH/phpDocumentor/Setup.inc.php $PATCHES_PATH/phpDocumentor/Setup.inc.php.patch
check_retval "patch" $?

#
# Patch Converter.inc to fix timezone error.
#
echo "Patch Converter.inc."
patch $PHPDOC_PATH/phpDocumentor/Converter.inc $PATCHES_PATH/phpDocumentor/Converter.inc.patch
check_retval "patch" $?

#
# Patch Smarty_Compiler.class.php to fix timezone error.
#
echo "Patch Smarty_Compiler.class.php."
patch $PHPDOC_PATH/phpDocumentor/Smarty-2.6.0/libs/Smarty_Compiler.class.php $PATCHES_PATH/phpDocumentor/Smarty-2.6.0/libs/Smarty_Compiler.class.php.patch
check_retval "patch" $?

#
# Patch header.tpl to support UTF-8
#
echo "Patch header.tpl."
patch $PHPDOC_PATH/phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates/header.tpl $PATCHES_PATH/phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates/header.tpl.patch
check_retval "patch" $?

#
# Rename tamplate cause Smarty expect .tpl extension (bug in some PHPDoc distribs)
#
if [ -f $PHPDOC_PATH/phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates/pkgelementindex.tp ]
then
    echo "Rename $PHPDOC_PATH/phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates/pkgelementindex.tp to $PHPDOC_PATH/phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates/pkgelementindex.tpl"
    mv $PHPDOC_PATH/phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates/pkgelementindex.tp $PHPDOC_PATH/phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates/pkgelementindex.tpl
fi

exit 0
