<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

//Получаем производителей с количеством товаров оттуда
$arSelect = Array("ID", "NAME", "PROPERTY_MANUFACTURER");
$arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y");
$arGroup = array("PROPERTY_MANUFACTURER");
$res = CIBlockElement::GetList(Array(), $arFilter, $arGroup, false, $arSelect);
while($ob = $res->GetNextElement())
{
$arResult["MANUFACTURER_CNT"][] = $ob->GetFields();
}
