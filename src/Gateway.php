<?php

namespace Placetopay\CamaraComercioBogotaSdk;

use PlacetoPay\Base\Messages\AdministrativeTransaction;
use PlacetoPay\Base\Messages\Transaction;
use Placetopay\CamaraComercioBogotaSdk\Constants\AdditionalOperations;
use Placetopay\CamaraComercioBogotaSdk\Entities\ConsultInformationTransaction;
use Placetopay\CamaraComercioBogotaSdk\Entities\Settings;
use Placetopay\CamaraComercioBogotaSdk\Exceptions\CamaraComercioBogotaSdkException;
use Placetopay\CamaraComercioBogotaSdk\Support\ParserManager;
use Placetopay\CamaraComercioBogotaSdk\Support\SettingsResolver;
use PlacetoPay\Tangram\Carriers\RestCarrier;
use PlacetoPay\Tangram\Events\Dispatcher;
use PlacetoPay\Tangram\Exceptions\InvalidSettingException;
use PlacetoPay\Tangram\Listeners\HttpLoggerListener;
use PlacetoPay\Tangram\Services\BaseGateway;
use Throwable;

class Gateway extends BaseGateway
{
    protected Settings $settings;
    protected RestCarrier $carrier;
    protected ParserManager $parserManager;

    /**
     * @throws InvalidSettingException
     */
    public function __construct(array $settings)
    {
        $this->settings = new Settings($settings, SettingsResolver::create($settings));
        $this->carrier = new RestCarrier($this->settings->client());
        $this->parserManager = new ParserManager($this->settings);

        $this->addEventDispatcher();
    }

    public function consultInformation(ConsultInformationTransaction $transaction): AdministrativeTransaction
    {
        $this->process(AdditionalOperations::CONSULT_INFORMATION, $transaction);

        return $transaction;
    }

    /**
     * @throws CamaraComercioBogotaSdkException
     */
    private function process(string $operation, Transaction $transaction): void
    {
        try {
            $parser = $this->parserManager->getParser($operation);
            $carrierDataObjet = $this->parseRequest($operation, $transaction, $parser);
            $this->carrier->request($carrierDataObjet);
            $this->parseResponse($carrierDataObjet, $parser);

            return;
        } catch (Throwable $e) {
            throw new CamaraComercioBogotaSdkException($e->getMessage(), $e->getCode(), $e);
        }
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

            $listener = new HttpLoggerListener(
                $loggerSettings,
                $this->settings->providerName(),
                $this->settings->simulatorMode()
            );

            $this->carrier->setDispatcher(Dispatcher::create($listener->getEventsMethodsToDispatcher()));
        }
    }

    protected function setLoggerContext(&$loggerSettings): void
    {
        // Add masking rules for logs
        $loggerSettings['context'] = [
            'request' => [],
        ];
    }
}
