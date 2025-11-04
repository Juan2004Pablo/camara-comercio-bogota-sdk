<?php

namespace DummyNamespace\Soap\Simulators;

use DummyNamespace\Soap\Constants\SoapOperations;
use DummyNamespace\Soap\Simulators\Behaviours\AuthorizationBehaviour;
use Placetopay\SoapClient\Response;

class SimulatorFactory
{
    protected const BEHAVIOURS = [
        SoapOperations::SALE => AuthorizationBehaviour::class,
    ];

    public function __invoke(string $wsdl, string $operation, array $request, array $options = []): Response
    {
        $behaviour = self::BEHAVIOURS[$operation] ?? null;

        if (!$behaviour) {
            return new Response(['There is no behavior']);
        }

        return $behaviour::create()->resolve($request);
    }
}
