<?php

namespace Placetopay\CamaraComercioBogotaSdk\Constants;

class Statuses
{
    public const SUCCESS = '00';

    public static function isSuccessful(string $status): bool
    {
        return $status === self::SUCCESS;
    }
}
