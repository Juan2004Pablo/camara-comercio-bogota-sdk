<?php

namespace Placetopay\CamaraComercioBogotaSdk;

use Placetopay\CamaraComercioBogotaSdk\Entities\Settings;
use Placetopay\CamaraComercioBogotaSdk\Support\AuthenticationManager;
use Placetopay\CamaraComercioBogotaSdk\Support\ParserManager;
use Placetopay\CamaraComercioBogotaSdk\Support\SettingsResolver;
use PlacetoPay\Tangram\Carriers\RestCarrier;
use PlacetoPay\Tangram\Events\Dispatcher;
use PlacetoPay\Tangram\Exceptions\InvalidSettingException;
use PlacetoPay\Tangram\Listeners\HttpLoggerListener;
use PlacetoPay\Tangram\Services\BaseGateway;

class Gateway extends BaseGateway
{
    protected Settings $settings;
    protected RestCarrier $carrier;
    protected AuthenticationManager $authenticationManager;
    protected ParserManager $parserManager;

    /**
     * @throws InvalidSettingException
     */
    public function __construct(array $settings)
    {
        $this->settings = new Settings($settings, SettingsResolver::create($settings));
        $this->carrier = new RestCarrier($this->settings->client());
        $this->authenticationManager = new AuthenticationManager($this->settings, $this->carrier);
        $this->parserManager = new ParserManager($this->settings);

        $this->addEventDispatcher();
    }

    // TODO: Add operations implementing available contracts

    public function settings(): Settings
    {
        return $this->settings;
    }

    /**
     * @throws InvalidSettingException
     */
    protected function addEventDispatcher(): void
    {
        // Add Events Required
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
