#!/bin/sh

# $ ./patch_phpdoc.sh /home/sorc/kotoba2
ABS_PATH=$1

CUR_DIR=`pwd`
cp -r $ABS_PATH/res/phpdoc/* $ABS_PATH/phpdoc/

#
# Patch Setup.inc.php to support UTF-8
#
TARGET_DIR="$ABS_PATH/phpdoc/phpDocumentor"
if ! [ -d $TARGET_DIR ]
then
    echo "Error. Directory $TARGET_DIR not exist."
    exit 1
fi
if ! [ -f $TARGET_DIR/Setup.inc.php ]
then
    echo "Error. Regular file $TARGET_DIR/Setup.inc.php not exist."
    exit 1
fi
if ! [ -f $TARGET_DIR/Setup.inc.php.patch ]
then
    echo "Error. Regular file $TARGET_DIR/Setup.inc.php.patch not exist."
    exit 1
fi

echo "Change directory to $TARGET_DIR"
cd $TARGET_DIR
dos2unix $TARGET_DIR/Setup.inc.php
patch $TARGET_DIR/Setup.inc.php $TARGET_DIR/Setup.inc.php.patch
echo "Go back to $CUR_DIR"
cd $CUR_DIR

#
# Patch Converter.inc to fix timezone error.
#
TARGET_DIR="$ABS_PATH/phpdoc/phpDocumentor"
if ! [ -d $TARGET_DIR ]
then
    echo "Error. Directory $TARGET_DIR not exist."
    exit 1
fi
if ! [ -f $TARGET_DIR/Converter.inc ]
then
    echo "Error. Regular file $TARGET_DIR/Converter.inc not exist."
    exit 1
fi
if ! [ -f $TARGET_DIR/Converter.inc.patch ]
then
    echo "Error. Regular file $TARGET_DIR/Converter.inc.patch not exist."
    exit 1
fi

echo "Change directory to $TARGET_DIR"
cd $TARGET_DIR
dos2unix $TARGET_DIR/Converter.inc
patch $TARGET_DIR/Converter.inc $TARGET_DIR/Converter.inc.patch
echo "Go back to $CUR_DIR"
cd $CUR_DIR

#
# Patch Smarty_Compiler.class.php to fix timezone error.
#
TARGET_DIR="$ABS_PATH/phpdoc/phpDocumentor/Smarty-2.6.0/libs"
if ! [ -d $TARGET_DIR ]
then
    echo "Error. Directory $TARGET_DIR not exist."
    exit 1
fi
if ! [ -f $TARGET_DIR/Smarty_Compiler.class.php ]
then
    echo "Error. Regular file $TARGET_DIR/Smarty_Compiler.class.php not exist."
    exit 1
fi
if ! [ -f $TARGET_DIR/Smarty_Compiler.class.php.patch ]
then
    echo "Error. Regular file $TARGET_DIR/Smarty_Compiler.class.php.patch not exist."
    exit 1
fi

echo "Change directory to $TARGET_DIR"
cd $TARGET_DIR
dos2unix $TARGET_DIR/Smarty_Compiler.class.php
patch $TARGET_DIR/Smarty_Compiler.class.php $TARGET_DIR/Smarty_Compiler.class.php.patch
echo "Go back to $CUR_DIR"
cd $CUR_DIR

#
# Patch header.tpl to support UTF-8
#
TARGET_DIR="$ABS_PATH/phpdoc/phpDocumentor/Converters/HTML/Smarty/templates/PHP/templates"
if ! [ -d $TARGET_DIR ]
then
    echo "Error. Directory $TARGET_DIR not exist."
    exit 1
fi
if ! [ -f $TARGET_DIR/header.tpl ]
then
    echo "Error. Regular file $TARGET_DIR/header.tpl not exist."
    exit 1
fi
if ! [ -f $TARGET_DIR/header.tpl.patch ]
then
    echo "Error. Regular file $TARGET_DIR/header.tpl.patch not exist."
    exit 1
fi

echo "Change directory to $TARGET_DIR"
cd $TARGET_DIR
dos2unix $TARGET_DIR/header.tpl
patch $TARGET_DIR/header.tpl $TARGET_DIR/header.tpl.patch

#
# Rename tamplate cause Smarty expect .tpl extension (bug in some PHPDoc distribs)
#
if [ -f $TARGET_DIR/pkgelementindex.tp ]
then
    echo "Rename $TARGET_DIR/pkgelementindex.tp to $TARGET_DIR/pkgelementindex.tpl"
    mv pkgelementindex.tp pkgelementindex.tpl
fi

echo "Go back to $CUR_DIR"
cd $CUR_DIR
exit 0
