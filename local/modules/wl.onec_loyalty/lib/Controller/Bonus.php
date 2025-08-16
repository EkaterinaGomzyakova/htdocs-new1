<?php

namespace WL\OnecLoyalty\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\SystemException;
use WL\OnecLoyalty\Actions\LoadAction;

class Bonus extends BaseController
{
    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [
            /** @see static::loadAction() */
            'load' => [],
        ];
    }

    /**
     * Загрузка данных
     *
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public function loadAction(): array
    {
        return (new LoadAction())->run(userId: CurrentUser::get()->getId());
    }
}