<?php

use Bitrix\Main\Loader;
Loader::includeModule('sale');
Loader::includeModule('wl.snailshop');
Loader::includeModule('wl.delivery_area');
Loader::includeModule('shestpa.lastmodified');

// подключаемые файлы
$requireFiles = array_map(
    static fn($file) => '/local/php_interface/include/' . $file . '.php',
    [
        'const', 'CatalogActionDiscount', 'fillBarcodeProperty', 'paymentRestrictions',
	    'discountRestrictions', 'adminAdditionalMenus', 'offDiscountsForNonCatalogProducts',
	    'fixShipmentQuantity', 'catalogElementsHandlers', 'fixArticlesSpecialChars', 'searchTweaks',
	    'seoFunctions', 'eventsLog', 'restrictMoreThanOneCoupon', 'onSdekSend', 'dump', 'shutdown', 'Unisender'
    ]
);
array_unshift($requireFiles, '/local/vendor/autoload.php');
foreach ($requireFiles as $requireFile) {
	if (file_exists($_SERVER["DOCUMENT_ROOT"] . $requireFile)) {
		require_once $_SERVER['DOCUMENT_ROOT'] . $requireFile;
	}
}
require_once 'autoload.php';


// подписки на события
$eventManager = Bitrix\Main\EventManager::getInstance();

// модуль main
$eventManager->addEventHandler('main', 'OnAfterUserAdd', [WL\UserHandlers::class, 'OnAfterUserAdd']);
$eventManager->addEventHandler('main', 'OnBeforeUserRegister', [WL\UserHandlers::class, 'OnBeforeUserRegister']);
$eventManager->addEventHandler('main', 'OnAfterUserRegister', [WL\UserHandlers::class, 'OnAfterUserRegister']);
$eventManager->addEventHandler('main', 'OnAdminContextMenuShow', [WL\MainHandlers::class, 'OnAdminContextMenuShow']);
$eventManager->addEventHandler('main', 'OnBeforeProlog', [WL\Handlers\Main::class, 'OnBeforeProlog']);
$eventManager->addEventHandler('main', 'OnBeforeUserUpdate', [WL\Handlers\Main::class, 'OnBeforeUserUpdateHandler']);
$eventManager->addEventHandler("main", "OnBeforeEventAdd", [WL\Handlers\Catalog::class, 'sendOnSubscribeSubmit']);

// модуль iblock
$eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', [Clanbeauty\Properties\BasketRule::class, 'GetUserTypeDescription']);
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', [Clanbeauty\IblockHandlers::class, 'PreventImageDeletion']);
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', [Clanbeauty\IblockHandlers::class, 'OnBeforeIBlockElementUpdate']);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', [Clanbeauty\IblockHandlers::class, 'OnAfterIBlockElementUpdate']);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', [Clanbeauty\IblockHandlers::class, 'OnAfterIBlockElementAdd']);
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockSectionUpdate', [Clanbeauty\IblockHandlers::class, 'OnBeforeIBlockSectionUpdate']);

// модуль sale
$eventManager->addEventHandler('sale', 'OnSaleOrderPaid', [WL\Handlers\Sale::class, 'OnSaleOrderPaid']);
$eventManager->addEventHandler('sale', 'OnSaleStatusOrder', [WL\Handlers\Sale::class, 'OnSaleStatusOrder']);
$eventManager->addEventHandler('sale', 'OnSaleOrderBeforeSaved', [WL\Handlers\Sale::class, 'OnSaleOrderBeforeSaved']);
$eventManager->addEventHandler('sale', 'OnSaleBeforeOrderDelete', [WL\Handlers\Sale::class, 'OnSaleBeforeOrderDelete']);
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', [WL\Handlers\Sale::class, 'OnSaleOrderSaved']);
$eventManager->addEventHandler('sale', 'OnOrderAdd', [WL\Handlers\Sale::class, 'OnOrderAddHandler']);

// модуль catalog
$eventManager->addEventHandler('catalog', 'Bitrix\Catalog\Price::OnAfterAdd', [WL\Handlers\Catalog::class, 'onPriceUpdate']);
$eventManager->addEventHandler('catalog', 'Bitrix\Catalog\Price::OnAfterUpdate', [WL\Handlers\Catalog::class, 'onPriceUpdate']);
$eventManager->addEventHandler('catalog', 'Bitrix\Catalog\Model\Product::OnAfterAdd', [WL\Handlers\Catalog::class, 'onProductUpdate']);
$eventManager->addEventHandler('catalog', 'Bitrix\Catalog\Model\Product::OnAfterUpdate', [WL\Handlers\Catalog::class, 'onProductUpdate']);
$eventManager->addEventHandler('catalog', 'OnBeforeGroupUpdate', [WL\Handlers\Catalog::class, 'StopPriceRename']);