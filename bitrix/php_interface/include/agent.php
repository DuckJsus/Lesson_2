<?
//Функция проверяет наличие акций с истекшим сроком и отправляет сообщение на email всем, кто в группе Администраторы
function AgentNoticeDatedSale()
{
    if(CModule::IncludeModule("iblock"))
    {   
        //Get the number of overdue discounts
        $arFilter = array("IBLOCK_ID" => DISCOUNT_IBLOCK_ID, "!ACTIVE_DATE" =>"Y", "ACTIVE"=>"Y");
        $res_count = CIBlockElement::GetList(array(), $arFilter, array(), false, false);

        //Check overdue discount
        if($res_count>0)
        {
            // Get list email of admins
            $arFilter = array("GROUPS_ID" => ADMIN_GROUP_ID);
            $arParameters["FIELDS"] = array("EMAIL");
            $rsEmails = CUser::GetList(($by="id"), ($order="desc"), $arFilter, $arParameters);
            while($ob = $rsEmails->Fetch())
            {
                $arEmails[] = $ob["EMAIL"];
            }
            $strEmails = implode(", ",$arEmails);

            //Send entry to the log
            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "NOTICE_DATED_SALE",
                "MODULE_ID" =>"iblock",
                "ITEM_ID" => "",
                "DESCRIPTION" => "Проверка актуальности, устарело акций: ".$res_count,
            ));

            //Send an email notification to administrators
            if($strEmails)
            {
                $arEventFields = array(
                    "TEXT" => "Проверка актуальности, устарело акций: ".$res_count,
                    "EMAIL" => $strEmails,
                );
                CEvent::Send("NOTICE_DATED_SALE", SITE_ID, $arEventFields);
            }
        }   
    }
    return "AgentNoticeDatedSale();";
}
?>