<?
AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "OnBeforeIBlockElementDeleteHandler");
// создаем обработчик события "OnBeforeIBlockElementDelete"
function OnBeforeIBlockElementDeleteHandler($ID)
{
    CModule::IncludeModule("iblock");
    $arSelect = Array("ID", "SHOW_COUNTER", "NAME");
    $arFilter = Array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $ID);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    while($ob = $res->GetNextElement())
    $arItems = $ob->GetFields();
    
    if($arItems["SHOW_COUNTER"]>1)//Если просмотров больше 1
    {
        (new CIBlockElement())->Update($ID, ['ACTIVE'=>'N']);//Деактивируем элемент
        $GLOBALS['DB']->Commit();//Сохраняем в ДБ предыдущие данные
        global $APPLICATION;
        $GLOBALS['APPLICATION']->throwException("Элемент нельзя удалить, у него ".$arItems["SHOW_COUNTER"]." просмотров.");
        return false;//Отменяем удаление
    } 
}
?>