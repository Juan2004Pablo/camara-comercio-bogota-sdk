<?php

namespace DummyNamespace\Soap\Support;

use DummyNamespace\Soap\Simulators\SimulatorFactory;
use PlacetoPay\Base\Constants\Operations;
use Placetopay\SoapClient\Client;
use Placetopay\SoapClient\Contracts\ClientContract;
use Placetopay\SoapClient\Handlers\MockHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsResolver extends OptionsResolver
{
    public static function create(array $settings): OptionsResolver
    {
        $resolver = new self();

        $resolver->defineProviderName();
        $resolver->defineSimulatorMode();
        $resolver->defineWsdl();
        $resolver->defineClientOptions();
        $resolver->defineClient();
        $resolver->defineLocations();

        $resolver->setRequired(isset($settings['client']) ? 'client' : 'wsdl');

        if (isset($settings['logger'])) {
            $resolver->defineLogger();
        }

        return $resolver;
    }

    protected function defineProviderName(): void
    {
        $this->define('providerName')
            ->allowedTypes('string')
            ->default('Dummy');
    }

    protected function defineSimulatorMode(): void
    {
        $this->define('simulatorMode')
            ->allowedTypes('bool')
            ->default(false);
    }

    protected function defineLogger(): void
    {
        $this->define('logger')->default(function (OptionsResolver $loggerResolver) {
            $loggerResolver->define('name')->allowedTypes('string');
            $loggerResolver->define('via')->required()->allowedTypes(LoggerInterface::class);
            $loggerResolver->define('path')->allowedTypes('string', 'null');
            $loggerResolver->define('debug')->allowedTypes('bool')->default(false);
        });
    }

    protected function defineWsdl()
    {
        $this->define('wsdl')
            ->allowedTypes('string', 'string[]')
            ->default([
                Operations::AUTHENTICATION => 'AuthenticationWsdlPath',
            ]);
    }

    protected function defineClientOptions(): void
    {
        $this->define('clientOptions')
            ->allowedTypes('array')
            ->default(function (OptionsResolver $clientOptionsResolver) {
                $clientOptionsResolver->define('soapVersion')->allowedTypes('int')->default(SOAP_1_1);
                $clientOptionsResolver->define('features')->allowedTypes('int')->default(SOAP_SINGLE_ELEMENT_ARRAYS);
                $clientOptionsResolver->define('cache_wsdl')->allowedTypes('int')->default(WSDL_CACHE_NONE);
                $clientOptionsResolver->define('trace')->allowedTypes('bool')->default(true);
                $clientOptionsResolver->define('encoding')->allowedTypes('string')->default('UTF-8');
                $clientOptionsResolver->define('connection_timeout')->allowedTypes('int')->default(30);
            });
    }

    protected function defineLocations()
    {
        $this->define('locations')
            ->allowedTypes('string', 'string[]')
            ->default([
                Operations::AUTHENTICATION => 'AuthenticationLocation',
            ]);
    }

    protected function defineClient(): void
    {
        $this->define('client')
            ->allowedTypes(ClientContract::class)
            ->default(function (Options $options) {
                $clientSettings = [
                    'options' => $options['clientOptions'],
                    'wsdl' => $options['wsdl'],
                ];

                if ($options['simulatorMode']) {
                    $clientSettings['handler'] = new MockHandler();
                    $clientSettings['options']['factories']['response'] = new SimulatorFactory();

                    if ($options['logger']['debug'] ?? false) {
                        $clientSettings['logger'] = $options['logger']['via'];
                    }
                }

                return new Client($clientSettings);
            });
    }
}
