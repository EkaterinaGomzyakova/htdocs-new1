<?php

namespace WL\OnecLoyalty\Controller;

use Bitrix\Main\Engine\Controller;
use Throwable;
use WL\OnecLoyalty\Tools\Log;

abstract class BaseController extends Controller
{
    /**
     * @param Throwable $throwable
     *
     * @return void
     */
    protected function runProcessingThrowable(Throwable $throwable): void
    {
        Log::getInstance()->error($throwable->getMessage(), $throwable->getTrace());
    }
}