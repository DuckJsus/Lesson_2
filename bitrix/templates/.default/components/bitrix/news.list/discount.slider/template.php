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

?>
<script>
	$().ready(function(){
		$(function(){
			$('#slides').slides({
				preload: false,
				generateNextPrev: false,
				autoHeight: true,
				play: 4000,
				effect: 'fade'
			});
		});
	});
</script>

<div class="sl_slider" id="slides">
	<div class="slides_container">
		<?foreach($arResult["ITEMS"] as $arItem):?>
		<div>
			<div>
				<?if(is_array($arItem["DETAIL_PICTURE"])):?>
				<a href="<?=$arItem["PROPERTY_LINK_PROD_DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["DETAIL_PICTURE"]["src"]?>" alt="" /></a>
				<?endif;?>
				<h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a></h2>
				<p><?echo $arItem["PROPERTY_LINK_PROD_NAME"]?> всего за <?echo $arItem["PROPERTY_LINK_PROD_PROPERTY_PRICE_VALUE"]?> руб</p>
				<a href="<?=$arItem["PROPERTY_LINK_PROD_DETAIL_PAGE_URL"]?>" class="sl_more">Подробнее &rarr;</a>
			</div>
		</div>
		<?endforeach;?>
	</div>
</div>