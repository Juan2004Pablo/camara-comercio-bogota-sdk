<?php

namespace DummyNamespace\Soap;

use Placetopay\CamaraComercioBogotaSdk\Exceptions\CamaraComercioBogotaSdkException;
use Placetopay\CamaraComercioBogotaSdk\Support\ParserManager;
use DummyNamespace\Soap\Entities\Settings;
use DummyNamespace\Soap\Support\SettingsResolver;
use PlacetoPay\Base\Constants\Operations;
use PlacetoPay\Base\Messages\FinancialTransaction;
use PlacetoPay\Base\Messages\Transaction;
use PlacetoPay\Tangram\Carriers\SoapCarrier;
use PlacetoPay\Tangram\Events\Dispatcher;
use PlacetoPay\Tangram\Exceptions\InvalidSettingException;
use PlacetoPay\Tangram\Listeners\SoapLoggerListener;
use PlacetoPay\Tangram\Services\BaseGateway;
use Throwable;

class Gateway extends BaseGateway
{
    protected Settings $settings;
    protected SoapCarrier $carrier;
    protected ParserManager $parserManager;

    /**
     * @throws InvalidSettingException
     */
    public function __construct(array $settings)
    {
        $this->settings = new Settings($settings, SettingsResolver::create($settings));
        $this->carrier = new SoapCarrier($this->settings->client());
        $this->parserManager = new ParserManager($this->settings);

        $this->addEventDispatcher();
    }

    public function settings(): Settings
    {
        return $this->settings;
    }

    /**
     * @throws InvalidSettingException
     */
    protected function addEventDispatcher(): void
    {
        if ($loggerSettings = $this->settings->loggerSettings()) {
            $this->setLoggerContext($loggerSettings);

            $listener = new SoapLoggerListener(
                $loggerSettings,
                $this->settings->providerName(),
                $this->settings->simulatorMode()
            );

            $this->carrier->setDispatcher(Dispatcher::create($listener->getEventsMethodsToDispatcher()));
        }
    }

    protected function setLoggerContext(&$loggerSettings): void
    {
        $loggerSettings['context'] = [
            '*' => [
                'settings' => [
                    'data.account' => 'secured',
                ],
            ],
        ];
    }

    protected function authorizeTransaction(FinancialTransaction $transaction): FinancialTransaction
    {
        return $this->process($transaction, Operations::SALE);
    }

    /**
     * @throws CamaraComercioBogotaSdkException
     */
    private function process(Transaction $transaction, string $operation): Transaction
    {
        try {
            $parser = $this->parserManager->getParser($operation);
            $carrierDataObject = $this->parseRequest($operation, $transaction, $parser);
            $this->carrier->request($carrierDataObject);
            $this->parseResponse($carrierDataObject, $parser);

            return $transaction;
        } catch (Throwable $exception) {
            throw new CamaraComercioBogotaSdkException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
