<?php

namespace DummyNamespace\Soap\Entities;

use Placetopay\CamaraComercioBogotaSdk\Exceptions\CamaraComercioBogotaSdkException;
use Placetopay\SoapClient\Contracts\ClientContract;
use PlacetoPay\Tangram\Entities\BaseSettings;

class Settings extends BaseSettings
{
    public function providerName(): string
    {
        return $this->get('providerName');
    }

    public function timeout(): int
    {
        return $this->get('clientOptions')['timeout'];
    }

    public function locations(): array
    {
        return $this->get('locations');
    }

    /**
     * @throws CamaraComercioBogotaSdkException
     */
    public function getLocation(string $operation): string
    {
        if (!isset($this->locations()[$operation])) {
            throw new CamaraComercioBogotaSdkException("This operation doesn't have location");
        }

        return $this->locations()[$operation];
    }

    public function simulatorMode(): bool
    {
        return $this->get('simulatorMode');
    }

    public function loggerSettings(): array
    {
        return $this->get('logger');
    }

    public function client(): ClientContract
    {
        return $this->get('client');
    }
}
