<?
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdateDeactivate");//Если новость создана >= 3 дней назад идеактивируется, то отменяет деактивацию и выводит сообщение

function OnBeforeIBlockElementUpdateDeactivate(&$arFields)
{
    if(($arFields["IBLOCK_ID"] == NEWS_IBLOCK_ID) && ($arFields["ACTIVE"] == 'N'))//Проверяет, что инфоблок == Новости и элемент неактивенна момент изменения
    {
        $res = CIBlockElement::GetByID($arFields["ID"]);//получаем состояние новости до начала изменений
        $arItems = $res->Fetch();

        if($arItems["ACTIVE"] == 'Y')
        {
            $stmp_create = MakeTimeStamp($arItems["DATE_CREATE"], "DD.MM.YYYY HH:MI:SS");//Date create in UNIX
            $stmp_curr = time();//Current date in UNIX
            $stmp = ($stmp_curr - $stmp_create) / 86400;//Days have passed since the creation date
            
            if($stmp >= 3)//Элемент создан более 3 дней назад
            {
                global $APPLICATION;
                $APPLICATION->throwException("Вы деактивировали свежую новость");//Отменяем деактивацию ивыводим сообщение
                return false;
            }
        }
    }
}