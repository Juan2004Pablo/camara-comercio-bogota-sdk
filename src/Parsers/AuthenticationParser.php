<?php

namespace Placetopay\CamaraComercioBogotaSdk\Parsers;

use PlacetoPay\Base\Messages\AdministrativeTransaction;
use Placetopay\CamaraComercioBogotaSdk\Exceptions\AuthenticationException;
use PlacetoPay\Tangram\Contracts\CarrierDataObjectContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;

class AuthenticationParser implements ParserHandlerContract
{
    public function parserRequest(CarrierDataObjectContract $carrierDataObject): array
    {
        //
    }

    /**
     * @throws AuthenticationException
     */
    public function parserResponse(CarrierDataObjectContract $carrierDataObject): AdministrativeTransaction
    {
        try {
            $response = $carrierDataObject->response();

            /** @var AdministrativeTransaction $transaction */
            $transaction = $carrierDataObject->transaction();

            return $transaction->mergeAdditional([
                'token' => '',
                'expiredAt' => '',
            ]);
        } catch (\Throwable $exception) {
            throw new AuthenticationException('');
        }
    }

    /**
     * @throws AuthenticationException
     */
    public function errorHandler(CarrierDataObjectContract $carrierDataObject): AdministrativeTransaction
    {
        $exception = $carrierDataObject->error();

        throw new AuthenticationException($exception->getMessage(), $exception->getCode(), $exception);
    }
}
