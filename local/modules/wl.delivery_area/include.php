<?php
use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
    "wl.delivery_area", [
    "WLDeliveryArea\Handlers" => "lib/handlers.php",
    "WLDeliveryArea\Controller\Delivery" => "lib/controllers/Delivery.php",
]);

include 'handlers.php';