<?php

namespace Placetopay\CamaraComercioBogotaSdk\Parsers;

use PlacetoPay\Base\Messages\Transaction;
use PlacetoPay\Tangram\Contracts\CarrierDataObjectContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;

class FinancialParser implements ParserHandlerContract
{
    public function parserRequest(CarrierDataObjectContract $carrierDataObject): array
    {
        // TODO: Implement parserRequest() method.
    }

    public function parserResponse(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        // TODO: Implement parserResponse() method.
    }

    public function errorHandler(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        // TODO: Implement errorHandler() method.
    }
}
