<?php

namespace Placetopay\CamaraComercioBogotaSdk\Support;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Placetopay\CamaraComercioBogotaSdk\Simulators\ClientSimulator;
use PlacetoPay\Tangram\Entities\Cache;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsResolver extends OptionsResolver
{
    public static function create(array $settings): OptionsResolver
    {
        $resolver = new self();

        $resolver->defineProviderName();
        $resolver->defineUrl();
        $resolver->defineClient();
        $resolver->defineCache();
        $resolver->defineSimulatorMode();

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

    protected function defineUrl(): void
    {
        $this->define('url')
            ->allowedTypes('string')
            ->normalize(fn (Options $options, $value) => self::normalizeUrl($value));
    }

    protected function defineClient(): void
    {
        $this->define('client')
            ->allowedTypes(ClientInterface::class)
            ->default(function (Options $options) {
                $settings = [
                    'base_uri' => $options['url'] ?? null,
                    'headers' => [
                        'User-Agent' => 'PlacetopayConnector',
                    ],
                ];

                return $options['simulatorMode'] ? new ClientSimulator($settings) : new Client($settings);
            });
    }

    protected function defineCache(): void
    {
        $this->define('cache')
            ->allowedTypes(CacheInterface::class)
            ->default(new Cache());
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

    private static function normalizeUrl(string $value): string
    {
        return str_ends_with($value, '/') ? $value : $value . '/';
    }
}
