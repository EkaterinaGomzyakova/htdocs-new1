<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use WL\OnecLoyalty\Actions\LoadAction;

Loader::requireModule('wl.onec_loyalty');

$arResult['loyalty'] = (new LoadAction())->run(
    userId  : CurrentUser::get()->getId(),
    currency: 'RUB'
);