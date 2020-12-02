<?
//Подключили до попытки изменения пользователя
AddEventHandler("main", "OnBeforeUserUpdate", "OnBeforeUserUpdateEditor");
//Подключили после попытки изменения пользователя
AddEventHandler("main", "OnAfterUserUpdate", "OnAfterUserUpdateEditor");

function OnBeforeUserUpdateEditor(&$arFields)
{
    //пользователь до попытки изменения не в группе
    if(!in_array(CONTENT_EDITOR_GROUP_ID, CUser::GetUserGroup($arFields["ID"])))
    {   
        global $arIDs;
        $arIDs = CGroup::GetGroupUser(CONTENT_EDITOR_GROUP_ID);//получаем ID пользователей, состоявших в группе изначально
        
        function OnAfterUserUpdateEditor(&$arFields)
        {
            //пользователь после попытки изменения в группе
            if(in_array(CONTENT_EDITOR_GROUP_ID, CUser::GetUserGroup($arFields["ID"])))
            {
                global $arIDs;
                //Получаем email пользователей в группе
                $strIDs = implode(",",$arIDs);
                $arFilter = array("GROUPS_ID" => CONTENT_EDITOR_GROUP_ID, "!iD" => $arFields["ID"]);
                $res = CUser::GetList(($by = "ID"), ($order = "asc"), $arFilter, array("FIELDS" => array("EMAIL")));
                while($ob = $res->Fetch())
                {
                    $arEmails[] = $ob["EMAIL"];
                }
                $strEmails = implode(",", $arEmails);

                //Отправляем оповещения по списку email`ов
                $arFields = array(
                    "EMAIL_TO" => $strEmails
                    );
                CEvent::Send("ADD_CONTENT_EDITOR", "s1", $arFields);

            }
        }
    }
}