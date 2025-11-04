<?php

namespace DummyNamespace\Soap\Parsers;

use DummyNamespace\Soap\Constants\SoapOperations;
use DummyNamespace\Soap\Entities\Settings;
use PlacetoPay\Base\Constants\ReasonCodes;
use PlacetoPay\Base\Entities\Status;
use PlacetoPay\Base\Messages\Transaction;
use Placetopay\CamaraComercioBogotaSdk\Exceptions\CamaraComercioBogotaSdkException;
use PlacetoPay\Tangram\Contracts\CarrierDataObjectContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;

abstract class CamaraComercioBogotaSdkParser implements ParserHandlerContract
{
    protected Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function parserRequest(CarrierDataObjectContract $carrierDataObject): array
    {
        $operation = $carrierDataObject->operation();
        $carrierDataObject->setOptions(array_merge([
            'operation' => SoapOperations::OPERATIONS[$operation],
            'soapOptions' => [
                'location' => $this->settings->getLocation($operation),
            ],
        ], $carrierDataObject->options()));

        return $this->getRequest($carrierDataObject);
    }

    public function parserResponse(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        $transaction = $carrierDataObject->transaction();

        try {
            $response = $carrierDataObject->response()->getData();
            $this->checkResponse($response);
            $this->setResponseData($transaction, $response);
            $transaction
                ->setStatus(Status::quickOk(ReasonCodes::APPROVED_TRANSACTION))
                ->status()->setMessage($response['']);
        } catch (CamaraComercioBogotaSdkException $exception) {
            $transaction->setStatus(Status::quick(
                Status::ST_FAILED,
                ReasonCodes::GENERAL_REJECTION,
                $exception->getMessage()
            ));
        }

        return $transaction;
    }

    public function errorHandler(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        return $carrierDataObject
            ->transaction()
            ->setStatus(Status::quickFailed(
                $carrierDataObject->error()->getCode() === 0
                    ? ReasonCodes::BAD_REQUEST
                    : ReasonCodes::GENERAL_REJECTION,
                $carrierDataObject->error()->getMessage()
            ));
    }

    abstract protected function getRequest(CarrierDataObjectContract $carrierDataObject): array;

    abstract protected function checkResponse(array $response): void;

    abstract protected function setResponseData(Transaction $transaction, array $response): void;
}
