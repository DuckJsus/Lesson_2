<?
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("CIBlockDeactivate", "OnBeforeIBlockElementUpdateDeactivate"));

    if(CModule::IncludeModule("iblock"))
    {
        $arSelect = Array("DATE_CREATE", "ACTIVE");
        $arFilter = Array("NEWS_IBLOCK_ID", "ID"=>$arFields["ID"]);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arItems[] = $ob->GetFields();
        }
    }
    
    if($arItems[0]["ACTIVE"] == "Y")
    {
        class CIBlockDeactivate
        {
            function OnBeforeIBlockElementUpdateDeactivate(&$arFields)
            {
                global $arItems;
                if($arFields['IBLOCK_ID'] == NEWS_IBLOCK_ID)
                {
                    $date_create = $arItems[0]["DATE_CREATE"];// зададим дату создания новости
                    $date_curr = ConvertTimeStamp();// зададим текущую дату
                    $stmp_create = MakeTimeStamp($date_create, "DD.MM.YYYY HH:MI:SS");//Преобразуем вермя в UNIX формат
                    $stmp_curr = MakeTimeStamp($date_curr, "DD.MM.YYYY HH:MI:SS");//Преобразуем вермя в UNIX формат
                    //Сравнение времени в днях
                    $curr = ($stmp_curr - $stmp_create)/(60*60*24);
                    //При деактивации свежей новости (< 3 дней от созданий) выводить предупреждение и отменять деактивацию
                    if(($curr <= 3) && ($arFields["ACTIVE"] == "N"))
                    { 
                        global $APPLICATION;
                        $APPLICATION->throwException("Вы деактивировали свежую новость");
                        return false;
                    }
                }
            }
        }
    }
?>