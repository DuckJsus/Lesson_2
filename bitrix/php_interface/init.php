<?
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/constants.php"))
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/constants.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/functions.php"))
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/functions.php");
	
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/agent.php"))
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/agent.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/event_deactivate.php"))
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/event_deactivate.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/event_delete.php"))
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/event_delete.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/event_adduser.php"))
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/event_adduser.php");
?>

