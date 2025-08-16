<?php

namespace WL\OnecLoyalty\Tools;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Throwable;

class Log
{
    private const path = '/local/logs/';
    private static Log $_instance;
    private Logger $logger;

    private function __construct()
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . self::path . 'wl_onec_loyalty.log';

        $this->logger = new Logger('wl.onec_loyalty');
        $this->logger->pushHandler(new RotatingFileHandler($path, 7));
    }

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