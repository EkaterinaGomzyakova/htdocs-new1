<?php
use Bitrix\Main\Loader;
$arClasses = array(
    "WL\SnailShop" => "lib/snailshop.php",
    'WL\HistoryPrice' => 'lib/HistoryPrice.php',
    'WL\HL' => 'lib/HL.php',
    'WL\Iblock' => 'lib/iblock.php',
    'WL\IblockUtils' => 'lib/IblockUtils.php',
    'WL\Log' => 'lib/Log.php',

    'SnailShop\\Controller\\Price' => 'lib/controllers/Price.php',
    'SnailShop\\Controller\\Goods' => 'lib/controllers/Goods.php',
    'SnailShop\\Controller\\GenerateCertificate' => 'lib/controllers/generatecertificate.php',
);

Loader::registerAutoLoadClasses("wl.snailshop", $arClasses);