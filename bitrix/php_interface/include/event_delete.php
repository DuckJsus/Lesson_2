<?
AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "OnBeforeIBlockElementDeleteHandler");

function OnBeforeIBlockElementDeleteHandler($ID)
{
    $res = CIBlockElement::GetByID($ID);//Получаем данные элемента
        $arItems = $res->Fetch();
    if($arItems["IBLOCK_ID"] == CATALOG_IBLOCK_ID)//Если инфоблок - Продукты
    {
        if($arItems["SHOW_COUNTER"] > 1)//Если просмотров больше 1
        {
            $GLOBALS['DB']->StartTransaction();
            (new CIBlockElement())->Update($ID, ['ACTIVE'=>'N']);//Деактивируем элемент
            $GLOBALS['DB']->Commit();//Сохраняем в ДБ предыдущие данные

            global $APPLICATION;
            $GLOBALS['APPLICATION']->throwException("Элемент нельзя удалить, количество просмотров: ".$arItems["SHOW_COUNTER"]);
            return false;//Отменяем удаление
        }  
    } 
}