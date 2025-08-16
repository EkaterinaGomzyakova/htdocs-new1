<?php

    namespace Clanbeauty;

    use Bitrix\Main\Loader;
    use Bitrix\Sale\Basket;
    use Bitrix\Sale\Order;
    use CBitrixComponent;
    use CCatalogProduct;
    use CFile;
    use CIBlockElement;

    class ShoppingListComponent extends CBitrixComponent
    {
        public function executeComponent()
        {
            global $USER;
            Loader::includeModule('iblock');
            Loader::includeModule('sale');
            if (!$USER->IsAuthorized()) {
                ShowError('Для доступа к вашим отзывам требуется авторизация');
            } else {
                $orders = Order::getList([
                    'filter' => ['USER_ID' => $USER->GetID(), 'STATUS_ID' => 'F'],
                    'select' => ['ID', 'DATE_INSERT'],
                    'order' => ['ID' => 'DESC']
                ])->fetchAll();

                $ordersID = array_column($orders, 'ID');

                $basketItems = Basket::getList([
                    'select' => ['PRODUCT_ID', 'ORDER_ID'],
                    'filter' => ['ORDER_ID' => $ordersID],
                    'order' => ['ID' => 'DESC']
                ])->fetchAll();

                $uniqueUserProducts = array_unique(array_column($basketItems, 'PRODUCT_ID'));

                $arProductWithComment = [];
                $arUnpublishedComment = [];
                $dbComments = CIBlockElement::GetList([], ["IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"], "PROPERTY_USER" => $USER->GetID()], false, false, ["ID", "IBLOCK_ID", "PREVIEW_TEXT", "ACTIVE", "PROPERTY_OBJECT", "PROPERTY_DIGNITY", "PROPERTY_FAULT"]);
                while ($arComment = $dbComments->GetNext()) {
                    $arProductWithComment[] = $arComment["PROPERTY_OBJECT_VALUE"];

                    if ($arComment["ACTIVE"] != "Y") {
                        $arUnpublishedComment[] = $arComment;
                    } else {
                        $arPublishedComment[] = $arComment;
                    }
                }

                $arComments = CIBlockElement::GetList([], ["IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"], "ID" => $arProductWithComment], false, false, ["ID", "IBLOCK", "DETAIL_PAGE_URL", "NAME"]);
                while ($ob = $arComments->GetNext()) {
                    $infoComments[] = $ob;
                }

                foreach ($arUnpublishedComment as $key => $arComment) {
                    foreach ($infoComments as $arCommentInfo) {
                        if($arCommentInfo["ID"] == $arComment["PROPERTY_OBJECT_VALUE"]) {
                            $arUnpublishedComment[$key]["NAME"] = $arCommentInfo["NAME"];
                        }
                    }
                }

                foreach ($arPublishedComment as $key => $arComment) {
                    foreach ($infoComments as $arCommentInfo) {
                        if($arCommentInfo["ID"] == $arComment["PROPERTY_OBJECT_VALUE"]) {
                            $arPublishedComment[$key]["DETAIL_PAGE_URL"] = $arCommentInfo["DETAIL_PAGE_URL"];
                            $arPublishedComment[$key]["NAME"] = $arCommentInfo["NAME"];
                        }
                    }
                }

                $arProductWithoutComment = array_diff($uniqueUserProducts, $arProductWithComment);

                $this->arResult['PRODUCTS_WITHOUT_COMMENT_ID'] = false;

                if(!empty($infoComments)) {
                    $this->arResult['INFO_COMMENTS'] = $infoComments;
                }

                if(!empty($arProductWithoutComment)) {
                    $this->arResult['PRODUCTS_WITHOUT_COMMENT_ID'] = $arProductWithoutComment;
                }

                if(!empty($arUnpublishedComment)) {
                    $this->arResult['UNPUBLISHED_COMMENTS'] = $arUnpublishedComment;
                }

                if(!empty($arPublishedComment)) {
                    $this->arResult['PUBLISHED_COMMENTS'] = $arPublishedComment;
                }

                if(!empty($basketItems)) {
                    $this->arResult['BASKET_ITEMS'] = $basketItems;
                }

                $this->includeComponentTemplate();
            }
        }
    }