<?
//Подключили до попытки изменения пользователя
AddEventHandler("main", "OnBeforeUserUpdate", "OnBeforeUserUpdateEditor");

function OnBeforeUserUpdateEditor(&$arFields)
{  
    //Записываем в NOW_IN_GROUP был ли пользователь контент-редактором изначально
    if(!in_array(GROUP_CONTENT_EDITOR_ID, CUser::GetUserGroup($arFields["ID"])))
    {
        $arFields["CONTENT_EDITOR"] = false;
    }else{
        $arFields["CONTENT_EDITOR"] = true;
    } 
}
//Подключили после попытки изменения пользователя
AddEventHandler("main", "OnAfterUserUpdate", "OnAfterUserUpdateEditor");
//Добавили почтовое событие для оповещения других контент-редакторов
AddEventHandler("main", "OnBeforeEventAdd", "OnBeforeEventAddEditor");

function OnAfterUserUpdateEditor(&$arFields)
{
    
    if(!$arFields["CONTENT_EDITOR"])//Пользователь не был контент-редактором
    {
        if(in_array(GROUP_CONTENT_EDITOR_ID, CUser::GetUserGroup($arFields["ID"])))//Его добавили в контент-редакторы
        {
            //Находим всех пользователей из группы
            $arIDs = CGroup::GetGroupUser(GROUP_CONTENT_EDITOR_ID);
            $cnt = count($arIDs);
            $x = 0;
            while ($x<$cnt)
            {
                $rsUsers = CUser::GetByID($arIDs[$x]);
                $arUsers[] = $rsUsers->Fetch();
                $arEmail[] = $arUsers["EMAIL"];
                $x += 1;
            }

            



            //Почтовое событие
            $arFields = array(
                "ID"          => 32,
                "CONTRACT_ID" => 1,
                "TYPE_SID"    => "LEFT",
                "EMAIL" => "ialecs1997@gmail.com"
                );
            CEvent::Send("ADD_CONTENT_EDITOR", s1, $arFields);
            
            
        }
    }
}
?>