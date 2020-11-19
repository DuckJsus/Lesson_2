<?
$arTempID = array();
$catalogIBlockID = false;
foreach($arResult["ITEMS"] as $elem)
{
    $arTempID[] = $elem["PROPERTIES"]["LINK_CAT"]["VALUE"];
    $catalogIBlockID = $elem["PROPERTIES"]["LINK_CAT"]["LINK_IBLOCK_ID"];
}
$arSort = false;
$arFilter = array(
    "IBLOCK_ID" => IBLOCK_CAT_ID,
    "ACTIVE" => "Y",
    "ID" => $arTempID,
);
$arGroupBy = false;
$arNavStartParams = array("nTopCount" => 50);
$arSelect = array("ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_PRICE", "PROPERTY_MANUFACTURER");
$BDRes = CIBlockElement::GetList(
    $arSort,
    $arFilter,
    $arGroupBy,
    $arNavStartParams,
    $arSelect
);
$arResult["CAT_ELEM"] = array();
while($arRes = $BDRes->getNext())
{
    $arResult["CAT_ELEM"][$arRes["ID"]] = $arRes;
};
?>