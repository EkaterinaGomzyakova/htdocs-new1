<?php

namespace WL\OnecLoyalty\Enums;

enum MethodEnum: string
{
    case getclientbonuses = 'getclientbonuses';
    case ping = 'ping';
    case unlockclientbonuses3_0 = 'unlockclientbonuses3_0';
    case accrueclientbonuses = 'accrueclientbonuses';
    case writeoffclientbonuses3_0 = 'writeoffclientbonuses3_0';

    /**
     * @return string
     */
    public function getHttpMethod(): string
    {
        return match ($this) {
            self::getclientbonuses, self::ping => 'GET',
            self::unlockclientbonuses3_0, self::accrueclientbonuses, self::writeoffclientbonuses3_0 => 'POST',
            default => 'GET'
        };
    }
}
