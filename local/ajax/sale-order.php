<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
\Bitrix\Main\Loader::includeModule('sale');
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if (!empty($request->get('action'))) {
    switch ($request->get('action')) {
        case 'get_reccomend_items':
            $APPLICATION->RestartBuffer();
            $basketItemsID = $request->get('items_id');
            $basketItems = \Bitrix\Sale\Basket::getList([
                'filter' => ['ID' => $basketItemsID],
                'select' => ['ID', 'PRODUCT_ID']
            ])->fetchAll();

            $products = [];
            $filter = ['IBLOCK_ID' => GOODS_IBLOCK_ID, 'ID' => array_column($basketItems, 'PRODUCT_ID')];
            $select = ['ID', 'PROPERTY_EXPANDABLES'];
            $rows = CIBlockElement::GetList([], $filter, false, false, $select);
            while ($row = $rows->fetch()) {
                $products[$row['ID']] = $row;
            }
            die();
            break;
        case 'confirmConsentProcessingPersonalData':
            $APPLICATION->RestartBuffer();
            if (empty($request['USER_ID'])) {
                throw new Exception('Пользователь не задан');
            }
            $user = \Bitrix\Main\UserTable::getList([
                'filter' => ['ID' => $request['USER_ID']],
                'select' => ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'PERSONAL_PHONE']
            ])->fetch();
            if (empty($user)) {
                throw new Exception('Пользователь не найден');
            }
            $cuser = new CUser();
            $cuser->Update($user['ID'], ['UF_CONSENT_PROCESSING' => 1]);
            echo \Bitrix\Main\Web\Json::encode(['success' => true]);
            die();
            break;
    }
}