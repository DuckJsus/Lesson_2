<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$this->addExternalCss("/bitrix/templates/.default/components/mycomponents/news.list/vacancy/template_style.css");
$this->addExternalJS("/bitrix/templates/.default/js/jquery-1.8.2.min.js");
$this->addExternalJS("/bitrix/templates/.default/js/functions.js");
?>

<div class="sb_nav">
	<?foreach($arResult["ITEMS"] as $arSect):?>
		<?if($arSect["ELEM_TYPE"] == "SECTION"):?>
			<b><?if($arSect["NAME"]):?></b>
				<ul>
				<li class="close">
				<span class="sb_showchild"></span>
				<a><span><?echo $arSect["NAME"]?><br /></span></a>
					<?foreach($arResult["ITEMS"] as $arItem):?>
						<?if(($arItem["ELEM_TYPE"] != "SECTION") && ($arSect["ID"] == $arItem["IBLOCK_SECTION_ID"])):?>
							<ul>
							<li><a>
								<!--Эрмитаж-->
								<?
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage(
									'CT_BNL_ELEMENT_DELETE_CONFIRM')));
								?>
								<div id="<?=$this->GetEditAreaId($arItem['ID']);?>">
								<?if($arItem["NAME"]):?>
									<p><b><?echo $arItem["NAME"]?></b></p><br />
								<?endif;?>
								<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
									<p>
									<b><?=$arProperty["NAME"]?>:&nbsp;</b>
									<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
										<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
									<?else:?>
										<?=$arProperty["DISPLAY_VALUE"];?>
									<?endif?>
									</p><br />
								<?endforeach;?>
								<?if($arParams["DISPLAY_DETAIL_TEXT"] == "Y" && $arItem["DETAIL_TEXT"]):?>
									<p><b><?=GetMessage("DESCRIPTION_DETAIL_TEXT");?></b><?echo $arItem["DETAIL_TEXT"];?></p><br />
								<?endif;?>
								</div>
								</a></li>	
							</ul>
						<?endif;?>
					<?endforeach;?>
				</li>
				</ul>	
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
</div>
<?dump($arResult);?>