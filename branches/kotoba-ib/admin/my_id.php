<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

require '../kwrapper.php';

kotoba_setup($link, $smarty);
$smarty->assign('id', $_SESSION['user']);
$smarty->display('my_id.tpl');
?>