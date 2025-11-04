<?php

namespace DummyNamespace\Soap\Constants;

class Statuses
{
    public const SUCCESS = '00';

    public static function isSuccessful(string $status): bool
    {
        return $status === self::SUCCESS;
    }
}
