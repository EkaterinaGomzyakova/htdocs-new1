<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION, $isShowSale, $arRegion, $isShowCompany, $isShowCatalogSections, $arShowPromoOnMainFilter, $isShowCatalogElements, $isShowMiddleAdvBottomBanner, $isShowBlog, $USER;
CModule::IncludeModule("wl.snailshop");

$APPLICATION->IncludeComponent(
	"wl:flashsale",
	".default",
	[
		'XML_ID' => 'BANNER',
	]
);
?>

<div class="maxwidth-theme">
	<div class="visible-xs mobile-search">
		<?php include($_SERVER['DOCUMENT_ROOT'] . "/include/top_page/search.title.catalog.page.php"); ?>
	</div>

	<div class="move-to-catalog visible-xs visible-sm">
		<a class="btn btn-lg btn-default" href="/catalog/">Каталог</a>
		<?php /*<a class="btn btn-lg btn-default new_year" href="/catalog/new_year/"><span class="fa fa-tree"></span> Новый год</a>*/ ?>
		<a class="btn btn-lg btn-default" href="/info/brands/">Бренды</a>
	</div>
</div>

<div class="maxwidth-theme index-banner">
	<?php
	$APPLICATION->IncludeComponent(
		"aspro:com.banners.next",
		"top_big_banners_swiper",
		array(
			"IBLOCK_TYPE" => "aspro_next_adv",
			"IBLOCK_ID" => "6",
			"TYPE_BANNERS_IBLOCK_ID" => "4",
			"SET_BANNER_TYPE_FROM_THEME" => "N",
			"NEWS_COUNT" => "10",
			"NEWS_COUNT2" => "4",
			"SORT_BY1" => "SORT",
			"SORT_ORDER1" => "ASC",
			"SORT_BY2" => "ID",
			"SORT_ORDER2" => "DESC",
			"PROPERTY_CODE" => array(
				0 => "TEXT_POSITION",
				1 => "TARGETS",
				2 => "TEXTCOLOR",
				3 => "URL_STRING",
			),
			"CHECK_DATES" => "Y",
			"CACHE_GROUPS" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600",
			"BANNER_TYPE_THEME" => "TOP",
			"COMPONENT_TEMPLATE" => "top_big_banners_swiper",
			"FILTER_NAME" => "arRegionLink",
			"CATALOG" => "/catalog/"
		),
		false
	);
	?>
</div>
<!-- Блок с кнопками-картинками "Бренды" и "Sale" -->
<div class="homepage-buttons-container">

    <!-- Ссылка-кнопка "Бренды" -->
    <a href="/info/brands/index.php" class="homepage-button-link">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/Brands.svg" alt="Бренды">
    </a>
    
    <!-- Ссылка-кнопка "Sale" -->
    <a href="/offers/discount/" class="homepage-button-link">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/Sale.svg" alt="Sale">
    </a>

</div>
<div class="maxwidth-theme">
	<div class="move-to-catalog visible-xs visible-sm">
		<a class="btn social-btn social-btn-vk" target="_blank" href="https://vk.com/clanbeauty">VK</a>
		<a class="btn social-btn social-btn-tg" target="_blank" href="https://t.me/clanbeauty">TG</a>
		<a class="btn social-btn social-btn-wa" target="_blank"
			href="https://api.whatsapp.com/send?phone=79202488898">WA</a>
	</div>
</div>


<?php
$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"skin-types",
	array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "aspro_next_content",
		"IBLOCK_ID" => "33",
		"NEWS_COUNT" => "20",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_BY2" => "ID",
		"SORT_ORDER2" => "DESC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array("ID"),
		"PROPERTY_CODE" => array("DESCRIPTION"),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_LAST_MODIFIED" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_TOP_PAGER" => "Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "Y",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"PAGER_BASE_LINK_ENABLE" => "Y",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"PAGER_BASE_LINK" => "",
		"PAGER_PARAMS_NAME" => "arrPager",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);

if ($isShowCatalogSections || $isShowCatalogElements || $isShowMiddleAdvBottomBanner) {
	?>
	<div class="maxwidth-theme">
		<?php
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_catalog_hit.php");
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_adv_middle.php");
		?>
	</div>
	<?php
}


