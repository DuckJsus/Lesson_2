<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

/** @global CIntranetToolbar $INTRANET_TOOLBAR */
global $INTRANET_TOOLBAR;

use Bitrix\Main\Context,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Iblock;


if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if($arParams["IBLOCK_TYPE"] == '')
	$arParams["IBLOCK_TYPE"] = "news";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);
$arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N";
$arParams["SET_LAST_MODIFIED"] = $arParams["SET_LAST_MODIFIED"]==="Y";


$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

if($this->startResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arNavigation, $arrFilter, $pagerParameters)))
{
	if(!Loader::includeModule("iblock"))
	{
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if(is_numeric($arParams["IBLOCK_ID"]))
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		));
	}
	else
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		));
	}

	$arResult = $rsIBlock->GetNext();
	if (!$arResult)
	{
		$this->abortResultCache();
		Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
			,true
			,$arParams["SET_STATUS_404"] === "Y"
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);
		return;
	}

	$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
	//SELECT
	$arSelect = array_merge($arParams["FIELD_CODE"], array(
		"ID",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"NAME",
		"ACTIVE_FROM",
		"TIMESTAMP_X",
		"DETAIL_PAGE_URL",
		"LIST_PAGE_URL",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"PREVIEW_PICTURE",
	));
	$bGetProperty = !empty($arParams["PROPERTY_CODE"]);
	//WHERE
	$arFilter = array (
		"IBLOCK_ID" => $arResult["ID"],
		"IBLOCK_LID" => SITE_ID,
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => $arParams['CHECK_PERMISSIONS'] ? "Y" : "N",
	);

	if($arParams["CHECK_DATES"])
		$arFilter["ACTIVE_DATE"] = "Y";

	$PARENT_SECTION = CIBlockFindTools::GetSectionID(
		$arParams["PARENT_SECTION"],
		$arParams["PARENT_SECTION_CODE"],
		array(
			"GLOBAL_ACTIVE" => "Y",
			"IBLOCK_ID" => $arResult["ID"],
		)
	);

	if (
		$arParams["STRICT_SECTION_CHECK"]
		&& (
			$arParams["PARENT_SECTION"] > 0
			|| $arParams["PARENT_SECTION_CODE"] <> ''
		)
	)
	{
		if ($PARENT_SECTION <= 0)
		{
			$this->abortResultCache();
			Iblock\Component\Tools::process404(
				trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
				,true
				,$arParams["SET_STATUS_404"] === "Y"
				,$arParams["SHOW_404"] === "Y"
				,$arParams["FILE_404"]
			);
			return;
		}
	}

	$arParams["PARENT_SECTION"] = $PARENT_SECTION;

	if($arParams["PARENT_SECTION"]>0)
	{
		$arFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
		if($arParams["INCLUDE_SUBSECTIONS"])
			$arFilter["INCLUDE_SUBSECTIONS"] = "Y";

		$arResult["SECTION"]= array("PATH" => array());
		$rsPath = CIBlockSection::GetNavChain($arResult["ID"], $arParams["PARENT_SECTION"]);
		$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"], $arParams["IBLOCK_URL"]);
		while($arPath = $rsPath->GetNext())
		{
			$ipropValues = new Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arPath["ID"]);
			$arPath["IPROPERTY_VALUES"] = $ipropValues->getValues();
			$arResult["SECTION"]["PATH"][] = $arPath;
		}

		$ipropValues = new Iblock\InheritedProperty\SectionValues($arResult["ID"], $arParams["PARENT_SECTION"]);
		$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
	}
	else
	{
		$arResult["SECTION"]= false;
	}
	//ORDER BY
	$arSort = array(
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	);
	if(!array_key_exists("ID", $arSort))
		$arSort["ID"] = "DESC";

	$shortSelect = array('ID', 'IBLOCK_ID');
	foreach (array_keys($arSort) as $index)
	{
		if (!in_array($index, $shortSelect))
		{
			$shortSelect[] = $index;
		}
	}

	$arResult["ITEMS"] = array();
	$arResult["ELEMENTS"] = array();
	$rsElement = CIBlockElement::GetList($arSort, array_merge($arFilter , $arrFilter), false, $arNavParams, $shortSelect);
	while ($row = $rsElement->Fetch())
	{
		$id = (int)$row['ID'];
		$arResult["ITEMS"][$id] = $row;
		$arResult["ELEMENTS"][] = $id;
	}
	unset($row);

	if (!empty($arResult['ITEMS']))
	{
		$elementFilter = array(
			"IBLOCK_ID" => $arResult["ID"],
			"IBLOCK_LID" => SITE_ID,
			"ID" => $arResult["ELEMENTS"]
		);

		$obParser = new CTextParser;
		$iterator = CIBlockElement::GetList(array(), $elementFilter, false, false, $arSelect);
		$iterator->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);
		while ($arItem = $iterator->GetNext())
		{
			$arButtons = CIBlock::GetPanelButtons(
				$arItem["IBLOCK_ID"],
				$arItem["ID"],
				0,
				array("SECTION_BUTTONS" => false, "SESSID" => false)
			);
			$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

			if ($arParams["PREVIEW_TRUNCATE_LEN"] > 0)
				$arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);

			if ($arItem["ACTIVE_FROM"] <> '')
				$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
			else
				$arItem["DISPLAY_ACTIVE_FROM"] = "";

			Iblock\InheritedProperty\ElementValues::queue($arItem["IBLOCK_ID"], $arItem["ID"]);

			$arItem["FIELDS"] = array();

			if ($bGetProperty)
			{
				$arItem["PROPERTIES"] = array();
			}
			$arItem["DISPLAY_PROPERTIES"] = array();

			$id = (int)$arItem["ID"];
			$arResult["ITEMS"][$id] = $arItem;
		}
		unset($obElement);
		unset($iterator);

		if ($bGetProperty)
		{
			unset($elementFilter['IBLOCK_LID']);
			CIBlockElement::GetPropertyValuesArray(
				$arResult["ITEMS"],
				$arResult["ID"],
				$elementFilter
			);
		}
	}

	//Set element properties
	$arResult['ITEMS'] = array_values($arResult['ITEMS']);

	foreach ($arResult["ITEMS"] as &$arItem)
	{
		if ($bGetProperty)
		{
			foreach ($arParams["PROPERTY_CODE"] as $pid)
			{
				$prop = &$arItem["PROPERTIES"][$pid];
				if (
					(is_array($prop["VALUE"]) && count($prop["VALUE"]) > 0)
					|| (!is_array($prop["VALUE"]) && $prop["VALUE"] <> '')
				)
				{
					$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "news_out");
				}
			}
		}

		$ipropValues = new Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
		$arItem["IPROPERTY_VALUES"] = $ipropValues->getValues();
		Iblock\Component\Tools::getFieldImageData(
			$arItem,
			array('PREVIEW_PICTURE', 'DETAIL_PICTURE'),
			Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
			'IPROPERTY_VALUES'
		);

		foreach($arParams["FIELD_CODE"] as $code)
			if(array_key_exists($code, $arItem))
				$arItem["FIELDS"][$code] = $arItem[$code];
	}
	unset($arItem);

	//Set sections list
	$arSelect = array("ID", "NAME");
	$arFilter = array('IBLOCK_ID'=>$arParams["IBLOCK_ID"]); 
	$rsSect = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, $arSelect, false);
	while ($arSect = $rsSect->GetNext())
	{
		$arSect["ELEM_TYPE"] = "SECTION";
		$arResult["ITEMS"][] = $arSect;
	}

	$this->includeComponentTemplate();
}