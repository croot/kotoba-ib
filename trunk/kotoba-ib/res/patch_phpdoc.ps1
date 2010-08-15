# > .\patch_phpdoc.ps1 C:\Apache22\htdocs\kotoba "C:\Program Files\GnuWin32"

$ABS_PATH=$args[0]
$GNUWIN32_PATH=$args[1]
$CUR_DIR=(Get-Location).path
if (! (Test-Path $ABS_PATH)) {
    echo "Error. Directory $ABS_PATH not exist."
    exit 1
}
if (! (Test-Path "$GNUWIN32_PATH\bin")) {
    echo "Error. Directory $GNUWIN32_PATH\bin not exist."
    exit 1
}

$env:Path += ";$GNUWIN32_PATH\bin"

dir $ABS_PATH\res\phpdoc -Recurse -Exclude ".svn" | Copy-Item -Destination {Join-Path $ABS_PATH\phpdoc\ $_.FullName.Substring("$ABS_PATH\res\phpdoc".length)}

#
# Patch Setup.inc.php to support UTF-8
#
$TARGET_DIR="$ABS_PATH\phpdoc\phpDocumentor"
if (! (Test-Path $TARGET_DIR)) {
    echo "Error. Directory $TARGET_DIR not exist."
    exit 1
}
if (! (Test-Path $TARGET_DIR\Setup.inc.php)) {
    echo "Error. Regular file $TARGET_DIR\Setup.inc.php not exist."
    exit 1
}
if (! (Test-Path $TARGET_DIR\Setup.inc.php.patch)) {
    echo "Error. Regular file $TARGET_DIR/Setup.inc.php.patch not exist."
    exit 1
}

echo "Change directory to $TARGET_DIR"
cd $TARGET_DIR
unix2dos.exe $TARGET_DIR\Setup.inc.php
unix2dos.exe $TARGET_DIR\Setup.inc.php.patch
patch.exe $TARGET_DIR\Setup.inc.php $TARGET_DIR\Setup.inc.php.patch
dos2unix.exe $TARGET_DIR\Setup.inc.php.patch
echo "Go back to $CUR_DIR"
cd $CUR_DIR

#
# Patch Converter.inc to fix timezone error.
#
$TARGET_DIR="$ABS_PATH\phpdoc\phpDocumentor"
if (! (Test-Path $TARGET_DIR)) {
    echo "Error. Directory $TARGET_DIR not exist."
    exit 1
}
if (! (Test-Path $TARGET_DIR\Converter.inc)) {
    echo "Error. Regular file $TARGET_DIR\Converter.inc not exist."
    exit 1
}
if (! (Test-Path $TARGET_DIR\Converter.inc.patch)) {
    echo "Error. Regular file $TARGET_DIR\Converter.inc.patch not exist."
    exit 1
}

echo "Change directory to $TARGET_DIR"
cd $TARGET_DIR
unix2dos.exe $TARGET_DIR\Converter.inc
unix2dos.exe $TARGET_DIR\Converter.inc.patch
patch.exe $TARGET_DIR\Converter.inc $TARGET_DIR\Converter.inc.patch
dos2unix.exe $TARGET_DIR\Converter.inc.patch
echo "Go back to $CUR_DIR"
cd $CUR_DIR

#
# Patch Smarty_Compiler.class.php to fix timezone error.
#
$TARGET_DIR="$ABS_PATH\phpdoc\phpDocumentor\Smarty-2.6.0\libs"
if (! (Test-Path $TARGET_DIR)) {
    echo "Error. Directory $TARGET_DIR not exist."
    exit 1
}
if (! (Test-Path $TARGET_DIR\Smarty_Compiler.class.php)) {
    echo "Error. Regular file $TARGET_DIR\Smarty_Compiler.class.php not exist."
    exit 1
}
if (! (Test-Path $TARGET_DIR\Smarty_Compiler.class.php.patch)) {
    echo "Error. Regular file $TARGET_DIR\Smarty_Compiler.class.php.patch not exist."
    exit 1
}

echo "Change directory to $TARGET_DIR"
cd $TARGET_DIR
unix2dos.exe $TARGET_DIR\Smarty_Compiler.class.php
unix2dos.exe $TARGET_DIR\Smarty_Compiler.class.php.patch
patch.exe $TARGET_DIR\Smarty_Compiler.class.php $TARGET_DIR\Smarty_Compiler.class.php.patch
dos2unix.exe $TARGET_DIR\Smarty_Compiler.class.php.patch
echo "Go back to $CUR_DIR"
cd $CUR_DIR

#
# Patch header.tpl to support UTF-8
#
$TARGET_DIR="$ABS_PATH\phpdoc\phpDocumentor\Converters\HTML\Smarty\templates\PHP\templates"
if (! (Test-Path $TARGET_DIR)) {
    echo "Error. Directory $TARGET_DIR not exist."
    exit 1
}
if (! (Test-Path $TARGET_DIR\header.tpl)) {
    echo "Error. Regular file $TARGET_DIR\header.tpl not exist."
    exit 1
}
if (! (Test-Path $TARGET_DIR\header.tpl.patch)) {
    echo "Error. Regular file $TARGET_DIR\header.tpl.patch not exist."
    exit 1
}

echo "Change directory to $TARGET_DIR"
cd $TARGET_DIR
unix2dos.exe $TARGET_DIR\header.tpl
unix2dos.exe $TARGET_DIR\header.tpl.patch
patch $TARGET_DIR\header.tpl $TARGET_DIR\header.tpl.patch
dos2unix.exe $TARGET_DIR\header.tpl.patch

#
# Rename tamplate cause Smarty expect .tpl extension (bug in some PHPDoc distribs)
#
if ((Test-Path $TARGET_DIR\pkgelementindex.tp)) {
    echo "Rename $TARGET_DIR\pkgelementindex.tp to $TARGET_DIR\pkgelementindex.tpl"
    mv pkgelementindex.tp pkgelementindex.tpl
}

echo "Go back to $CUR_DIR"
cd $CUR_DIR
exit 0
