<?php
Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    'WL\Order' => '/local/php_interface/include/Order.php',
    'WL\Unisender' => '/local/php_interface/include/Unisender.php',
    'WL\UserHandlers' => '/local/php_interface/include/UserHandlers.php',
    'WL\MainHandlers' => '/local/php_interface/include/MainHandlers.php',
    'WL\Handlers\Catalog' => '/local/php_interface/include/handlers/catalog.php',
    'WL\Handlers\Sale' => '/local/php_interface/include/handlers/sale.php',
    'WL\Handlers\Main' => '/local/php_interface/include/handlers/main.php',
    'WL\Agents' => '/local/php_interface/include/Agents.php',
    'WL\WishList' => '/local/php_interface/classes/WishList.php',
    'WL\Basket' => '/local/php_interface/include/Basket.php',
    'Clanbeauty\Tools' => '/local/php_interface/classes/Tools.php',
    'Clanbeauty\Properties\BasketRule' => '/local/php_interface/custom_properties/BasketRule.php',
    'Clanbeauty\IblockHandlers' => '/local/php_interface/classes/IblockHandlers.php',
    'Clanbeauty\CatalogHelpers' => '/local/php_interface/classes/CatalogHelpers.php',
]);