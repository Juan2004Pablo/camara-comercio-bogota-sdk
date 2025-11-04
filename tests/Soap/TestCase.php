<?php

namespace Tests\Soap;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Placetopay\CamaraComercioBogotaSdk\Gateway;
use PlacetoPay\Tangram\Mock\TestLogger;

class TestCase extends PHPUnitTestCase
{
    protected TestLogger $logger;

    public function createGateway(array $settings = [], bool $mockClient = true): Gateway
    {
        $this->logger = new TestLogger();

        return new Gateway(array_merge([
            'simulatorMode' => $mockClient,
            'logger' => [
                'via' => $this->logger,
                'path' => 'fake/path/camara-comercio-bogota-sdk.log',
            ],
        ], $settings));
    }
}
