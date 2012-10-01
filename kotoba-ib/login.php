<?php
require_once "config.default";  // TODO Should include config.php in production.

$page = new LoginPage();
$page->setArguments(array(
    "stylesheet" => "kusaba.css",
    "title" => "Login"
));

try {
    echo $page->render();
} catch (Exception $e) {
    echo $e;
    exit(1);
}

exit(0);
?>
