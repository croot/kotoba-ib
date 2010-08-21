# > .\generate_doc.ps1 C:\Apache22\htdocs\kotoba C:\Apache22\htdocs\kotoba\docs

$ABS_PATH=$args[0]
$PHPDOC_PATH=(Join-Path $args[0] phpdoc)
$DOC_PATH=$args[1]
$CUR_DIR=(Get-Location).path
if (! (Test-Path $ABS_PATH)) {
    echo "Error. Directory $ABS_PATH not exist."
    exit 1
}
if (! (Test-Path $PHPDOC_PATH)) {
    echo "Error. Directory $PHPDOC_PATH not exist."
    exit 1
}
if (! (Test-Path $DOC_PATH)) {
    echo "Error. Directory $DOC_PATH not exist."
    exit 1
}

echo "Change directory to $PHPDOC_PATH"
cd $PHPDOC_PATH

$TARGETS=""
$TARGETS+="$ABS_PATH\lib\db.php,"
$TARGETS+="$ABS_PATH\lib\errors.php,"
$TARGETS+="$ABS_PATH\lib\events.php,"
$TARGETS+="$ABS_PATH\lib\logging.php,"
$TARGETS+="$ABS_PATH\lib\mark.php,"
$TARGETS+="$ABS_PATH\lib\misc.php,"
$TARGETS+="$ABS_PATH\lib\mysql.php,"
$TARGETS+="$ABS_PATH\lib\popdown_handlers.php"

.\phpdoc.bat -o HTML:Smarty:PHP -f $TARGETS -t $DOC_PATH

echo ""
echo "Go back to $CUR_DIR"
cd $CUR_DIR
