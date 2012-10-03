<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

namespace ru\azuchan;

require_once "config.php";
require_once "new_config.inc";

$page = new pages\LoginPage("Login");
echo $page->render();

\DataExchange::releaseResources();
exit(0);
?>
