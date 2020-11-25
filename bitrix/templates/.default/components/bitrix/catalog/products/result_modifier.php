<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?$this->SetViewTarget('manufacturer_cnt');?>
    <?
    $arSelect = Array("ID", "NAME", "PROPERTY_MANUFACTURER");
    $arFilter = Array("IBLOCK_ID"=>CATALOG_IBLOCK_ID, "ACTIVE"=>"Y");
    $arGroup = array("PROPERTY_MANUFACTURER");
    $res = CIBlockElement::GetList(Array(), $arFilter, $arGroup, Array("nPageSize"=>50), $arSelect);
    while($ob = $res->GetNextElement())
    {
    $arFields = $ob->GetFields();
    echo "<div>".$arFields["PROPERTY_MANUFACTURER_VALUE"]." - ".$arFields["CNT"]."</div>";
    }
    ?> 
<?$this->EndViewTarget();?>