<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    use Bitrix\Main\Page\Asset;
    Asset::getInstance()->addJs("/bitrix/templates/.default/components/bitrix/news.list/discount.slider/jquery-1.8.2.min.js");
    Asset::getInstance()->addJs("/bitrix/templates/.default/components/bitrix/news.list/discount.slider/slides.min.jquery.js");
    Asset::getInstance()->addCss("/bitrix/templates/.default/components/bitrix/news.list/discount.slider/style.css");
?>