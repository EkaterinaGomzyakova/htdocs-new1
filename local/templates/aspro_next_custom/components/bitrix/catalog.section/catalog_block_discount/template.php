<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? $this->setFrameMode(true); ?>
<? if (count($arResult["ITEMS"]) >= 1) { ?>
    <!-- ИЗМЕНЕНИЕ: Добавили position-relative для позиционирования стрелок -->
    <div class="top_wrapper items_wrapper a-products-slider-wrapper" style="position: relative;">
        <div class="fast_view_params" data-params="<?= urlencode(serialize($arTransferParams)); ?>"></div>
        
        <!-- === НАЧАЛО БЛОКА SLIDER === -->
        <!-- ИЗМЕНЕНИЕ: Стандартная структура Swiper -->
        <div class="swiper a-products-slider">
            <div class="swiper-wrapper">
                <? foreach ($arResult["ITEMS"] as $arItem) { ?>
                    <? $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

                    $totalCount = CNext::GetTotalCount($arItem, $arParams);
                    $arItemIDs = CNext::GetItemsIDs($arItem, "N");
                    $arQuantityData = CNext::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"]);

                    $item_id = $arItem["ID"];
                    $strMeasure = '';
                    if ($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]) {
                        if (isset($arItem["ITEM_MEASURE"]) && (is_array($arItem["ITEM_MEASURE"]) && $arItem["ITEM_MEASURE"]["TITLE"])) {
                            $strMeasure = $arItem["ITEM_MEASURE"]["TITLE"];
                        } else {
                            $arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
                            $strMeasure = $arMeasure["SYMBOL_RUS"];
                        }
                    }
                    $arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, array(), 'small', $arParams);
                    
                    $elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);
                    ?>

                    <!-- ИЗМЕНЕНИЕ: Каждый товар теперь .swiper-slide. Классы с колонками (col-*) убраны -->
                    <div class="swiper-slide"> 
                        <div class="catalog_item_wrapp item"> <!-- Убран класс с колонкой -->
                            <div class="catalog_item item_wrap " id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                                <div class="inner_wrap">
                                    <div class="image_wrapper_block shine">
                                        <div class="stickers">
                                            <? $prop = ($arParams["STIKERS_PROP"] ? $arParams["STIKERS_PROP"] : "HIT"); ?>
                                            <? if (is_array($arItem["PROPERTIES"][$prop]["VALUE_XML_ID"])) : ?>
                                                <? foreach ($arItem["PROPERTIES"][$prop]["VALUE_XML_ID"] as $key => $class) { ?>
                                                    <div>
                                                        <div class="sticker_<?= strtolower($class); ?>"><?= $arItem["PROPERTIES"][$prop]["VALUE"][$key] ?></div>
                                                    </div>
                                                <? } ?>
                                            <? endif; ?>
                                            <? if ($arParams["SALE_STIKER"] && $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]) { ?>
                                                <div>
                                                    <div class="sticker_sale_text"><?= $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]; ?></div>
                                                </div>
                                            <? } ?>
                                        </div>
                                        <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y") : ?>
                                            <div class="like_icons">
                                                <? if ($arParams["DISPLAY_WISH_BUTTONS"] == "Y" && !$arItem["OFFERS"]) : ?>
                                                    <? if ($USER->IsAuthorized()) : ?>
                                                        <div class="wish_item_button">
                                                            <span title="<?= GetMessage('CATALOG_WISH') ?>" class="wish_item to" data-item="<?= $arItem["ID"] ?>" data-iblock="<?= $arItem["IBLOCK_ID"] ?>"><i></i></span>
                                                            <span title="<?= GetMessage('CATALOG_WISH_OUT') ?>" class="wish_item in added" style="display: none;" data-item="<?= $arItem["ID"] ?>" data-iblock="<?= $arItem["IBLOCK_ID"] ?>"><i></i></span>
                                                        </div>
                                                    <? else : ?>
                                                        <div class="wish_item_button">
                                                            <span class="wish_item to" data-toggle="tooltip" title="<?= GetMessage('TOOLTIP_WISHIST') ?>"><i></i></span>
                                                        </div>
                                                    <? endif; ?>
                                                <? endif; ?>
                                                <? if ($arParams["DISPLAY_COMPARE"] == "Y") : ?>
                                                    <div class="compare_item_button">
                                                        <span title="<?= GetMessage('CATALOG_COMPARE') ?>" class="compare_item to" data-iblock="<?= $arParams["IBLOCK_ID"] ?>" data-item="<?= $arItem["ID"] ?>"><i></i></span>
                                                        <span title="<?= GetMessage('CATALOG_COMPARE_OUT') ?>" class="compare_item in added" style="display: none;" data-iblock="<?= $arParams["IBLOCK_ID"] ?>" data-item="<?= $arItem["ID"] ?>"><i></i></span>
                                                    </div>
                                                <? endif; ?>
                                            </div>
                                        <? endif; ?>
                                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="thumb shine">
                                            <?
                                            $a_alt = ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"]);
                                            $a_title = ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"]);
                                            ?>
                                            <? if (!empty($arItem['WL_PREVIEW_SLIDER'])) { ?>
                                                <div class="catalog-section-carousel" id="catalog-section-carousel-<?= $arItem['ID'] ?>">
                                                    <div class="catalog-section-carousel-images">
                                                        <? foreach ($arItem['WL_PREVIEW_SLIDER'] as $key => $path) { ?>
                                                            <img loading="lazy" <?= ($key === 0) ? '' : 'style="display:none"'; ?> data-image="<?= $arItem['ID'] . "-" . $key ?>" src="<?= $path ?>" alt="<?= $a_alt; ?>" title="<?= $a_title; ?>" />
                                                        <? } ?>
                                                    </div>
                                                    <div class="catalog-section-carousel-dots">
                                                        <? foreach ($arItem['WL_PREVIEW_SLIDER'] as $key => $path) { ?>
                                                            <div class="catalog-section-carousel-dot" data-key="<?= $arItem['ID'] . "-" . $key ?>" style="width: <?= floor(100 / count($arItem['WL_PREVIEW_SLIDER'])) ?>%;"></div>
                                                        <? } ?>
                                                    </div>
                                                </div>
                                            <? } elseif (!empty($arItem["PREVIEW_PICTURE"]["ID"])) { ?>
                                                <img loading="lazy" src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $a_alt; ?>" title="<?= $a_title; ?>" />
                                            <? } elseif (!empty($arItem["DETAIL_PICTURE"]["ID"])) { ?>
                                                <? $img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"]['ID'], array("width" => 500, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, true); ?>
                                                <img loading="lazy" src="<?= $img["src"] ?>" alt="<?= $a_alt; ?>" title="<?= $a_title; ?>" />
                                            <? } else { ?>
                                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo_medium.png" alt="<?= $a_alt; ?>" title="<?= $a_title; ?>" />
                                            <? } ?>
                                        </a>
                                    </div>

                                    <div class="item_info">

                                        <? if (is_array($arItem['DISPLAY_PROPERTIES']['BRAND']['LINK_ELEMENT_VALUE'])) { ?>
                                            <div class="brand-title hidden"><?= current($arItem['DISPLAY_PROPERTIES']['BRAND']['LINK_ELEMENT_VALUE'])['NAME'] ?></div>
                                        <? } ?>

                                        <!-- Название товара (остается без изменений) -->
                                        <div class="item-title">
                                            <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="dark_link">
                                                <p><?= $arItem['DISPLAY_PROPERTIES']['ALT_NAME']['VALUE'] ?></p>
                                                <span><?= $elementName; ?></span>
                                            </a>
                                        </div>

                                        <!-- [НОВАЯ СТРУКТУРА] -->

                                        <!-- СТРОКА 1: Цена + Экономия + Отзывы -->
                                        <div class="item_info_main_row">
                                            
                                            <!-- Левая часть: Цена и стикеры экономии -->
                                            <div class="cost prices clearfix">
                                                <? if ($arItem["OFFERS"]) { ?>
                                                    <? \Aspro\Functions\CAsproSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id, array(), 'N'); ?>
                                                <? } else { ?>
                                                    <?
                                                    $item_id = $arItem["ID"];
                                                    if (isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX'])
                                                    {
                                                        if ($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1) {
                                                            echo CNext::showPriceRangeTop($arItem, $arParams, GetMessage("CATALOG_ECONOMY"));
                                                        }
                                                        echo CNext::showPriceMatrix($arItem, $arParams, $strMeasure, $arAddToBasketData);
                                                        $arMatrixKey = array_keys($arItem['PRICE_MATRIX']['MATRIX']);
                                                        $min_price_id = current($arMatrixKey);
                                                    } elseif ($arItem["PRICES"]) {
                                                        $arCountPricesCanAccess = 0;
                                                        $min_price_id = 0;
                                                        \Aspro\Functions\CAsproItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id, 'N');
                                                    } ?>
                                                <? } ?>
                                            </div>

                                            <!-- Правая часть: Отзывы -->
                                            <? if ($arItem['REVIEWS_COUNT'] > 0) { ?>
                                                <div class="reviews-section">
                                                   
                                                </div>
                                            <? } ?>
                                        </div>

                                        <!-- СТРОКА 2: Таймер акции -->
                                        <? if ($arParams["SHOW_DISCOUNT_TIME"] == "Y") {
                                            $arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $USER->GetUserGroupArray(), "N", $min_price_id, SITE_ID);
                                            $arDiscount = array();
                                            if ($arDiscounts)
                                                $arDiscount = current($arDiscounts);
                                            if ($arDiscount["ACTIVE_TO"] && $arDiscount['USE_COUPONS'] != "Y") {
                                            ?>
                                                <div class="view_sale_block <?= ($arQuantityData["HTML"] ? '' : 'wq'); ?>">
                                                    <div class="count_d_block">
                                                        <span class="active_to hidden"><?= $arDiscount["ACTIVE_TO"]; ?></span>
                                                        <div class="title"><?= GetMessage("UNTIL_AKC"); ?></div>
                                                        <span class="countdown values"><span class="item"></span><span class="item"></span><span class="item"></span><span class="item"></span></span>
                                                    </div>
                                                </div>
                                            <? }
                                        } ?>

                                        <!-- СТРОКА 3: Информация об остатках -->
                                        <?= $arQuantityData["HTML"]; ?>

                                        <!-- [КОНЕЦ НОВОЙ СТРУКТУРЫ] -->
                                        
                                    </div>
                                    <div class="footer_button">
                                        <? if (!$arItem["OFFERS"] || ($arItem["OFFERS"] && !$arItem['OFFERS_PROP'])) : ?>
                                            <div class="counter_wrapp <?= ($arItem["OFFERS"] && $arParams["TYPE_SKU"] == "TYPE_1" ? 'woffers' : '') ?>">
                                                <? if (($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arAddToBasketData["CAN_BUY"]) : ?>
                                                    <div class="counter_block hidden" data-offers="<?= ($arItem["OFFERS"] ? "Y" : "N"); ?>" data-item="<?= $arItem["ID"]; ?>">
                                                        <span class="minus">-</span>
                                                        <input type="text" class="text" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<?= $arAddToBasketData["MIN_QUANTITY_BUY"] ?>" />
                                                        <span class="plus" <?= ($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='" . $arAddToBasketData["MAX_QUANTITY_BUY"] . "'" : "") ?>>+</span>
                                                    </div>
                                                <? endif; ?>
                                                <div class="button_block <?= (($arAddToBasketData["ACTION"] == "ORDER"/*&& !$arItem["CAN_BUY"]*/)  || !$arAddToBasketData["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] || $arAddToBasketData["ACTION"] == "SUBSCRIBE" ? "wide" : ""); ?>">
                                                    <!--noindex-->
                                                    <?= $arAddToBasketData["HTML"] ?>
                                                    <!--/noindex-->
                                                </div>
                                            </div>
                                        <? elseif ($arItem["OFFERS"]) : ?>
                                            <? if (empty($arItem['OFFERS_PROP'])) { ?>
                                                <div class="offer_buy_block buys_wrapp woffers">
                                                    <?
                                                    $arItem["OFFERS_MORE"] = "Y";
                                                    $arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small read_more1', $arParams); ?>
                                                    <!--noindex-->
                                                    <?= $arAddToBasketData["HTML"] ?>
                                                    <!--/noindex-->
                                                </div>
                                            <? } else { ?>
                                                <div class="offer_buy_block buys_wrapp woffers" style="display:none;">
                                                    <div class="counter_wrapp"></div>
                                                </div>
                                            <? } ?>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end .swiper-slide -->
                <? } ?>
            </div> <!-- end .swiper-wrapper -->
        </div> <!-- end .swiper -->

        <!-- ИЗМЕНЕНИЕ: Добавляем стрелки навигации -->
        <div class="swiper-button-prev a-slider-btn-prev"></div>
        <div class="swiper-button-next a-slider-btn-next"></div>
        
        <!-- === КОНЕЦ БЛОКА SLIDER === -->
    </div>
<? } ?>

<?
// Подключаем файлы Swiper.js
$this->addExternalJS("https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js");
$this->addExternalCss("https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css");
?>