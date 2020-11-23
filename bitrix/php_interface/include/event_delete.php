<?
/*AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdateHandler");
 // создаем обработчик события "OnBeforeIBlockElementUpdate"
function OnBeforeIBlockElementUpdateHandler(&$arFields)
{
    CModule::IncludeModule("iblock");
    $arSelect = Array("ID", "SHOW_COUNTER", "NAME");
    $arFilter = Array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $arFields["ID"]);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    while($ob = $res->GetNextElement())
    $arItems = $ob->GetFields();
    
    
    if($arItems["SHOW_COUNTER"] > 1)
    {
        $arFields = Array(
            "ACTIVE"=>"N"
        );
        $ibp = new CIBlockProperty;
        $ibp->Update($arFields["ID"], $arFields);

        
    }
}*/



AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "OnBeforeIBlockElementDeleteHandler");


// создаем обработчик события "OnBeforeIBlockElementDelete"
function OnBeforeIBlockElementDeleteHandler($ID)
{
    CModule::IncludeModule("iblock");
    $arSelect = Array("ID", "SHOW_COUNTER", "NAME");
    $arFilter = Array("CATALOG_IBLOCK_ID");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(false), $arSelect);
    while($ob = $res->GetNextElement())
    $arItems = $ob->GetFields();
    
    
    
        $arFields = Array(
            "ACTIVE"=>"N"
        );
        $ibp = new CIBlockProperty;
        $ibp->Update($arFields["ID"], $arFields);

     
        global $APPLICATION;
        $APPLICATION->throwException("элемент с ID=1 нельзя удалить.");
        return false;
    
}


?>