<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? $this->setFrameMode(true); ?>
<? if (count($arResult["ITEMS"]) >= 1) { ?>
    <? if (($arParams["AJAX_REQUEST"] == "N") || !isset($arParams["AJAX_REQUEST"])) { ?>
        <div class="top_wrapper row margin0 <?= ($arParams["SHOW_UNABLE_SKU_PROPS"] != "N" ? "show_un_props" : "unshow_un_props"); ?>">
            <div class="catalog_block items block_list">
            <? } ?>
            <?
            $currencyList = '';
            if (!empty($arResult['CURRENCIES'])) {
                $templateLibrary[] = 'currency';
                $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
            }
            $templateData = [
                'TEMPLATE_LIBRARY' => $templateLibrary,
                'CURRENCIES' => $currencyList
            ];
            unset($currencyList, $templateLibrary);

            $arParams["BASKET_ITEMS"] = ($arParams["BASKET_ITEMS"] ? $arParams["BASKET_ITEMS"] : []);

            if (is_array($arParams['OFFERS_CART_PROPERTIES'])) {
                $arOfferProps = implode(';', $arParams['OFFERS_CART_PROPERTIES']);
            }


            switch ($arParams["LINE_ELEMENT_COUNT"]) {
                case '1':
                case '2':
                    $col = 2;
                    break;
                case '3':
                    $col = 3;
                    break;
                case '5':
                    $col = 5;
                    break;
                default:
                    $col = 4;
                    break;
            }
            if ($arParams["LINE_ELEMENT_COUNT"] > 5)
                $col = 5; ?>
            <? foreach ($arResult["ITEMS"] as $arItem) { ?>
                <?
                $mobileWidthClass = 'col-xs-6';
                if($arItem['PROPERTIES']['DOUBLE_WIDTH']['VALUE'] == "Y") {
                    $mobileWidthClass = 'col-xs-12 double-width-item';
                }
                ?>
                <div class="item_block col-<?= $col; ?> col-md-<?= ceil(12 / $col); ?> col-sm-<?= ceil(12 / round($col / 2)) ?> <?= $mobileWidthClass?>">
                    <div class="catalog_item_wrapp item">
                        <div class="basket_props_block" id="bx_basket_div_<?= $arItem["ID"]; ?>" style="display: none;">
                            <? if (!empty($arItem['PRODUCT_PROPERTIES_FILL'])) {
                                foreach ($arItem['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo) {
                            ?>
                                    <input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
                                <? if (isset($arItem['PRODUCT_PROPERTIES'][$propID]))
                                        unset($arItem['PRODUCT_PROPERTIES'][$propID]);
                                }
                            }
                            $arItem["EMPTY_PROPS_JS"] = "Y";
                            $emptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
                            if (!$emptyProductProperties) {
                                $arItem["EMPTY_PROPS_JS"] = "N"; ?>
                                <div class="wrapper">
                                    <table>
                                        <? foreach ($arItem['PRODUCT_PROPERTIES'] as $propID => $propInfo) {
                                        ?>
                                            <tr>
                                                <td><? echo $arItem['PROPERTIES'][$propID]['NAME']; ?></td>
                                                <td>
                                                    <? if ('L' == $arItem['PROPERTIES'][$propID]['PROPERTY_TYPE'] && 'C' == $arItem['PROPERTIES'][$propID]['LIST_TYPE']) {
                                                        foreach ($propInfo['VALUES'] as $valueID => $value) {
                                                    ?>
                                                            <label>
                                                                <input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
                                                            </label>
                                                        <?
                                                        }
                                                    } else {
                                                        ?>
                                                        <select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]">
                                                            <?
                                                            foreach ($propInfo['VALUES'] as $valueID => $value) {
                                                            ?>
                                                                <option value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option>
                                                            <? } ?>
                                                        </select>
                                                    <? } ?>
                                                </td>
                                            </tr>
                                        <? } ?>
                                    </table>
                                </div>
                            <?
                            } ?>
                        </div>
                        <? $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), ["CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')]);

                        $arItem["strMainID"] = $this->GetEditAreaId($arItem['ID']);
                        $arItemIDs = CNext::GetItemsIDs($arItem);

                        $totalCount = CNext::GetTotalCount($arItem, $arParams);
                        $arQuantityData = CNext::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"]);

                        $item_id = $arItem["ID"];
                        $strMeasure = '';
                        $arAddToBasketData;
                        if (!$arItem["OFFERS"] || $arParams['TYPE_SKU'] !== 'TYPE_1') {
                            if ($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]) {
                                $arMeasure = CCatalogMeasure::getList([], ["ID" => $arItem["CATALOG_MEASURE"]], false, false, [])->GetNext();
                                $strMeasure = $arMeasure["SYMBOL_RUS"];
                            }
                            $arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
                        } elseif ($arItem["OFFERS"]) {
                            $strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
                        }
                        $elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);
                        ?>
                        <div class="catalog_item main_item_wrapper item_wrap <?= (($_GET['q'])) ? 's' : '' ?>" id="<?= $arItemIDs["strMainID"]; ?>">
                            <div>
                                <div class="image_wrapper_block">
                                    <div class="stickers">
                                        <? $prop = ($arParams["STIKERS_PROP"] ? $arParams["STIKERS_PROP"] : "HIT"); ?>
                                        <? if (is_array($arItem["PROPERTIES"][$prop]["VALUE_XML_ID"])) { ?>
                                            <? foreach ($arItem["PROPERTIES"][$prop]["VALUE_XML_ID"] as $key => $class) { ?>
                                                <div>
                                                    <div class="sticker_<?= strtolower($class); ?>"><?= $arItem["PROPERTIES"][$prop]["VALUE"][$key] ?></div>
                                                </div>
                                            <? } ?>
                                        <? } ?>
                                        <? if ($arParams["SALE_STIKER"] && $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]) { ?>
                                            <div>
                                                <div class="sticker_sale_text"><?= $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]; ?></div>
                                            </div>
                                        <? } ?>
                                    </div>
                                    <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y") { ?>
                                        <div class="like_icons">
                                            <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N") { ?>
                                                <? if (!$arItem["OFFERS"]) { ?>
                                                    <? if ($USER->IsAuthorized()) { ?>
                                                        <div class="wish_item_button">
                                                            <span title="<?= GetMessage('CATALOG_WISH') ?>" class="wish_item to" data-item="<?= $arItem["ID"] ?>" data-iblock="<?= $arItem["IBLOCK_ID"] ?>"><i></i></span>
                                                            <span title="<?= GetMessage('CATALOG_WISH_OUT') ?>" class="wish_item in added" style="display: none;" data-item="<?= $arItem["ID"] ?>" data-iblock="<?= $arItem["IBLOCK_ID"] ?>"><i></i></span>
                                                        </div>
                                                    <? } else { ?>
                                                        <div class="wish_item_button">
                                                            <span class="wish_item to" data-toggle="tooltip" title="<?= GetMessage('TOOLTIP_WISHIST') ?>"><i></i></span>

                                                        </div>
                                                    <? } ?>
                                                <? } elseif ($arItem["OFFERS"] && !empty($arItem['OFFERS_PROP'])) { ?>
                                                    <div class="wish_item_button" style="display: none;">
                                                        <span title="<?= GetMessage('CATALOG_WISH') ?>" class="wish_item to <?= $arParams["TYPE_SKU"]; ?>" data-item="" data-iblock="<?= $arItem["IBLOCK_ID"] ?>" data-offers="Y" data-props="<?= $arOfferProps ?>"><i></i></span>
                                                        <span title="<?= GetMessage('CATALOG_WISH_OUT') ?>" class="wish_item in added <?= $arParams["TYPE_SKU"]; ?>" style="display: none;" data-item="" data-iblock="<?= $arOffer["IBLOCK_ID"] ?>"><i></i></span>
                                                    </div>
                                                <? } ?>
                                            <? } ?>
                                            <? if ($arParams["DISPLAY_COMPARE"] == "Y") { ?>
                                                <? if (!$arItem["OFFERS"] || $arParams["TYPE_SKU"] !== 'TYPE_1') { ?>
                                                    <div class="compare_item_button">
                                                        <span title="<?= GetMessage('CATALOG_COMPARE') ?>" class="compare_item to" data-iblock="<?= $arParams["IBLOCK_ID"] ?>" data-item="<?= $arItem["ID"] ?>"><i></i></span>
                                                        <span title="<?= GetMessage('CATALOG_COMPARE_OUT') ?>" class="compare_item in added" style="display: none;" data-iblock="<?= $arParams["IBLOCK_ID"] ?>" data-item="<?= $arItem["ID"] ?>"><i></i></span>
                                                    </div>
                                                <? } elseif ($arItem["OFFERS"]) { ?>
                                                    <div class="compare_item_button">
                                                        <span title="<?= GetMessage('CATALOG_COMPARE') ?>" class="compare_item to <?= $arParams["TYPE_SKU"]; ?>" data-iblock="<?= $arParams["IBLOCK_ID"] ?>" data-item=""><i></i></span>
                                                        <span title="<?= GetMessage('CATALOG_COMPARE_OUT') ?>" class="compare_item in added <?= $arParams["TYPE_SKU"]; ?>" style="display: none;" data-iblock="<?= $arParams["IBLOCK_ID"] ?>" data-item=""><i></i></span>
                                                    </div>
                                                <? } ?>
                                            <? } ?>
                                        </div>
                                    <? } ?>
                                    <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="thumb shine" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>">
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
                                            <? $img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"]['ID'], ["width" => 500, "height" => 500], BX_RESIZE_IMAGE_PROPORTIONAL, true); ?>
                                            <img loading="lazy" src="<?= $img["src"] ?>" alt="<?= $a_alt; ?>" title="<?= $a_title; ?>" />
                                        <? } else { ?>
                                            <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo_medium.png" alt="<?= $a_alt; ?>" title="<?= $a_title; ?>" />
                                        <? } ?>
                                    </a>
                                </div>
                                <div class="item_info <?= $arParams["TYPE_SKU"] ?>">
                                    <? if (is_array($arItem['DISPLAY_PROPERTIES']['BRAND']['LINK_ELEMENT_VALUE'])) { ?>
                                        <div class="brand-title hidden"><?= current($arItem['DISPLAY_PROPERTIES']['BRAND']['LINK_ELEMENT_VALUE'])['NAME'] ?></div>
                                    <? } ?>
                                    <div class="item-title">
                                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="dark_link">
                                            <p><?= $arItem['PROPERTIES']['ALT_NAME']['VALUE'] ?></p>
                                            <span><?= $elementName; ?></span>
                                        </a>
                                    </div>

                                    <? if ($arParams["SHOW_RATING"] == "Y") { ?>
                                        <div class="rating">
                                            <? $APPLICATION->IncludeComponent(
                                                "bitrix:iblock.vote",
                                                "element_rating_front",
                                                [
                                                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                                    "IBLOCK_ID" => $arItem["IBLOCK_ID"],
                                                    "ELEMENT_ID" => $arItem["ID"],
                                                    "MAX_VOTE" => 5,
                                                    "VOTE_NAMES" => [],
                                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                                    "DISPLAY_AS_RATING" => 'vote_avg'
                                                ],
                                                $component,
                                                ["HIDE_ICONS" => "Y"]
                                            ); ?>
                                        </div>
                                    <? } ?>

                                    <? if (!empty($arItem['DISPLAY_PROPERTIES']['VOLUME']['DISPLAY_VALUE'])) { ?>
                                        <div class="list-props-wrapper">
                                            <span class="prop"><?= $arItem['DISPLAY_PROPERTIES']['VOLUME']['NAME'] ?>: </span>
                                            <span class="value"><?= $arItem['DISPLAY_PROPERTIES']['VOLUME']['DISPLAY_VALUE'] ?></span>
                                        </div>
                                    <? } ?>

                                     <? if ($arItem['REVIEWS_COUNT'] > 0) { ?>
                                        <div class="reviews-section">
                                            <span><?= GetMessage("CATALOG_REVIEWS_COUNT"); ?>:</span> <span class="reviews-count"><?= $arItem['REVIEWS_COUNT'] ?></span>
                                        </div>
                                    <? } ?>

                                    <div class="sa_block">
                                        <?= $arQuantityData["HTML"]; ?>
                                        <div class="article_block">
                                            <? if (isset($arItem['ARTICLE']) && $arItem['ARTICLE']['VALUE']) { ?>
                                                <?= $arItem['ARTICLE']['NAME']; ?>: <?= $arItem['ARTICLE']['VALUE']; ?>
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="cost prices clearfix">
                                        <? if ($arItem["OFFERS"]) { ?>
                                            <div class="with_matrix <?= ($arParams["SHOW_OLD_PRICE"] == "Y" ? 'with_old' : ''); ?>" style="display:none;">
                                                <div class="price price_value_block"><span class="values_wrapper"></span></div>
                                                <? if ($arParams["SHOW_OLD_PRICE"] == "Y") : ?>
                                                    <div class="price discount"></div>
                                                <? endif; ?>
                                                <? if ($arParams["SHOW_DISCOUNT_PERCENT"] == "Y") { ?>
                                                    <div class="sale_block matrix" style="display:none;">
                                                        <div class="sale_wrapper">
                                                            <div class="text"><span class="title"><?= GetMessage("CATALOG_ECONOMY"); ?></span>
                                                                <span class="values_wrapper"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <? } ?>
                                            </div>
                                            <? \Aspro\Functions\CAsproSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id, $arItemIDs, 'N'); ?>
                                        <? } else { ?>
                                            <?
                                            $item_id = $arItem["ID"];
                                            if (isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) { ?>
                                                <? if ($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1) { ?>
                                                    <?= CNext::showPriceRangeTop($arItem, $arParams, GetMessage("CATALOG_ECONOMY")); ?>
                                                <? } ?>
                                                <?= CNext::showPriceMatrix($arItem, $arParams, $strMeasure, $arAddToBasketData); ?>
                                                <? $arMatrixKey = array_keys($arItem['PRICE_MATRIX']['MATRIX']);
                                                $min_price_id = current($arMatrixKey); ?>
                                            <? } else {
                                                $arCountPricesCanAccess = 0;
                                                $min_price_id = 0; ?>
                                                <? \Aspro\Functions\CAsproItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id, 'N'); ?>
                                            <? } ?>
                                        <? } ?>
                                    </div>
                                    <? if ($arParams["SHOW_DISCOUNT_TIME"] == "Y" && $arParams['SHOW_COUNTER_LIST'] != 'N') { ?>
                                        <? $arUserGroups = $USER->GetUserGroupArray(); ?>
                                        <? if ($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] != 'Y' || ($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] == 'Y' && !$arItem['OFFERS'])) { ?>
                                            <? $arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $arUserGroups, "N", $min_price_id, SITE_ID);
                                            $arDiscount = [];

                                            if ($arDiscounts) {
                                                $arDiscount = current($arDiscounts);
                                            }

                                            if ($arDiscount["ACTIVE_TO"] && $arDiscount['USE_COUPONS'] != "Y") {
                                            ?>
                                                <div class="view_sale_block <?= ($arQuantityData["HTML"] ? '' : 'wq'); ?>">
                                                    <div class="count_d_block">
                                                        <span class="active_to hidden"><?= $arDiscount["ACTIVE_TO"]; ?></span>
                                                        <div class="title"><?= GetMessage("UNTIL_AKC"); ?></div>
                                                        <span class="countdown values"><span class="item"></span><span class="item"></span><span class="item"></span><span class="item"></span></span>
                                                    </div>
                                                </div>
                                            <? } ?>
                                        <? } else { ?>
                                            <? if ($arItem['JS_OFFERS']) {
                                                foreach ($arItem['JS_OFFERS'] as $keyOffer => $arTmpOffer2) {
                                                    $active_to = '';
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByProduct($arTmpOffer2['ID'], $arUserGroups, "N", $min_price_id, SITE_ID);
                                                    if ($arDiscounts) {
                                                        foreach ($arDiscounts as $arDiscountOffer) {
                                                            if ($arDiscountOffer['ACTIVE_TO']) {
                                                                $active_to = $arDiscountOffer['ACTIVE_TO'];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    $arItem['JS_OFFERS'][$keyOffer]['DISCOUNT_ACTIVE'] = $active_to;
                                                }
                                            } ?>
                                            <div class="view_sale_block" style="display:none;">
                                                <div class="count_d_block">
                                                    <span class="active_to_<?= $arItem["ID"] ?> hidden"><?= $arDiscount["ACTIVE_TO"]; ?></span>
                                                    <div class="title"><?= GetMessage("UNTIL_AKC"); ?></div>
                                                    <span class="countdown countdown_<?= $arItem["ID"] ?> values"></span>
                                                </div>
                                                <? if ($arQuantityData["HTML"]) { ?>
                                                    <div class="quantity_block">
                                                        <div class="title"><?= GetMessage("TITLE_QUANTITY_BLOCK"); ?></div>
                                                        <div class="values">
                                                            <span class="item">
                                                                <span class="value"><?= $totalCount; ?></span>
                                                                <span class="text"><?= GetMessage("TITLE_QUANTITY"); ?></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <? } ?>
                                            </div>
                                        <? } ?>
                                    <? } ?>
                                </div>
                                <div class="footer_button">
                                    <? if (!$arItem["OFFERS"] || $arParams['TYPE_SKU'] !== 'TYPE_1') { ?>
                                        <div class="counter_wrapp <?= ($arItem["OFFERS"] && $arParams["TYPE_SKU"] == "TYPE_1" ? 'woffers' : '') ?>">
                                            <? if (($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arAddToBasketData["CAN_BUY"]) { ?>
                                                <div class="counter_block hidden" data-offers="<?= ($arItem["OFFERS"] ? "Y" : "N"); ?>" data-item="<?= $arItem["ID"]; ?>">
                                                    <span class="minus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN']; ?>">-</span>
                                                    <input type="text" class="text" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<?= $arAddToBasketData["MIN_QUANTITY_BUY"] ?>" />
                                                    <span class="plus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP']; ?>" <?= ($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='" . $arAddToBasketData["MAX_QUANTITY_BUY"] . "'" : "") ?>>+</span>
                                                </div>
                                            <? } ?>

                                            <div id="<?= $arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>" class="button_block <?= (($arAddToBasketData["ACTION"] == "ORDER") || !$arAddToBasketData["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] || $arAddToBasketData["ACTION"] == "SUBSCRIBE" ? "wide" : ""); ?>">
                                                <!--noindex-->
                                                <?= $arAddToBasketData["HTML"] ?>
                                                <!--/noindex-->
                                                <? if (!$arAddToBasketData["CAN_BUY"]) { ?>
                                                    <div class="email-subscribe-description"><?= GetMessage('NOT_AVAILABLE_SEND_EMAIL') ?></div>
                                                <? } ?>
                                            </div>
                                        </div>
                                        <? if (isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) { ?>
                                            <? if ($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1) { ?>
                                                <? $arOnlyItemJSParams = [
                                                    "ITEM_PRICES" => $arItem["ITEM_PRICES"],
                                                    "ITEM_PRICE_MODE" => $arItem["ITEM_PRICE_MODE"],
                                                    "ITEM_QUANTITY_RANGES" => $arItem["ITEM_QUANTITY_RANGES"],
                                                    "MIN_QUANTITY_BUY" => $arAddToBasketData["MIN_QUANTITY_BUY"],
                                                    "ID" => $arItemIDs["strMainID"],
                                                ]; ?>
                                                <script type="text/javascript">
                                                    var <? echo $arItemIDs["strObName"]; ?>el = new JCCatalogSectionOnlyElement(<? echo CUtil::PhpToJSObject($arOnlyItemJSParams, false, true); ?>);
                                                </script>
                                            <? } ?>
                                        <? } ?>
                                    <? } elseif ($arItem["OFFERS"]) { ?>
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
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>
            <? if (($arParams["AJAX_REQUEST"] == "N") || !isset($arParams["AJAX_REQUEST"])) { ?>
            </div>
        </div>
    <? } ?>
    <? if ($arParams["AJAX_REQUEST"] == "Y") { ?>
        <div class="wrap_nav">
        <? } ?>
        <? if ($arParams['CUSTOM_HIDE_BOTTOM_NAV'] != "Y") { ?>
            <div class="bottom_nav <?= $arParams["DISPLAY_TYPE"]; ?>" <?= ($arParams["AJAX_REQUEST"] == "Y" ? "style='display: none; '" : ""); ?>>
                <? if ($arParams["DISPLAY_BOTTOM_PAGER"] == "Y") { ?><?= $arResult["NAV_STRING"] ?><? } ?>
            </div>
        <? } ?>
        <? if ($arParams["AJAX_REQUEST"] == "Y") { ?>
        </div>
    <? } ?>
<? } else { ?>
    <script>
        $('.sort_header').animate({
            'opacity': '1'
        }, 500);
    </script>
    <? if ($APPLICATION->GetPageProperty('WISHLIST') == 'Y') { ?>
        <? $APPLICATION->IncludeFile(SITE_DIR . "include/wishlist_no_products.php", [], ["MODE" => "html", "NAME" => GetMessage('EMPTY_CATALOG_DESCR')]); ?>
    <? } else { ?>
        <div class="no_goods catalog_block_view">
            <div class="no_products">
                <div class="wrap_text_empty">
                    <? if ($_REQUEST["set_filter"]) { ?>
                        <? $APPLICATION->IncludeFile(SITE_DIR . "include/section_no_products_filter.php", [], ["MODE" => "html", "NAME" => GetMessage('EMPTY_CATALOG_DESCR')]); ?>
                    <? } else { ?>
                        <? $APPLICATION->IncludeFile(SITE_DIR . "include/section_no_products.php", [], ["MODE" => "html", "NAME" => GetMessage('EMPTY_CATALOG_DESCR')]); ?>
                    <? } ?>
                </div>
            </div>
            <? if ($_REQUEST["set_filter"]) { ?>
                <span class="button wide"><?= GetMessage('RESET_FILTERS'); ?></span>
            <? } ?>
        </div>
    <? } ?>
<? } ?>

<div itemscope itemtype="https://schema.org/Product" style="display:none;">
    <span itemprop="brand">ClanBeauty</span>
    <span itemprop="name"><?= $arResult['NAME'] ? htmlentities(strip_tags($arResult["NAME"])) : $APPLICATION->GetTitle() ?></span>
    <span itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
        Rating: <span itemprop="ratingValue">5</span>, stars by
        <span itemprop="ratingCount">120</span> reviews
    </span>
    <span itemprop="offers" itemscope itemtype="https://schema.org/AggregateOffer">
        From <span itemprop="lowPrice"><?= $arResult["MIN_PRICE"] ? $arResult["MIN_PRICE"] : 0 ?></span> to
        <span itemprop="highPrice"><?= $arResult["MAX_PRICE"] ? $arResult["MAX_PRICE"] : 0 ?></span>
        <meta itemprop="priceCurrency" content="RUB" />
    </span>
</div>

<script>
    BX.message({
        QUANTITY_AVAILABLE: '<? echo COption::GetOptionString("aspro.next", "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID); ?>',
        QUANTITY_NOT_AVAILABLE: '<? echo COption::GetOptionString("aspro.next", "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS"), SITE_ID); ?>',
        ADD_ERROR_BASKET: '<? echo GetMessage("ADD_ERROR_BASKET"); ?>',
        ADD_ERROR_COMPARE: '<? echo GetMessage("ADD_ERROR_COMPARE"); ?>',
    })
</script>