$APPLICATION->IncludeFile(
    $APPLICATION->GetTemplatePath("/include/mainpage/comp_catalog_top_brand.php"),
    array(
        // Здесь мы передаем в наш файл ID бренда по умолчанию.
        // Это значение будет использоваться, если ничего не задано в админке.
        "TOP_BRAND_ID" => "18192" 
    ),
    array(
        "MODE" => "php", // Указываем, что это PHP-файл
        "NAME" => "Редактировать Топ-бренд сезона", // Название для всплывающего окна
    )
);

include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_brands.php");


if ($isShowCatalogSections || $isShowCatalogElements || $isShowMiddleAdvBottomBanner) {
	?>
	<div class="maxwidth-theme">
		<?php
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_catalog_novelty.php");
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_adv_middle.php");
		?>
	</div>
	<?php
}


if ($isShowCatalogSections || $isShowCatalogElements || $isShowMiddleAdvBottomBanner) {
    // Просто подключаем наш готовый файл. Всю HTML-обертку он создаст сам.
    include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_catalog_discount.php");

    // Рекламный баннер, скорее всего, тоже нужно обернуть, чтобы он не сломал верстку.
    ?>
    <div class="maxwidth-theme">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_adv_middle.php"); ?>
    </div>
    <?php
}



include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_catalog_sections.php");


if ($isShowCatalogSections || $isShowCatalogElements || $isShowMiddleAdvBottomBanner) {
	?>
	<div class="maxwidth-theme">
		<?php
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_catalog_blogger.php");
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_adv_middle.php");
		?>
	</div>
	<?php
}

if ($isShowSale) { ?>
	<div class="maxwidth-theme">
		<?php
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_articles.php");
		?>
	</div>
	<?php
}
//TODO включить, если потребуется фильтрация по свойству SHOW_ON_INDEX_PAGE
//$arShowPromoOnMainFilter = ['PROPERTY_SHOW_ON_INDEX_PAGE_VALUE' => 'Y'];
$APPLICATION->IncludeComponent(
	'bitrix:news.list',
	'promo_swiper',
	array(
		'IBLOCK_TYPE' => "aspro_next_content",
		'IBLOCK_ID' => 22,
		'NEWS_COUNT' => '10',
		'SORT_BY1' => "SORT",
		'SORT_ORDER1' => 'ASC',
		'SORT_BY2' => 'ID',
		'SORT_ORDER2' => 'DESC',
		"PROPERTY_CODE" => array(
			0 => 'REDIRECT',
			1 => 'SHOW_ON_INDEX_PAGE',
		),
		'CHECK_DATES' => 'Y',
		'ACTIVE' => 'Y',
		'CACHE_GROUPS' => 'N',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => '3600',
		'SET_TITLE' => 'N',
		'SET_BROWSER_TITLE' => 'N',
		'SET_META_KEYWORDS' => 'N',
		'SET_META_DESCRIPTION' => 'N',
		'SET_LAST_MODIFIED' => 'N',
		'COMPONENT_TEMPLATE' => 'promo_swiper',
		'FILTER_NAME' => 'arShowPromoOnMainFilter',
		'CATALOG' => '/promo/',
		'SET_TITLE' => 'N'
	),
	false
);

if ($isShowSale) { ?>
	<div class="maxwidth-theme">
		<?php
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_journal.php");
		?>
	</div>
	<?php
}


if ($isShowBlog) {
	?>
	<div class="maxwidth-theme">
		<?php
		include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_blog.php");
		?>
	</div>
	<?php
}

$APPLICATION->IncludeFile(
    SITE_DIR . "include/mainpage/giftcard_banner.php",
    array(),
    array(
        "MODE" => "html",
        "NAME" => "Редактировать баннер сертификатов",
    )
);



include($_SERVER['DOCUMENT_ROOT'] . "/include/footer/comp_viewed.php");

include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/comp_bottom_banners.php");

?>

<div class="maxwidth-theme">
	<?php
	if ($isShowCompany) {

		?>
		<div class="company_bottom_block">
			<div class="row wrap_md">
				<div class="col-md-2 col-sm-2 hidden-xs img">
					<?php
					include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/company/front_img.php");
					?>
				</div>
				<div class="col-md-10 col-sm-10 big">
					<?php
					if ($arRegion) {
						echo $arRegion['DETAIL_TEXT'];
					} else {
						include($_SERVER['DOCUMENT_ROOT'] . "/include/mainpage/company/front_info.php");
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}
	
	?>
</div>