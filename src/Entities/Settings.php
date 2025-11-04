<?php

namespace Placetopay\CamaraComercioBogotaSdk\Entities;

use GuzzleHttp\ClientInterface;
use PlacetoPay\Tangram\Entities\BaseSettings;
use Psr\SimpleCache\CacheInterface;

class Settings extends BaseSettings
{
    public function providerName(): string
    {
        return $this->get('providerName');
    }
    public function url(): string
    {
        return $this->get('url');
    }

    public function client(): ClientInterface
    {
        return $this->get('client');
    }

    public function cache(): CacheInterface
    {
        return $this->get('cache');
    }

    public function simulatorMode(): bool
    {
        return $this->get('simulatorMode');
    }

    public function loggerSettings(): ?array
    {
        return $this->get('logger');
    }
}
