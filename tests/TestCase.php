<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PlacetoPay\Atropos\Logger\TestLogger;
use Placetopay\CamaraComercioBogotaSdk\Gateway;
use PlacetoPay\Tangram\Exceptions\InvalidSettingException;

class TestCase extends PHPUnitTestCase
{
    protected TestLogger $logger;

    /**
     * @throws InvalidSettingException
     */
    public function createGateway(array $settings = []): Gateway
    {
        $this->logger = new TestLogger();

        return new Gateway(array_merge([
            'username' => 'username TEST',
            'password' => 'password TEST',
            'url' => 'https://testapi.camaracomercio.gov.co',
            'logger' => [
                'via' => $this->logger,
                'path' => 'fake/path/camara-comercio-bogota-sdk.log',
            ],
        ], $settings));
    }
}
