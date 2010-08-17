#!/bin/sh

# $ ./generate_doc.sh ~/kotoba2 ~/kotoba2/docs

ABS_PATH=$1
PHPDOC_PATH="$1/phpdoc"
DOC_PATH=$2
CUR_DIR=`pwd`
if ! [ -d $ABS_PATH ]
then
    echo "Error. Directory $ABS_PATH not exist."
    exit 1
fi
if ! [ -d $PHPDOC_PATH ]
then
    echo "Error. Directory $PHPDOC_PATH not exist."
    exit 1
fi
if ! [ -d $DOC_PATH ]
then
    echo "Error. Directory $DOC_PATH not exist."
    exit 1
fi

echo "Change directory to $PHPDOC_PATH"
cd $PHPDOC_PATH
./phpdoc -o HTML:Smarty:PHP -f "$ABS_PATH/lib/db.php,$ABS_PATH/lib/errors.php,$ABS_PATH/lib/events.php,$ABS_PATH/lib/logging.php,$ABS_PATH/lib/misc.php,$ABS_PATH/lib/mysql.php" -t $DOC_PATH

echo ""
echo "Go back to $CUR_DIR"
cd $CUR_DIR
