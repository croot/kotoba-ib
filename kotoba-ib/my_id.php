<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/*
 * Script shows user id and groups.
 */

require_once dirname(__FILE__) . '/config.php';
require_once Config::ABS_PATH . '/lib/misc.php';
require_once Config::ABS_PATH . '/lib/db.php';
require_once Config::ABS_PATH . '/lib/exceptions.php';

try {
    // Initialization.
    kotoba_session_start();
    if (Config::LANGUAGE != $_SESSION['language']) {
        require Config::ABS_PATH
            . "/locale/{$_SESSION['language']}/messages.php";
    }
    locale_setup();
    $smarty = new SmartyKotobaSetup();

    // Check if client banned.
    if ( ($ban = bans_check(get_remote_addr())) !== FALSE) {

        // Cleanup.
        DataExchange::releaseResources();

        $smarty->assign('ip', $_SERVER['REMOTE_ADDR']);
        $smarty->assign('reason', $ban['reason']);
        $smarty->display('banned.tpl');

        session_destroy();
        exit(1);
    }

    // Generate my id page html-code and display it.
    $smarty->assign('show_control', is_admin() || is_mod());
    $smarty->assign('boards', boards_get_visible($_SESSION['user']));
    $smarty->assign('id', $_SESSION['user']);
    $smarty->assign('groups', $_SESSION['groups']);
    $smarty->display('my_id.tpl');
    if (isset($_REQUEST['kuga'])) {
        echo take_it_easy();
    }

    // Cleanup.
    DataExchange::releaseResources();

    exit(0);
} catch(KotobaException $e) {

    // Cleanup.
    DataExchange::releaseResources();

    display_exception_page($smarty, $e, is_admin() || is_mod());
    exit(1);
}
?>
<?php
function take_it_easy() {
return <<<KUGA
<tt><br>,:7=OOOOO8OOONOOOOOOOOOOOOOOOO,,,,,,,,,,8OOO8OOOOOOOOOOOOOO8OOOOOOOOOOOOOOOODDOO<br>
.:IOOOOODOODD8OOOOOOOOOOOOOO8:,,,,,,,,,,,,,8OOOO8OO8OOOOOOO8OOOOOOOOOOOOOOOODDOO<br>
,,IOOOOODOODDOOOOOOOOOOOOOO8~,,,,,,,,,,,,,,,,,,7D8OON8O88OODOOOOOOOOOOOOOOOONDDO<br>
.,8OOOO8OO8DOOOOOOOOOOOOOO8=,,,,,,,,,,,,,,,,,,,,,,,,,7DDDDNDOOOOOOOOOOOOOOOODDDO<br>
.,OOOOODOODDOOOOOOOOOOOOD8+,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,:DOOOOOOOOOOOOO8OODDD8<br>
.,OOOOODOODOOOOOOOOOOOOD8+,,,,,,,,,,,,,,,,,,,,,D,,,,,,,,,,:8OOOOOOOOOOOOODOODDDD<br>
.,OOOOO8OONOOOOOOOOOOODD+,,,,,,,,,,,,,,,,,,,O,,,,,,,,,,,,,:8OOOOOOOOOOOOOOODD++N<br>
,,OOOOD8ODDOOOOOOOOOODD+:,,,,,,,,,,,,,,,,:O:,,,,,,,,,,,,,,,8OOOOOOOOOOOOOOO8D+$:<br>
.,OOOOD8ODOOOOOOOOOODN=,,,,,,,,,,,,,,,:,O,,,,,,,,,,,,,,,,,:OOOOOOOOOOOOOOOOO8+++<br>
.,OOOOOOODOOOOOOOO8DO~,,,,,,,,,,,,,,,,O7::,,7MNZOM8,,,,,,,7OOOOOOOOOOOOOOOOOO+~,<br>
~=OOOOOOOD8OOOOOODN$~,,,,,,,,,,,,,,,,8:I,7M++DM:..?+M,,,,,8OOOOOOOOOOOOO8OOOO=,,<br>
77OOOOOO8D8O8OOODD+:,,,,,,,,,,,,,,,OI,,,M+:.MDD,..N,$,,,,:OOOOOOOOOOOOOOOOOO8+$:<br>
::ZOOOOODDDODOOD8+,,,,,,,,,,,,,,,,O,,,7D:...DO8MMO8N.,,,,,OOOOOOOOOOOOOOOO8O8$,,<br>
+?OOOOOODDDDO8N+:,,,,,,,,,,,,,,,~8+,,,N.....NO77778=,,:,,:OOOOOOOOOOOOOOOO8OD,,N<br>
IIOOOOOODDD8D=+,,,,,,,,,,,:,,,,8++,,,,,......+OO8N8=:::::,OOOOOOOOOOOOOOOOOOD+8D<br>
??OOOOOODDDD+:,,,,,,,,,:,,,:,?,,+,,,,,,,......~D:=~~~~:::~8OOOOOOOOOOOODOOOO8\$DD<br>
,,ZO8OOODDD+,Z8$$8OOOO$+:,,,,,,,,,,,,,::::::~~~~~~=~=~~::7DOOOOOOOOOOOODOOOODDDD<br>
.,OO8OOODDD,=,,,,:7,:~+=~,,,,,,,,,,,:::::~~~=~~=~~~~~~~::8DOOOOOOOOOOOONOOOO8DDD<br>
.,OOOOOODDDN,,,,,D+ M.M$,,,,,,,,,,,::::~=~=~~~~~~~~~~::::ODOOOOOOOOOOODDOOOODDDD<br>
.,OOOOOO8DDDD,,,M+.DD+D,,,,,,,,,,,,::=~~~~~~~~~::::::::::O8OOOOOOOOOOODDOOOODDDD<br>
.,OOOOOOODDDDD,,D+.D$\$Z$,,,,,,,,,,,,::::::::::::::::::,,:O8OOOOOOOOOO8DDOOOODDDD<br>
.,ODOOOOODDDDDN,,=...8N,,,,,,,,,,,,,:::::::::::::,,,,,,,,OOOOOOOOOOOOODDOOOONDDD<br>
.,ODOOOOODDDDDDN,,,...,:::~::,,,,,,,,,,,,,,,,,,,,,,,,,,,?OOOOOOOOOOOOODDO8OODDDD<br>
.,ODOOOOODDDDDDD$::::~~=~:::,,,,,,,,,,,,,,,,,,,,,,,,,,,,D8OOOOOOOOOOOODDO8OODDDD<br>
.,ODOOOOODDDDDDDN=::=~=~~::+:,,,,,,,,,,,,,,,,,,,,,,,,,,,ODOOOOOOOOO8OODDO8OODDDD<br>
.,ONOOOOOODDDDDDDD:~~~~::::+:,,,,:,,,,,,,,,,,,,,,,,,,,,,ODOOOOOOOOOOODODODOOODDD<br>
.,ODOOOO8ODDDDDDDDN:~::::,++=+:,,,,,,,,,,,,,,,,,,,,,,,=.ONOOOOOOOO8OODODODOOODDD<br>
.,8DOOOO8ODDDDDDDDZ,::::,,,=+++,,,,,,,,,,,,,,,,,,,,,,,,IODOOOOOOOODOODODODOOO8DD<br>
.,DDOOOOOODDDDDDDD,?,,,,,,,,,~,,,,,,,,,,I.+O:,,,,,,,,,,888OOOOOOO8OODOODODOOOODD<br>
.,DDOOOOOODDDDDDDD.,M,,,,,,,,,,,,,,,,=.....~,,,,,,,,,,,ODOOOOOOOODOODOODOD8OOODD<br>
.,DDOOOOOODDDDDDDN,,,M,,,,,,,,,,,,,O~:,,,,,,,,,,,,,,,,:ODOOOOOOOODOOOOO8OD8OOODD<br>
,,NDOOOOOODDDDDDDI::~=D,,,,,,,,,,,,,,,,=++,,,,,,,,,,,,8ODOOOOOOODOODOOOOOD8OOODD<br>
::NDOOODOODDDDDDD??+==~~D::,,,,,,,,,,,,,,,,,,,,,,,,,,,O8DOOOOOO8DOO8OOOOOD8OOO8D<br>
IIDDOOOOOODDDDDD8,:::::~~OD7~,,,,,,,,,,,,,,,,,,,,,,,,~ODDOOOOOONDOOOOOOOODDOOOOD<br>
~~DDOOOOOODDDDDDI777777I?ZODDDN+:,,,,,,,,,,,,,,,,,,,,8OD8OOOOOODDO8OOOOOODDOOOOD<br>
77DDOO8OOODDDDD8~::::,,,,:8DDDDDDDN=:,,,,,,,,,,,,,,,,ODDOOOOOODD8OOOOOOOODDOOOOD<br>
==\$DOODOOODDDDD:~~==+??IIII8DDDDDDDDDDD+,,,,,,,,,,,?+ODDOOOOOODDOOOOOOOO8DDOOOO8</tt>
KUGA;
}
?>