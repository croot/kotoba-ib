<?php
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/
?>
<?php
require 'config.php';
require 'common.php';
require 'error_processing.php';
require 'events.php';

$smarty = new SmartyKotobaSetup();
if(isset($_POST['Keyword']))
{
	$smarty->assign('form', 0);
	$keyword_code   = RawUrlEncode($_POST['Keyword']);
	$keyword_length = strlen($keyword_code);
	
	if($keyword_length >= 16 && $keyword_length <= 32)
	{
		$keyword_hash = md5($keyword_code);		
		
		require 'databaseconnect.php';
		$sql = sprintf("select id from users where `Key` = '%s'", $keyword_hash);
		if(($result = mysql_query($sql)) != false)
		{
			if(mysql_num_rows($result) == 0)
			{
				$sql = sprintf("insert into users (`Key`, `SID`, `User Settings`) values ('%s', null, '')", $keyword_hash);
				if(mysql_query($sql) != false)
				{
					$smarty->assign('message', REG_SUCCESSFUL);
				}
				else
				{
					kotoba_error(sprintf(ERR_REGISTER_DATABASE, mysql_error()));
				}
			}
			else
			{
				$sql = sprintf("delete from users where `Key` = '%s'", $keyword_hash);
				if(mysql_query($sql) != false)
				{
					$smarty->assign('message', REG_UNREGISTERED);
				}
				else
				{
					kotoba_error(sprintf(ERR_REGISTER_UNREGISTER, mysql_error()));
				}
			}
		}
		else
		{
			kotoba_error(sprintf(ERR_REGISTER_DATABASE, mysql_error()));
		}
	}
	else
	{
		$smarty->assign('form', 1);
		$smarty->assign('message', ERR_BADKEYWORD);
	}
}
else {
	$smarty->assign('form', 1);
}

$smarty->display('register.tpl');
?>
