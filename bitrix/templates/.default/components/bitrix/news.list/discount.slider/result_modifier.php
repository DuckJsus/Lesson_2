<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//Изменение размера изображения на лету
foreach($arResult["ITEMS"] as $ID=>$arItems){
    $arImage=CFile::ResizeImageGet($arItems["DETAIL_PICTURE"], array('width'=>$arParams["LIST_PREV_PICT_W"], 'height'=>$arParams["LIST_PREV_PICT_H"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    $arResult["ITEMS"][$ID]["DETAIL_PICTURE"]=$arImage;
}

//Вывод данных привязанного элемента через GetList
$arTempID = array();
$catalogIBlockID = false;
foreach($arResult["ITEMS"] as $elem)
{
    $arTempID[] = $elem["PROPERTIES"]["LINK_PROD"]["VALUE"];
    $catalogIBlockID = $elem["PROPERTIES"]["LINK_PROD"]["LINK_IBLOCK_ID"];
}
$arSort = false;
$arFilter = array(
    "IBLOCK_ID" => $catalogIBlockID,
    "ACTIVE" => "Y",
    "ID" => $arTempID
);
$arGroupBy = false;
$arNavStartParams = array(
    "nTopCount" => $arParams["NEWS_COUNT"],
);
$arSelect = array("ID","NAME","DETAIL_PAGE_URL","PROPERTY_PRICE");
$BDRes = CIBlockElement::GetList(
    $arSort,
    $arFilter,
    $arGroupBy,
    $arNavStartParams,
    $arSelect
);
$arResult["PROD_ELEM"] = array();
while($arRes = $BDRes->GetNext())
{
    $arResult["PROD_ELEM"][$arRes["ID"]] = $arRes;
}
