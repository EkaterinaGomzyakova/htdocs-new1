<?php

use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler(
    'sale',
    'onSaleDeliveryHandlersClassNamesBuildList',
    ['\WLDeliveryArea\Handlers', 'onSaleDeliveryHandlersClassNamesBuildList']
);