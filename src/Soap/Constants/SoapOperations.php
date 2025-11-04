<?php

namespace DummyNamespace\Soap\Constants;

use PlacetoPay\Base\Constants\Operations;

class SoapOperations
{
    public const AUTHENTICATION = 'AuthenticationOperationName';
    public const SALE = 'SaleOperationName';

    public const OPERATIONS = [
        Operations::AUTHENTICATION => Operations::AUTHENTICATION . '@' . self::AUTHENTICATION,
        Operations::SALE => Operations::SALE . '@' . self::SALE,
    ];
}
