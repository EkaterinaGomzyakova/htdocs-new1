<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?php
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/owl.carousel.js');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/owl.carousel.css');

    $GLOBALS['arFilter'] = ['ID' => $arResult['PRODUCTS_WITHOUT_COMMENT_ID']];
    $APPLICATION->IncludeComponent("bitrix:news.list", "reviews-owl",
        Array(
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "AJAX_MODE" => "Y",
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => "2",
        "NEWS_COUNT" => "20",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        "SORT_BY2" => "SORT",
        "SORT_ORDER2" => "ASC",
        "FILTER_NAME" => "arFilter",
        "FIELD_CODE" => Array("ID"),
        "PROPERTY_CODE" => Array("DESCRIPTION"),
        "CHECK_DATES" => "Y",
        "DETAIL_URL" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "SET_TITLE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "SET_META_KEYWORDS" => "Y",
        "SET_META_DESCRIPTION" => "Y",
        "SET_LAST_MODIFIED" => "Y",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
        "ADD_SECTIONS_CHAIN" => "Y",
        "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "INCLUDE_SUBSECTIONS" => "Y",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "DISPLAY_TOP_PAGER" => "Y",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Новости",
        "PAGER_SHOW_ALWAYS" => "Y",
        "PAGER_TEMPLATE" => "",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "Y",
        "PAGER_BASE_LINK_ENABLE" => "Y",
        "SET_STATUS_404" => "Y",
        "SHOW_404" => "Y",
        "MESSAGE_404" => "",
        "PAGER_BASE_LINK" => "",
        "PAGER_PARAMS_NAME" => "arrPager",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => ""
    )
);?>

<?if(!empty($arResult['UNPUBLISHED_COMMENTS'])) { ?>
    <div class="unpublished-comments-main-title">На проверке</div>
    <div id="js-unpublished-comments" class="unpublished-comments">
        <? foreach ($arResult['UNPUBLISHED_COMMENTS'] as $arItem) { ?>
            <div class="unpublished-comments-item">
                <div class="unpublished-comments-name"><?=$arItem["NAME"]?></div>
                <div class="unpublished-comments-text"><?=$arItem["PREVIEW_TEXT"]?></div>
                <? if(!empty($arItem["PROPERTY_DIGNITY_VALUE"])) { ?>
                    <div class="unpublished-comments-prop">
                        <div class="unpublished-comments-title">Достоинства</div>
                        <div class="unpublished-comments-text"><?=$arItem["PROPERTY_DIGNITY_VALUE"]?></div>
                    </div>
                <? } ?>
                <? if(!empty($arItem["PROPERTY_FAULT_VALUE"])) { ?>
                    <div class="unpublished-comments-prop">
                        <div class="unpublished-comments-title">Недостатки</div>
                        <div class="unpublished-comments-text"><?=$arItem["PROPERTY_FAULT_VALUE"]?></div>
                    </div>
                <? } ?>
            </div>
        <? } ?>
    </div>
<? } ?>

<?if(!empty($arResult['PUBLISHED_COMMENTS'])) { ?>
    <div class="unpublished-comments-main-title">Опубликованы</div>
    <div id="js-published-comments" class="unpublished-comments published-comments owl-carousel">
        <? foreach ($arResult['PUBLISHED_COMMENTS'] as $arItem) { ?>
            <div class="unpublished-comments-item">
                <div class="unpublished-comments-name"><?=$arItem["NAME"]?></div>
                <div class="unpublished-comments-text"><?=$arItem["PREVIEW_TEXT"]?></div>
                <? if(!empty($arItem["PROPERTY_DIGNITY_VALUE"])) { ?>
                    <div class="unpublished-comments-prop">
                        <div class="unpublished-comments-title">Достоинства</div>
                        <div class="unpublished-comments-text"><?= $arItem["PROPERTY_DIGNITY_VALUE"]?></div>
                    </div>
                <? } ?>
                <? if(!empty($arItem["PROPERTY_FAULT_VALUE"])) { ?>
                    <div class="unpublished-comments-prop">
                        <div class="unpublished-comments-title">Недостатки</div>
                        <div class="unpublished-comments-text"><?= $arItem["PROPERTY_FAULT_VALUE"]?></div>
                    </div>
                <? } ?>
                <a class="btn btn-default btn-lg white" href="<?= $arItem["DETAIL_PAGE_URL"]?>?open_review=Y">Посмотреть</a>
            </div>
        <? } ?>
    </div>
<? } ?>
<script>
    $('#js-published-comments').owlCarousel({
        margin:10,
        nav:true,
        autoHeight: true,
        dots: false,
        responsive:{
            0:{
                items: 1
            },
            600:{
                items: 1
            },
            1000:{
                items: 3
            },
            1400:{
                items: 4
            }
        }
    })
</script>
