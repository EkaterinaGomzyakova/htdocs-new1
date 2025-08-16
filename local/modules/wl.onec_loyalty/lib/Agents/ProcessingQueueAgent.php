<?php

namespace WL\OnecLoyalty\Agents;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use WL\OnecLoyalty\Actions\QueueProcessingAction;

class ProcessingQueueAgent
{
    /**
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function execute(): string
    {
        (new QueueProcessingAction())->run();
        return '\WL\OnecLoyalty\Agents\ProcessingQueueAgent::execute();';
    }
}