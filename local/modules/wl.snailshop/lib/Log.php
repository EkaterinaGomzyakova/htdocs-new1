<?php

namespace WL;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Throwable;
use CAdminNotify;

class Log
{
    private const path = '/local/logs/';
    private static Log $_instance;
    private Logger $logger;

    private function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . self::path . 'wl_clanbeauty.log';

        $this->logger = new Logger('wl.clanbeauty');
        $this->logger->pushHandler(new RotatingFileHandler($path, 7));
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param Throwable $throwable
     *
     * @return void
     */
    public function exception(Throwable $throwable): void
    {
        $message = "Необработанное исключение (#{$throwable->getCode()}), файл {$throwable->getFile()}, строка {$throwable->getLine()}:\n\"{$throwable->getMessage()}\"";
        CAdminNotify::Add([
            'NOTIFY_TYPE'  => CAdminNotify::TYPE_ERROR,
            'MESSAGE'      => nl2br($message),
            'TAG'          => 'error' . md5($message),
            'ENABLE_CLOSE' => 'Y',
        ]);
        $this->logger->error(message: $throwable->getMessage(), context: $throwable->getTrace());
    }

    /**
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error(string $message, array $context): void
    {
        $this->logger->error(message: $message, context: $context);
    }
}