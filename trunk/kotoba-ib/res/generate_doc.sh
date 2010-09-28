#!/bin/sh

# $ ./generate_doc.sh /var/www/html/kotoba

ABS_PATH=$1
PHPDOC_PATH="$ABS_PATH/phpdoc"
DOC_PATH="$ABS_PATH/docs"

$PHPDOC_PATH/phpdoc -o HTML:Smarty:PHP -f "$ABS_PATH/lib/db.php,$ABS_PATH/lib/errors.php,$ABS_PATH/lib/events.php,$ABS_PATH/lib/logging.php,$ABS_PATH/lib/misc.php,$ABS_PATH/lib/mysql.php" -t $DOC_PATH
exit $?
