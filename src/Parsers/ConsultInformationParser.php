<?php

namespace Placetopay\CamaraComercioBogotaSdk\Parsers;

use PlacetoPay\Base\Constants\ReasonCodes;
use PlacetoPay\Base\Entities\Status;
use PlacetoPay\Base\Messages\Transaction;
use Placetopay\CamaraComercioBogotaSdk\Constants\Endpoints;
use Placetopay\CamaraComercioBogotaSdk\Entities\ConsultInformationTransaction;
use Placetopay\CamaraComercioBogotaSdk\Entities\Settings;
use Placetopay\CamaraComercioBogotaSdk\Support\CompanyDataTransformer;
use Placetopay\CamaraComercioBogotaSdk\Support\DocumentTypeMapper;
use PlacetoPay\Tangram\Contracts\CarrierDataObjectContract;
use PlacetoPay\Tangram\Contracts\ParserHandlerContract;

class ConsultInformationParser implements ParserHandlerContract
{
    protected Settings $settings;
    protected CompanyDataTransformer $transformer;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->transformer = new CompanyDataTransformer();
    }

    public function parserRequest(CarrierDataObjectContract $carrierDataObject): array
    {
        /** @var ConsultInformationTransaction $transaction */
        $transaction = $carrierDataObject->transaction();
        $endpoint = Endpoints::getOperationEndpoint($carrierDataObject->operation());

        $carrierDataObject->setOptions(array_merge([
            'method' => 'POST',
            'endpoint' => $endpoint,
        ], $carrierDataObject->options()));

        $documentType = DocumentTypeMapper::toApiFormat($transaction->person()->documentType());

        if ($documentType === null) {
            throw new \InvalidArgumentException(
                sprintf('Invalid document type: %s', $transaction->person()->documentType())
            );
        }

        return [
            'headers' => [],
            'json' => [
                'TipoIdentificacion' => $documentType,
                'Identificacion' => $transaction->person()->document(),
                'IdCamara' => '',
                'Matricula' => '',
                'UsuarioServicioWeb' => $this->settings->username(),
                'IdLlaveServicio' => $this->settings->password(),
            ],
        ];
    }

    public function parserResponse(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        /** @var ConsultInformationTransaction $transaction */
        $transaction = $carrierDataObject->transaction();
        $responseContent = $carrierDataObject->response()->getBody()->getContents();
        $responseData = json_decode($responseContent, true);

        if (!is_array($responseData) || !isset($responseData['transaction'])) {
            $transaction->setStatus(Status::quickFailed(
                ReasonCodes::INVALID_RESPONSE,
                'Invalid response format'
            ));
            return $transaction;
        }

        $transactionData = $responseData['transaction'];
        $statusData = $transactionData['status'] ?? [];

        // Set transaction status
        if (($statusData['successful'] ?? false) === true) {
            $transaction->setStatus(Status::quickOk(
                ReasonCodes::APPROVED_TRANSACTION,
                'Operation successfully!'
            ));

            // Transform and filter company data
            $companyData = $this->transformer->transform($transactionData['company'] ?? null);
            if ($companyData) {
                $transaction->setCompany($companyData);
            }
        } else {
            $transaction->setStatus(Status::quickFailed(
                ReasonCodes::INVALID_RESPONSE,
                $statusData['message'] ?? 'Query error'
            ));
        }

        return $transaction;
    }

    public function errorHandler(CarrierDataObjectContract $carrierDataObject): Transaction
    {
        $carrierDataObject->transaction()->setStatus(Status::quickFailed(
            ReasonCodes::INVALID_RESPONSE,
            $carrierDataObject->error()->getMessage()
        ));

        return $carrierDataObject->transaction();
    }
}
