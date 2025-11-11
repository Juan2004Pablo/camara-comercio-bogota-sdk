<?php

namespace Placetopay\CamaraComercioBogotaSdk\Constants;

class Endpoints
{
    public const CONSULT_INFORMATION = '/API/Consultar/InformacionMercantil';

    protected const OPERATIONS = [
        AdditionalOperations::CONSULT_INFORMATION => self::CONSULT_INFORMATION,
    ];

    public static function getOperationEndpoint(string $operation): ?string
    {
        return self::OPERATIONS[$operation] ?? null;
    }
}
