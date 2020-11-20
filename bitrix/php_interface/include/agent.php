<?
//Функция проверяет наличие акций с истекшим сроком и отправляет сообщение на email всем, кто в группе Администраторы
function AgentCheckCurrency()
{
    if(CModule::IncludeModule("iblock"))
    {   
        //Получаем переменные из инфоблока
        $arSelect = Array("ID", "NAME");
        $arFilter = Array("DISCOUNT_IBLOCK_ID"=>4, "!ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arItems[] = $ob->GetFields();
        }
        //Делает запись в лог
        CEventLog::Add(array(
            "SEVERITY" => "SECURITY",
            "AUDIT_TYPE_ID" => "CHECK_DISCOUNT",
            "MODULE_ID" =>"iblock",
            "ITEM_ID" => "",
            "DESCRIPTION" => "Проверка актуальности, устарели ".count($arItems)." элемента",
        ));
    
        //Проверяем наличие акций. Отправляем письма, если есть просроченные акции
        if(count($arItems)>0)
        {
            $filter = array("GROUP_ADMIN_ID" => array(1));
            $rsUsers = CUser::GetList(($by="personal_country"), ($order="desc"), $filter);// выбираем пользователей
            $arEmail = array();
            while($arUser = $rsUsers->GetNext())
            {
                $arEmail[] = $arUser['EMAIL'];
            }
    
            if(count($arEmail)>0)
            {
                $arEventFields=array(
                    "TEXT" => count($arItems),
                    "EMAIL" => implode(", ", $arEmail)
                );
    
                CEvent::Send("CHECK_DISCOUNT", SITE_ID, $arEventFields);
            }
        }
    }
    return "AgentCheckCurrency();";
}
?>