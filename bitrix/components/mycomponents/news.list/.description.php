<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_IBLOCK_VAC_LIST"),
	"DESCRIPTION" => GetMessage("T_IBLOCK_VAC_LIST_VAC"),
	"SORT" => 20,
	"EDUCATION" => 1,

	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "extra",
		"NAME" => GetMessage("T_IBLOCK_EXTRA"),
		"CHILD" => array(
			"ID" => "vacancy",
			"NAME" => GetMessage("T_IBLOCK_VAC"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "vac_cmpx",
			),
		),
	),
);

?>